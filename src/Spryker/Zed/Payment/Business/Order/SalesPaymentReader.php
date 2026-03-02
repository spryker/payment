<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment\Business\Order;

use Generated\Shared\Transfer\SalesPaymentTransfer;
use Spryker\Zed\Payment\Persistence\PaymentQueryContainerInterface;

/**
 * @deprecated The functionality moved to SalesPayment module.
 */
class SalesPaymentReader implements SalesPaymentReaderInterface
{
    /**
     * @var \Spryker\Zed\Payment\Persistence\PaymentQueryContainerInterface
     */
    protected $paymentQueryContainer;

    public function __construct(PaymentQueryContainerInterface $paymentQueryContainer)
    {
        $this->paymentQueryContainer = $paymentQueryContainer;
    }

    public function getPaymentMethodPriceToPay(SalesPaymentTransfer $paymentTransfer): int
    {
        $salesPaymentEntity = $this->paymentQueryContainer->queryPaymentMethodPriceToPay(
            $paymentTransfer->getFkSalesOrder(),
            $paymentTransfer->getPaymentProvider(),
            $paymentTransfer->getPaymentMethod(),
        )->findOne();

        return $salesPaymentEntity->getAmount();
    }
}
