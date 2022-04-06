<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment\Business\Enabler;

use Generated\Shared\Transfer\PaymentMethodAddedTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\PaymentProviderTransfer;
use Spryker\Zed\Payment\Business\Generator\PaymentMethodKeyGeneratorInterface;
use Spryker\Zed\Payment\Business\Mapper\PaymentMethodEventMapperInterface;
use Spryker\Zed\Payment\Business\Method\PaymentMethodUpdaterInterface;
use Spryker\Zed\Payment\Business\Writer\PaymentWriterInterface;
use Spryker\Zed\Payment\Dependency\Facade\PaymentToStoreReferenceFacadeInterface;
use Spryker\Zed\Payment\Persistence\PaymentRepositoryInterface;

class PaymentMethodEnabler implements PaymentMethodEnablerInterface
{
    /**
     * @var \Spryker\Zed\Payment\Persistence\PaymentRepositoryInterface
     */
    protected $paymentRepository;

    /**
     * @var \Spryker\Zed\Payment\Business\Writer\PaymentWriterInterface
     */
    protected $paymentWriter;

    /**
     * @var \Spryker\Zed\Payment\Business\Method\PaymentMethodUpdaterInterface
     */
    protected $paymentMethodUpdater;

    /**
     * @var \Spryker\Zed\Payment\Business\Generator\PaymentMethodKeyGeneratorInterface
     */
    protected $paymentMethodKeyGenerator;

    /**
     * @var \Spryker\Zed\Payment\Business\Mapper\PaymentMethodEventMapperInterface
     */
    protected PaymentMethodEventMapperInterface $paymentMethodEventMapper;

    /**
     * @var \Spryker\Zed\Payment\Dependency\Facade\PaymentToStoreReferenceFacadeInterface
     */
    protected PaymentToStoreReferenceFacadeInterface $storeReferenceFacade;

    /**
     * @param \Spryker\Zed\Payment\Persistence\PaymentRepositoryInterface $paymentRepository
     * @param \Spryker\Zed\Payment\Business\Writer\PaymentWriterInterface $paymentWriter
     * @param \Spryker\Zed\Payment\Business\Method\PaymentMethodUpdaterInterface $paymentMethodUpdater
     * @param \Spryker\Zed\Payment\Business\Generator\PaymentMethodKeyGeneratorInterface $paymentMethodKeyGenerator
     * @param \Spryker\Zed\Payment\Business\Mapper\PaymentMethodEventMapperInterface $paymentMethodEventMapper
     * @param \Spryker\Zed\Payment\Dependency\Facade\PaymentToStoreReferenceFacadeInterface $storeReferenceFacade
     */
    public function __construct(
        PaymentRepositoryInterface $paymentRepository,
        PaymentWriterInterface $paymentWriter,
        PaymentMethodUpdaterInterface $paymentMethodUpdater,
        PaymentMethodKeyGeneratorInterface $paymentMethodKeyGenerator,
        PaymentMethodEventMapperInterface $paymentMethodEventMapper,
        PaymentToStoreReferenceFacadeInterface $storeReferenceFacade
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->paymentWriter = $paymentWriter;
        $this->paymentMethodUpdater = $paymentMethodUpdater;
        $this->paymentMethodKeyGenerator = $paymentMethodKeyGenerator;
        $this->paymentMethodEventMapper = $paymentMethodEventMapper;
        $this->storeReferenceFacade = $storeReferenceFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodAddedTransfer $paymentMethodAddedTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodTransfer
     */
    public function enablePaymentMethod(PaymentMethodAddedTransfer $paymentMethodAddedTransfer): PaymentMethodTransfer
    {
        $paymentMethodTransfer = $this->paymentMethodEventMapper->mapPaymentMethodAddedTransferToPaymentMethodTransfer(
            $paymentMethodAddedTransfer,
            new PaymentMethodTransfer(),
        );

        $storeTransfer = $this->storeReferenceFacade->getStoreByStoreReference(
            $paymentMethodAddedTransfer->getMessageAttributesOrFail()->getStoreReferenceOrFail(),
        );

        $paymentMethodKey = $this->paymentMethodKeyGenerator->generatePaymentMethodKey(
            $paymentMethodTransfer->getGroupNameOrFail(),
            $paymentMethodTransfer->getLabelNameOrFail(),
            $storeTransfer->getNameOrFail(),
        );

        $paymentProviderTransfer = $this->findOrCreatePaymentProvider($paymentMethodTransfer->getGroupNameOrFail());

        $paymentMethodTransfer
            ->setName($paymentMethodTransfer->getLabelName())
            ->setIdPaymentProvider($paymentProviderTransfer->getIdPaymentProvider())
            ->setPaymentMethodKey($paymentMethodKey)
            ->setIsActive(false)
            ->setIsHidden(false);

        $existingPaymentMethodTransfer = $this->paymentRepository->findPaymentMethod($paymentMethodTransfer);
        if ($existingPaymentMethodTransfer) {
            $existingPaymentMethodTransfer->fromArray($paymentMethodTransfer->modifiedToArray());

            $paymentMethodResponseTransfer = $this->paymentMethodUpdater->updatePaymentMethod($existingPaymentMethodTransfer);

            return $paymentMethodResponseTransfer->getPaymentMethodOrFail();
        }

        $paymentMethodResponseTransfer = $this->paymentWriter->createPaymentMethod($paymentMethodTransfer);

        return $paymentMethodResponseTransfer->getPaymentMethodOrFail();
    }

    /**
     * @param string $paymentProviderName
     *
     * @return \Generated\Shared\Transfer\PaymentProviderTransfer
     */
    protected function findOrCreatePaymentProvider(string $paymentProviderName): PaymentProviderTransfer
    {
        $foundPaymentProviderTransfer = $this->paymentRepository->findPaymentProviderByKey(
            $paymentProviderName,
        );

        if ($foundPaymentProviderTransfer) {
            return $foundPaymentProviderTransfer;
        }

        $paymentProviderTransfer = (new PaymentProviderTransfer())
            ->setPaymentProviderKey($paymentProviderName)
            ->setName($paymentProviderName);
        $paymentProviderResponseTransfer = $this->paymentWriter->createPaymentProvider($paymentProviderTransfer);

        return $paymentProviderResponseTransfer->getPaymentProvider() ?? $paymentProviderTransfer;
    }
}
