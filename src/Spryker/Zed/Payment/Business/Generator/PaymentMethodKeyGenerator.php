<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment\Business\Generator;

use Spryker\Zed\Payment\Dependency\Service\PaymentToUtilTextServiceInterface;

class PaymentMethodKeyGenerator implements PaymentMethodKeyGeneratorInterface
{
    /**
     * @var \Spryker\Zed\Payment\Dependency\Service\PaymentToUtilTextServiceInterface
     */
    protected $utilTextService;

    public function __construct(PaymentToUtilTextServiceInterface $utilTextService)
    {
        $this->utilTextService = $utilTextService;
    }

    public function generatePaymentMethodKey(
        string $paymentProviderName,
        string $paymentMethodName,
        string $storeName
    ): string {
        return $this->utilTextService->generateSlug(
            sprintf('%s %s %s', $paymentProviderName, $paymentMethodName, $storeName),
        );
    }

    public function generate(
        string $paymentProviderName,
        string $paymentMethodName
    ): string {
        return $this->utilTextService->generateSlug(
            sprintf('%s %s', $paymentProviderName, $paymentMethodName),
        );
    }
}
