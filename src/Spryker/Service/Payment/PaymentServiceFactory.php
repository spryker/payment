<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Service\Payment;

use Spryker\Service\Kernel\AbstractServiceFactory;
use Spryker\Service\Payment\Model\PaymentMethodKeyExtractor;
use Spryker\Service\Payment\Model\PaymentMethodKeyExtractorInterface;

class PaymentServiceFactory extends AbstractServiceFactory
{
    /**
     * @return \Spryker\Service\Payment\Model\PaymentMethodKeyExtractorInterface
     */
    public function createPaymentMethodKeyExtractor(): PaymentMethodKeyExtractorInterface
    {
        return new PaymentMethodKeyExtractor();
    }
}
