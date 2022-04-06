<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment\Communication\Plugin\Event\Subscriber;

use Spryker\Zed\Event\Dependency\EventCollectionInterface;
use Spryker\Zed\Event\Dependency\Plugin\EventSubscriberInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Payment\Communication\Plugin\Event\Listener\PaymentEventTypeListenerPlugin;
use Spryker\Zed\Payment\Dependency\PaymentEvents;

/**
 * @method \Spryker\Zed\Payment\PaymentConfig getConfig()
 * @method \Spryker\Zed\Payment\Business\PaymentFacadeInterface getFacade()
 * @method \Spryker\Zed\Payment\Communication\PaymentCommunicationFactory getFactory()
 * @method \Spryker\Zed\Payment\Persistence\PaymentQueryContainerInterface getQueryContainer()
 */
class PaymentEventTypeSubscriberPlugin extends AbstractPlugin implements EventSubscriberInterface
{
    /**
     * {@inheritDoc}
     * - Adds listeners for payment related eventNames
     *
     * @api
     *
     * @param \Spryker\Zed\Event\Dependency\EventCollectionInterface $eventCollection
     *
     * @return \Spryker\Zed\Event\Dependency\EventCollectionInterface
     */
    public function getSubscribedEvents(EventCollectionInterface $eventCollection): EventCollectionInterface
    {
        $this->addOrderPaymentConfirmedListener($eventCollection);
        $this->addOrderPaymentConfirmationFailedListener($eventCollection);
        $this->addOrderPaymentPreauthorizedListener($eventCollection);
        $this->addOrderPaymentPreauthorizedFailedListener($eventCollection);
        $this->addOrderPaymentRefundedListener($eventCollection);
        $this->addOrderPaymentRefundedFailedListener($eventCollection);
        $this->addOrderPaymentReservationCanceledListener($eventCollection);
        $this->addOrderPaymentCancelReservationFailedListener($eventCollection);

        return $eventCollection;
    }

    /**
     * @param \Spryker\Zed\Event\Dependency\EventCollectionInterface $eventCollection
     *
     * @return void
     */
    protected function addOrderPaymentConfirmedListener(EventCollectionInterface $eventCollection)
    {
        $eventCollection->addListener(
            PaymentEvents::EVENT_LISTENED_ORDER_PAYMENT_CONFIRMED,
            new PaymentEventTypeListenerPlugin(),
        );
    }

    /**
     * @param \Spryker\Zed\Event\Dependency\EventCollectionInterface $eventCollection
     *
     * @return void
     */
    protected function addOrderPaymentConfirmationFailedListener(EventCollectionInterface $eventCollection)
    {
        $eventCollection->addListener(
            PaymentEvents::EVENT_LISTENED_ORDER_PAYMENT_CONFIRMATION_FAILED,
            new PaymentEventTypeListenerPlugin(),
        );
    }

    /**
     * @param \Spryker\Zed\Event\Dependency\EventCollectionInterface $eventCollection
     *
     * @return void
     */
    protected function addOrderPaymentPreauthorizedListener(EventCollectionInterface $eventCollection)
    {
        $eventCollection->addListener(
            PaymentEvents::EVENT_LISTENED_ORDER_PAYMENT_PREAUTHORIZED,
            new PaymentEventTypeListenerPlugin(),
        );
    }

    /**
     * @param \Spryker\Zed\Event\Dependency\EventCollectionInterface $eventCollection
     *
     * @return void
     */
    protected function addOrderPaymentPreauthorizedFailedListener(EventCollectionInterface $eventCollection)
    {
        $eventCollection->addListener(
            PaymentEvents::EVENT_LISTENED_ORDER_PAYMENT_PREAUTHORIZATION_FAILED,
            new PaymentEventTypeListenerPlugin(),
        );
    }

    /**
     * @param \Spryker\Zed\Event\Dependency\EventCollectionInterface $eventCollection
     *
     * @return void
     */
    protected function addOrderPaymentRefundedListener(EventCollectionInterface $eventCollection)
    {
        $eventCollection->addListener(
            PaymentEvents::EVENT_LISTENED_ORDER_PAYMENT_REFUNDED,
            new PaymentEventTypeListenerPlugin(),
        );
    }

    /**
     * @param \Spryker\Zed\Event\Dependency\EventCollectionInterface $eventCollection
     *
     * @return void
     */
    protected function addOrderPaymentRefundedFailedListener(EventCollectionInterface $eventCollection)
    {
        $eventCollection->addListener(
            PaymentEvents::EVENT_LISTENED_ORDER_PAYMENT_REFUND_FAILED,
            new PaymentEventTypeListenerPlugin(),
        );
    }

    /**
     * @param \Spryker\Zed\Event\Dependency\EventCollectionInterface $eventCollection
     *
     * @return void
     */
    protected function addOrderPaymentReservationCanceledListener(EventCollectionInterface $eventCollection)
    {
        $eventCollection->addListener(
            PaymentEvents::EVENT_LISTENED_ORDER_PAYMENT_RESERVATION_CANCELED,
            new PaymentEventTypeListenerPlugin(),
        );
    }

    /**
     * @param \Spryker\Zed\Event\Dependency\EventCollectionInterface $eventCollection
     *
     * @return void
     */
    protected function addOrderPaymentCancelReservationFailedListener(EventCollectionInterface $eventCollection)
    {
        $eventCollection->addListener(
            PaymentEvents::EVENT_LISTENED_ORDER_PAYMENT_CANCEL_RESERVATION_FAILED,
            new PaymentEventTypeListenerPlugin(),
        );
    }
}
