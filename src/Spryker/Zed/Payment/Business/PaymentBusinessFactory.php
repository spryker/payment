<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment\Business;

use Spryker\Client\Payment\PaymentClientInterface;
use Spryker\Service\Payment\PaymentServiceInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;
use Spryker\Zed\Payment\Business\Calculation\PaymentCalculator;
use Spryker\Zed\Payment\Business\Checkout\PaymentPluginExecutor;
use Spryker\Zed\Payment\Business\Disabler\PaymentMethodDisabler;
use Spryker\Zed\Payment\Business\Disabler\PaymentMethodDisablerInterface;
use Spryker\Zed\Payment\Business\Enabler\PaymentMethodEnabler;
use Spryker\Zed\Payment\Business\Enabler\PaymentMethodEnablerInterface;
use Spryker\Zed\Payment\Business\EventTriggerer\PaymentMessageOmsEventTriggerer;
use Spryker\Zed\Payment\Business\EventTriggerer\PaymentMessageOmsEventTriggererInterface;
use Spryker\Zed\Payment\Business\MessageEmitter\MessageEmitter;
use Spryker\Zed\Payment\Business\MessageEmitter\MessageEmitterInterface;
use Spryker\Zed\Payment\Business\Generator\PaymentMethodKeyGenerator;
use Spryker\Zed\Payment\Business\Generator\PaymentMethodKeyGeneratorInterface;
use Spryker\Zed\Payment\Business\Hook\OrderPostSaveHook;
use Spryker\Zed\Payment\Business\Hook\OrderPostSaveHookInterface;
use Spryker\Zed\Payment\Business\Mapper\PaymentMethodEventMapper;
use Spryker\Zed\Payment\Business\Mapper\PaymentMethodEventMapperInterface;
use Spryker\Zed\Payment\Business\Mapper\QuoteDataMapper;
use Spryker\Zed\Payment\Business\Mapper\QuoteDataMapperInterface;
use Spryker\Zed\Payment\Business\Method\PaymentMethodFinder;
use Spryker\Zed\Payment\Business\Method\PaymentMethodFinderInterface;
use Spryker\Zed\Payment\Business\Method\PaymentMethodReader;
use Spryker\Zed\Payment\Business\Method\PaymentMethodStoreRelationUpdater;
use Spryker\Zed\Payment\Business\Method\PaymentMethodStoreRelationUpdaterInterface;
use Spryker\Zed\Payment\Business\Method\PaymentMethodUpdater;
use Spryker\Zed\Payment\Business\Method\PaymentMethodUpdaterInterface;
use Spryker\Zed\Payment\Business\Method\PaymentMethodValidator;
use Spryker\Zed\Payment\Business\Method\PaymentMethodValidatorInterface;
use Spryker\Zed\Payment\Business\Order\SalesPaymentHydrator;
use Spryker\Zed\Payment\Business\Order\SalesPaymentReader;
use Spryker\Zed\Payment\Business\Order\SalesPaymentSaver;
use Spryker\Zed\Payment\Business\Provider\PaymentProviderReader;
use Spryker\Zed\Payment\Business\Provider\PaymentProviderReaderInterface;
use Spryker\Zed\Payment\Business\Writer\PaymentWriter;
use Spryker\Zed\Payment\Business\Writer\PaymentWriterInterface;
use Spryker\Zed\Payment\Dependency\Facade\PaymentToLocaleFacadeInterface;
use Spryker\Zed\Payment\Dependency\Facade\PaymentToMessageBrokerBridge;
use Spryker\Zed\Payment\Dependency\Facade\PaymentToOmsFacadeInterface;
use Spryker\Zed\Payment\Dependency\Facade\PaymentToStoreFacadeInterface;
use Spryker\Zed\Payment\Dependency\Facade\PaymentToStoreReferenceFacadeInterface;
use Spryker\Zed\Payment\Dependency\Service\PaymentToUtilTextServiceInterface;
use Spryker\Zed\Payment\PaymentDependencyProvider;

/**
 * @method \Spryker\Zed\Payment\Persistence\PaymentQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\Payment\Persistence\PaymentEntityManagerInterface getEntityManager()
 * @method \Spryker\Zed\Payment\Persistence\PaymentRepositoryInterface getRepository()()
 * @method \Spryker\Zed\Payment\PaymentConfig getConfig()
 */
class PaymentBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Spryker\Zed\Payment\Business\Method\PaymentMethodReaderInterface
     */
    public function createPaymentMethodReader()
    {
        return new PaymentMethodReader(
            $this->getPaymentMethodFilterPlugins(),
            $this->getConfig(),
            $this->getStoreFacade(),
            $this->getRepository(),
        );
    }

    /**
     * @return \Spryker\Zed\Payment\Business\Hook\OrderPostSaveHookInterface
     */
    public function createOrderPostSaveHook(): OrderPostSaveHookInterface
    {
        return new OrderPostSaveHook(
            $this->createQuoteDataMapper(),
            $this->getLocaleFacade(),
            $this->getRepository(),
            $this->getPaymentClient(),
            $this->getConfig(),
            $this->getStoreReferenceFacade(),
            $this->getPaymentService(),
        );
    }

    /**
     * @return \Spryker\Zed\Payment\Dependency\Facade\PaymentToStoreReferenceFacadeInterface
     */
    public function getStoreReferenceFacade(): PaymentToStoreReferenceFacadeInterface
    {
        return $this->getProvidedDependency(PaymentDependencyProvider::FACADE_STORE_REFERENCE);
    }

    /**
     * @return \Spryker\Client\Payment\PaymentClientInterface
     */
    public function getPaymentClient(): PaymentClientInterface
    {
        return $this->getProvidedDependency(PaymentDependencyProvider::CLIENT_PAYMENT);
    }

    /**
     * @return \Spryker\Zed\Payment\Business\Mapper\QuoteDataMapperInterface
     */
    public function createQuoteDataMapper(): QuoteDataMapperInterface
    {
        return new QuoteDataMapper();
    }

    /**
     * @return \Spryker\Zed\Payment\Dependency\Facade\PaymentToLocaleFacadeInterface
     */
    public function getLocaleFacade(): PaymentToLocaleFacadeInterface
    {
        return $this->getProvidedDependency(PaymentDependencyProvider::FACADE_LOCALE);
    }

    /**
     * @return \Spryker\Service\Payment\PaymentServiceInterface
     */
    public function getPaymentService(): PaymentServiceInterface
    {
        return $this->getProvidedDependency(PaymentDependencyProvider::SERVICE_PAYMENT);
    }

    /**
     * @return \Spryker\Zed\Payment\Business\Method\PaymentMethodValidatorInterface
     */
    public function createPaymentMethodValidator(): PaymentMethodValidatorInterface
    {
        return new PaymentMethodValidator(
            $this->createPaymentMethodReader(),
            $this->getPaymentService(),
        );
    }

    /**
     * @return \Spryker\Zed\Payment\Business\Enabler\PaymentMethodEnablerInterface
     */
    public function createPaymentMethodEnabler(): PaymentMethodEnablerInterface
    {
        return new PaymentMethodEnabler(
            $this->getRepository(),
            $this->createPaymentWriter(),
            $this->createPaymentMethodUpdater(),
            $this->createPaymentMethodKeyGenerator(),
            $this->createPaymentMethodEventMapper(),
            $this->getStoreReferenceFacade(),
        );
    }

    /**
     * @return \Spryker\Zed\Payment\Business\Disabler\PaymentMethodDisablerInterface
     */
    public function createPaymentMethodDisabler(): PaymentMethodDisablerInterface
    {
        return new PaymentMethodDisabler(
            $this->getEntityManager(),
            $this->createPaymentMethodKeyGenerator(),
            $this->createPaymentMethodEventMapper(),
            $this->getStoreReferenceFacade(),
        );
    }

    /**
     * @return \Spryker\Zed\Payment\Business\Mapper\PaymentMethodEventMapperInterface
     */
    public function createPaymentMethodEventMapper(): PaymentMethodEventMapperInterface
    {
        return new PaymentMethodEventMapper();
    }

    /**
     * @return \Spryker\Zed\Payment\Business\Calculation\PaymentCalculatorInterface
     */
    public function createPaymentCalculator()
    {
        return new PaymentCalculator();
    }

    /**
     * @return \Spryker\Zed\Payment\Business\Method\PaymentMethodFinderInterface
     */
    public function createPaymentMethodFinder(): PaymentMethodFinderInterface
    {
        return new PaymentMethodFinder($this->getRepository());
    }

    /**
     * @return \Spryker\Zed\Payment\Business\Method\PaymentMethodStoreRelationUpdaterInterface
     */
    public function createPaymentMethodStoreRelationUpdater(): PaymentMethodStoreRelationUpdaterInterface
    {
        return new PaymentMethodStoreRelationUpdater(
            $this->getEntityManager(),
            $this->getRepository(),
        );
    }

    /**
     * @return \Spryker\Zed\Payment\Business\Method\PaymentMethodUpdaterInterface
     */
    public function createPaymentMethodUpdater(): PaymentMethodUpdaterInterface
    {
        return new PaymentMethodUpdater(
            $this->getEntityManager(),
            $this->createPaymentMethodStoreRelationUpdater(),
        );
    }

    /**
     * @return \Spryker\Zed\Payment\Business\Generator\PaymentMethodKeyGeneratorInterface
     */
    public function createPaymentMethodKeyGenerator(): PaymentMethodKeyGeneratorInterface
    {
        return new PaymentMethodKeyGenerator($this->getUtilTextService());
    }

    /**
     * @return \Spryker\Zed\Payment\Dependency\Service\PaymentToUtilTextServiceInterface
     */
    public function getUtilTextService(): PaymentToUtilTextServiceInterface
    {
        return $this->getProvidedDependency(PaymentDependencyProvider::SERVICE_UTIL_TEXT);
    }

    /**
     * @return array<\Spryker\Zed\PaymentExtension\Dependency\Plugin\PaymentMethodFilterPluginInterface>
     */
    public function getPaymentMethodFilterPlugins()
    {
        return $this->getProvidedDependency(PaymentDependencyProvider::PAYMENT_METHOD_FILTER_PLUGINS);
    }

    /**
     * @return \Spryker\Zed\Payment\Dependency\Facade\PaymentToStoreFacadeInterface
     */
    public function getStoreFacade(): PaymentToStoreFacadeInterface
    {
        return $this->getProvidedDependency(PaymentDependencyProvider::FACADE_STORE);
    }

    /**
     * @return \Spryker\Zed\Payment\Business\Writer\PaymentWriterInterface
     */
    public function createPaymentWriter(): PaymentWriterInterface
    {
        return new PaymentWriter($this->getEntityManager());
    }

    /**
     * @deprecated Will be removed without replacement.
     *
     * @return \Spryker\Zed\Payment\Business\Checkout\PaymentPluginExecutorInterface
     */
    public function createCheckoutPaymentPluginExecutor()
    {
        return new PaymentPluginExecutor($this->getCheckoutPlugins(), $this->createPaymentSaver());
    }

    /**
     * @deprecated Will be removed without replacement.
     *
     * @return \Spryker\Zed\Payment\Business\Order\SalesPaymentSaverInterface
     */
    public function createPaymentSaver()
    {
        return new SalesPaymentSaver($this->getQueryContainer());
    }

    /**
     * @deprecated Will be removed without replacement.
     *
     * @return \Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPluginCollectionInterface
     */
    public function getCheckoutPlugins()
    {
        return $this->getProvidedDependency(PaymentDependencyProvider::CHECKOUT_PLUGINS);
    }

    /**
     * @deprecated Will be removed without replacement.
     *
     * @return \Spryker\Zed\Payment\Business\Order\SalesPaymentHydratorInterface
     */
    public function createPaymentHydrator()
    {
        return new SalesPaymentHydrator(
            $this->getPaymentHydrationPlugins(),
            $this->getQueryContainer(),
        );
    }

    /**
     * @deprecated Will be removed without replacement.
     *
     * @return \Spryker\Zed\Payment\Dependency\Plugin\Sales\PaymentHydratorPluginCollectionInterface
     */
    public function getPaymentHydrationPlugins()
    {
        return $this->getProvidedDependency(PaymentDependencyProvider::PAYMENT_HYDRATION_PLUGINS);
    }

    /**
     * @deprecated Will be removed without replacement.
     *
     * @return \Spryker\Zed\Payment\Business\Order\SalesPaymentReaderInterface
     */
    public function createSalesPaymentReader()
    {
        return new SalesPaymentReader(
            $this->getQueryContainer(),
        );
    }

    /**
     * @return \Spryker\Zed\Payment\Business\Provider\PaymentProviderReaderInterface
     */
    public function createPaymentProviderReader(): PaymentProviderReaderInterface
    {
        return new PaymentProviderReader($this->getRepository());
    }

    /**
     * @return \Spryker\Zed\Payment\Dependency\Facade\PaymentToOmsFacadeInterface
     */
    public function getOmsFacade(): PaymentToOmsFacadeInterface
    {
        return $this->getProvidedDependency(PaymentDependencyProvider::FACADE_OMS);
    }

    /**
     * @return \Spryker\Zed\Payment\Dependency\Facade\PaymentToMessageBrokerBridge
     */
    public function getMessageBrokerFacade(): PaymentToMessageBrokerBridge
    {
        return $this->getProvidedDependency(PaymentDependencyProvider::FACADE_MESSAGE_BROKER);
    }

    /**
     * @return \Spryker\Zed\Payment\Business\MessageEmitter\MessageEmitterInterface
     */
    public function createMessageEmitter(): MessageEmitterInterface
    {
        return new MessageEmitter($this->getMessageBrokerFacade());
    }

    /**
     * @return \Spryker\Zed\Payment\Business\EventTriggerer\PaymentMessageOmsEventTriggererInterface
     */
    public function createPaymentMessageOmsEventTriggerer(): PaymentMessageOmsEventTriggererInterface
    {
        return new PaymentMessageOmsEventTriggerer(
            $this->getOmsFacade(),
            $this->getConfig(),
        );
    }
}
