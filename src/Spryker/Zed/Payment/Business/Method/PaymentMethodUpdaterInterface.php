<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment\Business\Method;

use Generated\Shared\Transfer\PaymentMethodAddedTransfer;
use Generated\Shared\Transfer\PaymentMethodDeletedTransfer;
use Generated\Shared\Transfer\PaymentMethodResponseTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;

interface PaymentMethodUpdaterInterface
{
    /**
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodResponseTransfer
     */
    public function updatePaymentMethod(PaymentMethodTransfer $paymentMethodTransfer): PaymentMethodResponseTransfer;

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodAddedTransfer $paymentMethodAddedTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodTransfer
     */
    public function enableForeignPaymentMethod(PaymentMethodAddedTransfer $paymentMethodAddedTransfer): PaymentMethodTransfer;

    /**
     * @param \Generated\Shared\Transfer\PaymentMethodDeletedTransfer $paymentMethodDeletedTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodTransfer
     */
    public function disableForeignPaymentMethod(PaymentMethodDeletedTransfer $paymentMethodDeletedTransfer): PaymentMethodTransfer;
}
