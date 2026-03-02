<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment\Persistence;

use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\PaymentProviderTransfer;

interface PaymentEntityManagerInterface
{
    public function updatePaymentMethod(
        PaymentMethodTransfer $paymentMethodTransfer
    ): ?PaymentMethodTransfer;

    /**
     * @param array<int> $idStores
     * @param int $idPaymentMethod
     *
     * @return void
     */
    public function addPaymentMethodStoreRelationsForStores(
        array $idStores,
        int $idPaymentMethod
    ): void;

    /**
     * @param array<int> $idStores
     * @param int $idPaymentMethod
     *
     * @return void
     */
    public function removePaymentMethodStoreRelationsForStores(
        array $idStores,
        int $idPaymentMethod
    ): void;

    public function hidePaymentMethod(PaymentMethodTransfer $paymentMethodTransfer): void;

    public function createPaymentProvider(PaymentProviderTransfer $paymentProviderTransfer): PaymentProviderTransfer;

    public function createPaymentMethod(PaymentMethodTransfer $paymentMethodTransfer): PaymentMethodTransfer;
}
