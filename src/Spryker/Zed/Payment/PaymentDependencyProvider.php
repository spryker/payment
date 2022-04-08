<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Payment\Dependency\Facade\PaymentToLocaleFacadeBridge;
use Spryker\Zed\Payment\Dependency\Facade\PaymentToMessageBrokerBridge;
use Spryker\Zed\Payment\Dependency\Facade\PaymentToOmsFacadeBridge;
use Spryker\Zed\Payment\Dependency\Facade\PaymentToStoreFacadeBridge;
use Spryker\Zed\Payment\Dependency\Facade\PaymentToStoreReferenceFacadeBridge;
use Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPluginCollection;
use Spryker\Zed\Payment\Dependency\Plugin\Sales\PaymentHydratorPluginCollection;
use Spryker\Zed\Payment\Dependency\QueryContainer\PaymentToSalesBridge;
use Spryker\Zed\Payment\Dependency\Service\PaymentToUtilTextServiceBridge;

/**
 * @method \Spryker\Zed\Payment\PaymentConfig getConfig()
 */
class PaymentDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const FACADE_STORE = 'FACADE_STORE';

    /**
     * @var string
     */
    public const SERVICE_UTIL_TEXT = 'SERVICE_UTIL_TEXT';

    /**
     * @var string
     */
    public const PAYMENT_METHOD_FILTER_PLUGINS = 'PAYMENT_METHOD_FILTER_PLUGINS';

    /**
     * @deprecated Use {@link \Spryker\Zed\Checkout\CheckoutDependencyProvider::CHECKOUT_POST_HOOKS},
     * {@link \Spryker\Zed\Checkout\CheckoutDependencyProvider::CHECKOUT_ORDER_SAVERS},
     * {@link \Spryker\Zed\Checkout\CheckoutDependencyProvider::CHECKOUT_PRE_CONDITIONS} instead.
     *
     * @var string
     */
    public const CHECKOUT_PLUGINS = 'checkout plugins';

    /**
     * @deprecated Use {@link \Spryker\Zed\Checkout\CheckoutDependencyProvider::CHECKOUT_PRE_CONDITIONS} instead.
     *
     * @var string
     */
    public const CHECKOUT_PRE_CHECK_PLUGINS = 'pre check';

    /**
     * @deprecated Use {@link \Spryker\Zed\Checkout\CheckoutDependencyProvider::CHECKOUT_ORDER_SAVERS} instead.
     *
     * @var string
     */
    public const CHECKOUT_ORDER_SAVER_PLUGINS = 'order saver';

    /**
     * @deprecated Use {@link \Spryker\Zed\Checkout\CheckoutDependencyProvider::CHECKOUT_POST_HOOKS} instead.
     *
     * @var string
     */
    public const CHECKOUT_POST_SAVE_PLUGINS = 'post save';

    /**
     * @deprecated Use {@link \Spryker\Zed\SalesPayment\SalesPaymentDependencyProvider::SALES_PAYMENT_EXPANDER_PLUGINS} instead.
     *
     * @var string
     */
    public const PAYMENT_HYDRATION_PLUGINS = 'payment hydration plugins';

    /**
     * @var string
     */
    public const SERVICE_PAYMENT = 'SERVICE_PAYMENT';

    /**
     * @var string
     */
    public const FACADE_LOCALE = 'FACADE_LOCALE';

    /**
     * @var string
     */
    public const CLIENT_PAYMENT = 'CLIENT_PAYMENT';

    /**
     * @var string
     */
    public const FACADE_MESSAGE_BROKER = 'FACADE_MESSAGE_BROKER';

    /**
     * @var string
     */
    public const FACADE_STORE_REFERENCE = 'FACADE_STORE_REFERENCE';

    /**
     * @var string
     */
    public const FACADE_OMS = 'FACADE_OMS';

    /**
     * @var string
     */
    public const QUERY_CONTAINER_SALES = 'QUERY_CONTAINER_SALES';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = $this->addStoreFacade($container);
        $container = $this->addPaymentMethodFilterPlugins($container);

        $container = $this->addCheckoutPlugins($container);
        $container = $this->addPaymentHydrationPlugins($container);
        $container = $this->addPaymentService($container);
        $container = $this->addPaymentClient($container);
        $container = $this->addLocaleFacade($container);
        $container = $this->addUtilTextService($container);
        $container = $this->addStoreReferenceFacade($container);
        $container = $this->addOmsFacade($container);
        $container = $this->addMessageBrokerFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function providePersistenceLayerDependencies(Container $container)
    {
        $container = parent::providePersistenceLayerDependencies($container);

        $container = $this->addSalesQueryContainer($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addUtilTextService(Container $container): Container
    {
        $container->set(static::SERVICE_UTIL_TEXT, function (Container $container) {
            return new PaymentToUtilTextServiceBridge($container->getLocator()->utilText()->service());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addLocaleFacade(Container $container): Container
    {
        $container->set(static::FACADE_LOCALE, function (Container $container) {
            return new PaymentToLocaleFacadeBridge(
                $container->getLocator()->locale()->facade(),
            );
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPaymentClient(Container $container): Container
    {
        $container->set(static::CLIENT_PAYMENT, function (Container $container) {
            return $container->getLocator()->payment()->client();
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPaymentService(Container $container): Container
    {
        $container->set(static::SERVICE_PAYMENT, function (Container $container) {
            return $container->getLocator()->payment()->service();
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container = $this->addMessageBrokerFacade($container);
        $container = $this->addStoreReferenceFacade($container);
        $container = $this->addOmsFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addStoreFacade(Container $container): Container
    {
        $container->set(static::FACADE_STORE, function (Container $container) {
            return new PaymentToStoreFacadeBridge(
                $container->getLocator()->store()->facade(),
            );
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPaymentMethodFilterPlugins(Container $container): Container
    {
        $container->set(static::PAYMENT_METHOD_FILTER_PLUGINS, function (Container $container) {
            return $this->getPaymentMethodFilterPlugins();
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Zed\PaymentExtension\Dependency\Plugin\PaymentMethodFilterPluginInterface>
     */
    protected function getPaymentMethodFilterPlugins(): array
    {
        return [];
    }

    /**
     * @deprecated Will be removed without replacement.
     *
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCheckoutPlugins(Container $container)
    {
        $container->set(static::CHECKOUT_PLUGINS, function (Container $container) {
            return new CheckoutPluginCollection();
        });

        return $container;
    }

    /**
     * @deprecated Will be removed without replacement.
     *
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPaymentHydrationPlugins(Container $container)
    {
        $container->set(static::PAYMENT_HYDRATION_PLUGINS, function () {
            return $this->getPaymentHydrationPlugins();
        });

        return $container;
    }

    /**
     * @deprecated Will be removed without replacement.
     *
     * @return \Spryker\Zed\Payment\Dependency\Plugin\Sales\PaymentHydratorPluginCollectionInterface
     */
    protected function getPaymentHydrationPlugins()
    {
        return new PaymentHydratorPluginCollection();
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMessageBrokerFacade(Container $container): Container
    {
        $container->set(static::FACADE_MESSAGE_BROKER, function (Container $container) {
            return new PaymentToMessageBrokerBridge(
                $container->getLocator()->messageBroker()->facade(),
            );
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addStoreReferenceFacade(Container $container): Container
    {
        $container->set(static::FACADE_STORE_REFERENCE, function (Container $container) {
            return new PaymentToStoreReferenceFacadeBridge(
                $container->getLocator()->storeReference()->facade(),
            );
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addOmsFacade(Container $container): Container
    {
        $container->set(static::FACADE_OMS, function (Container $container) {
            return new PaymentToOmsFacadeBridge($container->getLocator()->oms()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addSalesQueryContainer(Container $container): Container
    {
        $container->set(static::QUERY_CONTAINER_SALES, function (Container $container) {
            return new PaymentToSalesBridge(
                $container->getLocator()->sales()->queryContainer(),
            );
        });

        return $container;
    }
}
