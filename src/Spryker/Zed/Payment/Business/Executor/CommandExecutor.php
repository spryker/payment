<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment\Business\Executor;

use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentCancelReservationRequestedTransfer;
use Generated\Shared\Transfer\PaymentConfirmationRequestedTransfer;
use Generated\Shared\Transfer\PaymentRefundRequestedTransfer;
use Spryker\Zed\Payment\Dependency\Facade\PaymentToMessageBrokerBridge;

class CommandExecutor implements CommandExecutorInterface
{
    /**
     * @var \Spryker\Zed\Payment\Dependency\Facade\PaymentToMessageBrokerBridge
     */
    protected $messageBrokerFacade;

    /**
     * @param \Spryker\Zed\Payment\Dependency\Facade\PaymentToMessageBrokerBridge $messageBrokerFacade
     */
    public function __construct(PaymentToMessageBrokerBridge $messageBrokerFacade)
    {
        $this->messageBrokerFacade = $messageBrokerFacade;
    }

    /**
     * @param array $orderItemIds
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function sendEventPaymentCancelReservationPending(array $orderItemIds, OrderTransfer $orderTransfer): void
    {
        $paymentCancelReservationRequestedTransfer = (new PaymentCancelReservationRequestedTransfer())
            ->setOrderReference($orderTransfer->getOrderReference())
            ->setOrderItemIds($orderItemIds)
            ->setCurrencyIsoCode($orderTransfer->getCurrencyIsoCode())
            ->setAmount(0);

        $this->messageBrokerFacade->sendMessage($paymentCancelReservationRequestedTransfer);
    }

    /**
     * @param array $orderItemIds
     * @param int $orderItemsTotal
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function sendEventPaymentConfirmationPending(
        array $orderItemIds,
        int $orderItemsTotal,
        OrderTransfer $orderTransfer
    ): void {
        if ($orderItemsTotal > 0) {
            $paymentConfirmationRequestedTransfer = (new PaymentConfirmationRequestedTransfer())
                ->setOrderReference($orderTransfer->getOrderReference())
                ->setOrderItemIds($orderItemIds)
                ->setCurrencyIsoCode($orderTransfer->getCurrencyIsoCode())
                ->setAmount($orderItemsTotal);

            $this->messageBrokerFacade->sendMessage($paymentConfirmationRequestedTransfer);
        }
    }

    /**
     * @param array $orderItemIds
     * @param int $orderItemsTotal
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function sendEventPaymentRefundPending(
        array $orderItemIds,
        int $orderItemsTotal,
        OrderTransfer $orderTransfer
    ): void {
        if ($orderItemsTotal > 0) {
            $paymentRefundRequestedTransfer = (new PaymentRefundRequestedTransfer())
                ->setOrderReference($orderTransfer->getOrderReference())
                ->setOrderItemIds($orderItemIds)
                ->setCurrencyIsoCode($orderTransfer->getCurrencyIsoCode())
                ->setAmount($orderItemsTotal * -1);

            $this->messageBrokerFacade->sendMessage($paymentRefundRequestedTransfer);
        }
    }
}
