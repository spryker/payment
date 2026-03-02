<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment\Business\Provider;

use Generated\Shared\Transfer\PaymentProviderTransfer;
use Spryker\Zed\Payment\Persistence\PaymentRepositoryInterface;

class PaymentProviderReader implements PaymentProviderReaderInterface
{
    /**
     * @var \Spryker\Zed\Payment\Persistence\PaymentRepositoryInterface
     */
    private $paymentRepository;

    public function __construct(PaymentRepositoryInterface $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function findPaymentProvider(PaymentProviderTransfer $paymentProviderTransfer): ?PaymentProviderTransfer
    {
        $paymentProviderTransfer->requirePaymentProviderKey();

        return $this->paymentRepository
            ->findPaymentProviderByKey($paymentProviderTransfer->getPaymentProviderKey());
    }
}
