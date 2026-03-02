<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment\Business\Method;

use Generated\Shared\Transfer\AddPaymentMethodTransfer;
use Generated\Shared\Transfer\DeletePaymentMethodTransfer;
use Generated\Shared\Transfer\PaymentMethodResponseTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\UpdatePaymentMethodTransfer;

interface PaymentMethodUpdaterInterface
{
    public function updatePaymentMethod(UpdatePaymentMethodTransfer $updatePaymentMethodTransfer): PaymentMethodTransfer;

    public function update(PaymentMethodTransfer $paymentMethodTransfer): PaymentMethodResponseTransfer;

    /**
     * @deprecated Use {@link \Spryker\Zed\Payment\Business\Method\PaymentMethodUpdaterInterface::addPaymentMethod()} instead.
     *
     * @param \Generated\Shared\Transfer\AddPaymentMethodTransfer $addPaymentMethodTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodTransfer
     */
    public function enableForeignPaymentMethod(AddPaymentMethodTransfer $addPaymentMethodTransfer): PaymentMethodTransfer;

    public function addPaymentMethod(AddPaymentMethodTransfer $addPaymentMethodTransfer): PaymentMethodTransfer;

    /**
     * @deprecated Use {@link \Spryker\Zed\Payment\Business\Method\PaymentMethodUpdaterInterface::deletePaymentMethod()} instead.
     *
     * @param \Generated\Shared\Transfer\DeletePaymentMethodTransfer $deletePaymentMethodTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodTransfer
     */
    public function disableForeignPaymentMethod(DeletePaymentMethodTransfer $deletePaymentMethodTransfer): PaymentMethodTransfer;

    public function deletePaymentMethod(DeletePaymentMethodTransfer $deletePaymentMethodTransfer): PaymentMethodTransfer;
}
