<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Service\Payment\Model;

use Generated\Shared\Transfer\PaymentTransfer;

class PaymentMethodKeyExtractor implements PaymentMethodKeyExtractorInterface
{
    /**
     * @param \Generated\Shared\Transfer\PaymentTransfer $paymentTransfer
     *
     * @return string
     */
    public function getPaymentSelectionKey(PaymentTransfer $paymentTransfer): string
    {
        preg_match('/^([\w]+)/', $paymentTransfer->getPaymentSelectionOrFail(), $matches);

        if (!isset($matches[0])) {
            return $paymentTransfer->getPaymentSelectionOrFail();
        }

        return $matches[0];
    }

    /**
     * @param \Generated\Shared\Transfer\PaymentTransfer $paymentTransfer
     *
     * @return string
     */
    public function getPaymentMethodKey(PaymentTransfer $paymentTransfer): string
    {
        preg_match('/\[([a-zA-Z0-9_-]+)\]/', $paymentTransfer->getPaymentSelectionOrFail(), $matches);

        if (!isset($matches[1])) {
            return $paymentTransfer->getPaymentSelectionOrFail();
        }

        return $matches[1];
    }
}
