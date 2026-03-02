<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Shared\Payment\Helper;

use Codeception\Module;

trait PaymentDataHelperTrait
{
    protected function getPaymentDataHelper(): PaymentDataHelper
    {
        /** @var \SprykerTest\Shared\Payment\Helper\PaymentDataHelper $paymentDataHelper */
        $paymentDataHelper = $this->getModule('\\' . PaymentDataHelper::class);

        return $paymentDataHelper;
    }

    abstract protected function getModule(string $name): Module;
}
