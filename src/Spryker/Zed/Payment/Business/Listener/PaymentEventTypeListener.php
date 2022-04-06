<?php


/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment\Business\Listener;

use Orm\Zed\Sales\Persistence\Map\SpySalesOrderItemTableMap;
use Spryker\Shared\Kernel\Transfer\TransferInterface;
use Spryker\Zed\Oms\Communication\Exception\ListenerCannotProcessEventException;
use Spryker\Zed\Payment\Dependency\Facade\PaymentToOmsFacadeInterface;
use Spryker\Zed\Payment\Dependency\Facade\PaymentToStoreFacadeInterface;
use Spryker\Zed\Payment\Dependency\Facade\PaymentToStoreReferenceFacadeInterface;
use Spryker\Zed\Payment\Dependency\PaymentEvents;
use Spryker\Zed\Payment\Dependency\PaymentStateMachineEvents;
use Spryker\Zed\Payment\Persistence\PaymentQueryContainerInterface;

class PaymentEventTypeListener implements PaymentEventTypeListenerInterface
{
 /**
  * @var array<string, string>
  */
    protected const MAPPED_EVENTS_TO_PAYMENT_EVENTS = [
        PaymentEvents::EVENT_LISTENED_ORDER_PAYMENT_PREAUTHORIZED => PaymentStateMachineEvents::OMS_PAYMENT_AUTHORIZATION_SUCCESSFUL,
        PaymentEvents::EVENT_LISTENED_ORDER_PAYMENT_PREAUTHORIZATION_FAILED => PaymentStateMachineEvents::OMS_PAYMENT_AUTHORIZATION_FAILED,
        PaymentEvents::EVENT_LISTENED_ORDER_PAYMENT_CONFIRMED => PaymentStateMachineEvents::OMS_PAYMENT_CONFIRMATION_SUCCESSFUL,
        PaymentEvents::EVENT_LISTENED_ORDER_PAYMENT_CONFIRMATION_FAILED => PaymentStateMachineEvents::OMS_PAYMENT_CONFIRMATION_FAILED,
        PaymentEvents::EVENT_LISTENED_ORDER_PAYMENT_REFUNDED => PaymentStateMachineEvents::OMS_PAYMENT_REFUND_SUCCESSFUL,
        PaymentEvents::EVENT_LISTENED_ORDER_PAYMENT_REFUND_FAILED => PaymentStateMachineEvents::OMS_PAYMENT_REFUND_FAILED,

        PaymentEvents::EVENT_LISTENED_ORDER_PAYMENT_RESERVATION_CANCELED => PaymentStateMachineEvents::OMS_PAYMENT_CANCEL_RESERVATION_SUCCESSFUL,
        PaymentEvents::EVENT_LISTENED_ORDER_PAYMENT_CANCEL_RESERVATION_FAILED => PaymentStateMachineEvents::OMS_PAYMENT_CANCEL_RESERVATION_FAILED,
    ];

    /**
     * @var array<string>
     */
    protected const PAYMENT_EVENTS_APPLIED_FOR_ALL_ORDER_ITEMS = [
        PaymentStateMachineEvents::OMS_PAYMENT_AUTHORIZATION_SUCCESSFUL,
        PaymentStateMachineEvents::OMS_PAYMENT_AUTHORIZATION_FAILED,
    ];

    /**
     * @var \Spryker\Zed\Payment\Dependency\Facade\PaymentToOmsFacadeInterface
     */
    protected $omsFacade;

    /**
     * @var \Spryker\Zed\Payment\Dependency\Facade\PaymentToStoreFacadeInterface
     */
    protected $storeFacade;

    /**
     * @var \Spryker\Zed\Payment\Dependency\Facade\PaymentToStoreReferenceFacadeInterface;
     */
    protected $storeReferenceFacade;

    /**
     * @var \Spryker\Zed\Payment\Persistence\PaymentQueryContainerInterface
     */
    protected $paymentQueryContainer;

    /**
     * @param \Spryker\Zed\Payment\Dependency\Facade\PaymentToOmsFacadeInterface $omsFacade
     * @param \Spryker\Zed\Payment\Dependency\Facade\PaymentToStoreReferenceFacadeInterface $storeReferenceFacade
     * @param \Spryker\Zed\Payment\Dependency\Facade\PaymentToStoreFacadeInterface $storeFacade
     * @param \Spryker\Zed\Payment\Persistence\PaymentQueryContainerInterface $paymentQueryContainer
     */
    public function __construct(
        PaymentToOmsFacadeInterface $omsFacade,
        PaymentToStoreReferenceFacadeInterface $storeReferenceFacade,
        PaymentToStoreFacadeInterface $storeFacade,
        PaymentQueryContainerInterface $paymentQueryContainer
    ) {
        $this->omsFacade = $omsFacade;
        $this->storeReferenceFacade = $storeReferenceFacade;
        $this->storeFacade = $storeFacade;
        $this->paymentQueryContainer = $paymentQueryContainer;
    }

    /**
     * @param \Generated\Shared\Transfer\OrderPaymentEventTransfer $transfer
     * @param string $eventName
     *
     * @throws \Spryker\Zed\Oms\Communication\Exception\ListenerCannotProcessEventException
     *
     * @return void
     */
    public function handle(TransferInterface $transfer, string $eventName): void
    {
        $currentStore = $this->storeReferenceFacade->getStoreByStoreName(
            $this->storeFacade->getCurrentStore()->getName(),
        );

        if ($currentStore->getStoreReference() !== $transfer->getStoreReference()) {
            return;
        }

        if (!isset(static::MAPPED_EVENTS_TO_PAYMENT_EVENTS[$eventName])) {
            throw new ListenerCannotProcessEventException(
                sprintf('The `%s` event can\'t be processed by this listener.', $eventName),
            );
        }

        $paymentEventName = static::MAPPED_EVENTS_TO_PAYMENT_EVENTS[$eventName];

        if (in_array($paymentEventName, static::PAYMENT_EVENTS_APPLIED_FOR_ALL_ORDER_ITEMS, true)) {
            $this->expandEventTransferWithOrderItemIds($transfer);
        }

        $eventPayload = $transfer->getPayload();

        $this->omsFacade->triggerEventForOrderItems($paymentEventName, $eventPayload['orderItemIds']);
    }

    /**
     * @param \Generated\Shared\Transfer\OrderPaymentEventTransfer $transfer
     *
     * @return void
     */
    protected function expandEventTransferWithOrderItemIds(TransferInterface $transfer): void
    {
        $salesOrderItemIds = $this->paymentQueryContainer->getFactory()
            ->getSalesQueryContainer()
            ->querySalesOrderItem()
            ->useOrderQuery()
            ->filterByOrderReference($transfer->getOrderReference())
            ->endUse()
            ->select([SpySalesOrderItemTableMap::COL_ID_SALES_ORDER_ITEM])
            ->find()
            ->toArray();

        $transfer->setPayload(['orderItemIds' => $salesOrderItemIds]);
    }
}
