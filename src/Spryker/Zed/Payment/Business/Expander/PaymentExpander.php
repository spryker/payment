<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment\Business\Expander;

use Generated\Shared\Transfer\PaymentMethodConditionsTransfer;
use Generated\Shared\Transfer\PaymentMethodCriteriaTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Zed\Payment\Dependency\Facade\PaymentToStoreFacadeInterface;
use Spryker\Zed\Payment\PaymentConfig;
use Spryker\Zed\Payment\Persistence\PaymentRepositoryInterface;

class PaymentExpander implements PaymentExpanderInterface
{
    /**
     * @var \Spryker\Zed\Payment\Persistence\PaymentRepositoryInterface
     */
    protected $paymentRepository;

    /**
     * @var \Spryker\Zed\Payment\Dependency\Facade\PaymentToStoreFacadeInterface
     */
    protected $storeFacade;

    public function __construct(
        PaymentRepositoryInterface $paymentRepository,
        PaymentToStoreFacadeInterface $storeFacade
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->storeFacade = $storeFacade;
    }

    public function expandPaymentWithPaymentSelection(PaymentTransfer $paymentTransfer, StoreTransfer $storeTransfer): PaymentTransfer
    {
        if ($paymentTransfer->getPaymentSelection()) {
            return $paymentTransfer;
        }

        $paymentMethodTransfer = $this->getPaymentMethodByNameAndProvider(
            $paymentTransfer->getPaymentMethod() ?? $paymentTransfer->getPaymentMethodNameOrFail(),
            $paymentTransfer->getPaymentProvider() ?? $paymentTransfer->getPaymentProviderNameOrFail(),
            $storeTransfer,
        );

        if (!$paymentMethodTransfer->getPaymentMethodKey()) {
            return $paymentTransfer;
        }

        $paymentMethodKey = $this->getPaymentMethodKey($paymentMethodTransfer);

        $paymentTransfer->setPaymentSelection($paymentMethodKey);

        return $paymentTransfer;
    }

    protected function getPaymentMethodByNameAndProvider(
        string $paymentMethodName,
        string $paymentProviderKey,
        StoreTransfer $storeTransfer
    ): PaymentMethodTransfer {
        $idStore = $storeTransfer->getIdStore();

        if (!$idStore) {
            $idStore = $this->storeFacade->getStoreByName($storeTransfer->getNameOrFail())
                ->getIdStore();
        }

        $paymentMethodCriteriaTransfer = (new PaymentMethodCriteriaTransfer())
            ->setPaymentMethodConditions(
                (new PaymentMethodConditionsTransfer())
                    ->addName($paymentMethodName)
                    ->addPaymentProviderKey($paymentProviderKey)
                    ->addIdStore($idStore),
            );

        $paymentMethodCollectionTransfer = $this->paymentRepository->getPaymentMethodCollection($paymentMethodCriteriaTransfer);

        if ($paymentMethodCollectionTransfer->getPaymentMethods()->count()) {
            return $paymentMethodCollectionTransfer->getPaymentMethods()->offsetGet(0);
        }

        return new PaymentMethodTransfer();
    }

    protected function getPaymentMethodKey(PaymentMethodTransfer $paymentMethodTransfer): string
    {
        if (!$paymentMethodTransfer->getIsForeign()) {
            return $paymentMethodTransfer->getPaymentMethodKey();
        }

        return sprintf(PaymentConfig::PAYMENT_FOREIGN_PROVIDER . '[%s]', $paymentMethodTransfer->getPaymentMethodKey());
    }
}
