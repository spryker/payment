<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment\Communication\Plugin\MessageBroker;

use Generated\Shared\Transfer\PaymentCanceledTransfer;
use Generated\Shared\Transfer\PaymentReservationCanceledTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\MessageBrokerExtension\Dependency\Plugin\MessageHandlerPluginInterface;

/**
 * @deprecated Use {@link \Spryker\Zed\Payment\Communication\Plugin\MessageBroker\PaymentOperationsMessageHandlerPlugin} instead.
 *
 * @method \Spryker\Zed\Payment\Business\PaymentFacadeInterface getFacade()
 * @method \Spryker\Zed\Payment\Persistence\PaymentQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\Payment\PaymentConfig getConfig()
 * @method \Spryker\Zed\Payment\Communication\PaymentCommunicationFactory getFactory()
 */
class PaymentReservationCanceledMessageHandlerPlugin extends AbstractPlugin implements MessageHandlerPluginInterface
{
    /**
     * {@inheritDoc}
     * - Triggers an OMS event for PaymentReservationCanceledTransfer.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PaymentReservationCanceledTransfer $paymentReservationCanceledTransfer
     *
     * @return void
     */
    public function onPaymentReservationCanceled(PaymentReservationCanceledTransfer $paymentReservationCanceledTransfer): void
    {
        $paymentCanceledTransfer = (new PaymentCanceledTransfer())->fromArray($paymentReservationCanceledTransfer->toArray(), true);

        $this->getFacade()->triggerPaymentMessageOmsEvent($paymentCanceledTransfer);
    }

    /**
     * {@inheritDoc}
     * - Return an array where the key is the class name to be handled and the value is the callable that handles the message.
     *
     * @api
     *
     * @return array<string, callable>
     */
    public function handles(): iterable
    {
        yield PaymentReservationCanceledTransfer::class => [$this, 'onPaymentReservationCanceled'];
    }
}
