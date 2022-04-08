<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment\Business;

use Generated\Shared\Transfer\CalculableObjectTransfer;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\PaymentMethodAddedTransfer;
use Generated\Shared\Transfer\PaymentMethodDeletedTransfer;
use Generated\Shared\Transfer\PaymentMethodResponseTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\PaymentProviderCollectionTransfer;
use Generated\Shared\Transfer\PaymentProviderResponseTransfer;
use Generated\Shared\Transfer\PaymentProviderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SalesPaymentTransfer;
use Spryker\Shared\Kernel\Transfer\TransferInterface;

/**
 * @method \Spryker\Zed\Payment\Business\PaymentBusinessFactory getFactory()
 */
interface PaymentFacadeInterface
{
    /**
     * Specification:
     * - Finds available payment methods
     * - Runs filter plugins
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodsTransfer
     */
    public function getAvailableMethods(QuoteTransfer $quoteTransfer);

    /**
     * Specification:
     * - Check whether the given order has a payment method external selected.
     * - Terminates hook execution if not.
     * - Receives all the necessary information about the payment method external.
     * - Sends a request with all pre-selected quote fields using PaymentMethodTransfer.paymentAuthorizationEndpoint.
     * - If the response is free of errors, uses PaymentMethodTransfer.paymentAuthorizationEndpoint and response data to build a redirect URL.
     * - Updates CheckoutResponseTransfer with errors or the redirect URL according to response received.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return void
     */
    public function executeOrderPostSaveHook(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): void;

    /**
     * Specification:
     * - Requires PaymentMethodTransfer.labelName transfer field to be set.
     * - Requires PaymentMethodTransfer.groupName transfer field to be set.
     * - Creates payment provider if respective provider doesn't exist in DB
     * - Creates payment method if the payment method with provided key doesn't exist in DB.
     * - Updates payment method otherwise.
     * - Sets payment method `is_hidden` flag to false if it already exists.
     * - Returns PaymentMethod transfer filled with payment method data.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PaymentMethodAddedTransfer $paymentMethodAddedTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodTransfer
     */
    public function enablePaymentMethod(PaymentMethodAddedTransfer $paymentMethodAddedTransfer): PaymentMethodTransfer;

    /**
     * Specification:
     * - Requires PaymentMethodTransfer.labelName transfer field to be set.
     * - Requires PaymentMethodTransfer.groupName transfer field to be set.
     * - Uses the specified data to find a payment method.
     * - Sets payment method `is_hidden` flag to true.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PaymentMethodDeletedTransfer $paymentMethodDeletedTransfer
     *
     * @return void
     */
    public function disablePaymentMethod(PaymentMethodDeletedTransfer $paymentMethodDeletedTransfer): void;

    /**
     * Specification:
     * - Distributes total price to payment methods
     * - Calculates price to pay
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     *
     * @return void
     */
    public function recalculatePayments(CalculableObjectTransfer $calculableObjectTransfer);

    /**
     * Specification:
     * - Finds payment providers which has available payment methods for the given store.
     *
     * @api
     *
     * @param string $storeName
     *
     * @return \Generated\Shared\Transfer\PaymentProviderCollectionTransfer
     */
    public function getAvailablePaymentProvidersForStore(string $storeName): PaymentProviderCollectionTransfer;

    /**
     * Specification:
     * - Finds payment method by the provided id.
     *
     * @api
     *
     * @param int $idPaymentMethod
     *
     * @return \Generated\Shared\Transfer\PaymentMethodResponseTransfer
     */
    public function findPaymentMethodById(int $idPaymentMethod): PaymentMethodResponseTransfer;

    /**
     * Specification:
     * - Finds a payment method.
     * - Uses PaymentMethodTransfer.idPaymentMethod if set to filter payment methods.
     * - Uses PaymentMethodTransfer.paymentMethodKey if set to filter payment methods.
     * - Returns a payment method found using the provided filters.
     * - Returns NULL otherwise.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodTransfer|null
     */
    public function findPaymentMethod(PaymentMethodTransfer $paymentMethodTransfer): ?PaymentMethodTransfer;

    /**
     * Specification:
     * - Updates payment method in database using provided PaymentMethod transfer object data.
     * - Updates or creates payment method store relations using 'storeRelation' collection in the PaymentMethod transfer object.
     * - Returns PaymentMethodResponse transfer object.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodResponseTransfer
     */
    public function updatePaymentMethod(
        PaymentMethodTransfer $paymentMethodTransfer
    ): PaymentMethodResponseTransfer;

    /**
     * Specification:
     * - Checks if selected payment methods exist.
     * - Checks `QuoteTransfer.payments` and `QuoteTransfer.payment` for BC reasons.
     * - Returns `false` and add an error in case at least one of the payment methods
     *  does not exist or is not available for `QuoteTransfer`.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function isQuotePaymentMethodValid(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    ): bool;

    /**
     * Specification:
     * - Creates payment provider.
     * - Requires PaymentProviderTransfer.paymentProviderKey.
     * - Requires PaymentProviderTransfer.name.
     * - Creates payment methods if PaymentProviderTransfer.paymentMethods are provided.
     * - Requires PaymentMethodTransfer.paymentMethodKey.
     * - Requires PaymentMethodTransfer.name.
     * - Creates payment method store relations if PaymentMethodTransfer.storeRelation is provided.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PaymentProviderTransfer $paymentProviderTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentProviderResponseTransfer
     */
    public function createPaymentProvider(PaymentProviderTransfer $paymentProviderTransfer): PaymentProviderResponseTransfer;

    /**
     * Specification:
     * - Creates payment method.
     * - Requires PaymentMethodTransfer.idPaymentProvider.
     * - Requires PaymentMethodTransfer.paymentMethodKey.
     * - Requires PaymentMethodTransfer.name.
     * - Creates payment method store relations if PaymentMethodTransfer.storeRelation is provided.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodResponseTransfer
     */
    public function createPaymentMethod(PaymentMethodTransfer $paymentMethodTransfer): PaymentMethodResponseTransfer;

    /**
     * Specification:
     * - Deactivates payment method.
     * - Requires PaymentMethodTransfer.idPaymentMethod.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodResponseTransfer
     */
    public function deactivatePaymentMethod(PaymentMethodTransfer $paymentMethodTransfer): PaymentMethodResponseTransfer;

    /**
     * Specification:
     * - Activates payment method.
     * - Requires PaymentMethodTransfer.idPaymentMethod.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PaymentMethodTransfer $paymentMethodTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentMethodResponseTransfer
     */
    public function activatePaymentMethod(PaymentMethodTransfer $paymentMethodTransfer): PaymentMethodResponseTransfer;

    /**
     * Specification:
     * - Runs pre-check plugins
     *
     * @api
     *
     * @deprecated Will be removed without replacement.
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function checkoutPreCheck(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer);

    /**
     * Specification:
     * - Runs post-check plugins
     *
     * @api
     *
     * @deprecated Will be removed without replacement.
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return void
     */
    public function checkoutPostCheck(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponseTransfer);

    /**
     * Specification:
     *  - Returns payment method price to pay
     *
     * @api
     *
     * @deprecated Use QuoteTransfer.payments or OrderTransfer.payments instead to get amount per payment method.
     *
     * @param \Generated\Shared\Transfer\SalesPaymentTransfer $salesPaymentTransfer
     *
     * @return int
     */
    public function getPaymentMethodPriceToPay(SalesPaymentTransfer $salesPaymentTransfer);

    /**
     * Specification:
     *  - Populates order transfer with payment data
     *
     * @api
     *
     * @deprecated Use {@link \Spryker\Zed\SalesPayment\Business\SalesPaymentFacadeInterface::expandOrderWithPayments()} instead.
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function hydrateOrderPayments(OrderTransfer $orderTransfer);

    /**
     * Specification:
     * - Requires PaymentProviderTransfer.paymentProviderKey transfer field to be set.
     * - Returns a payment provider transfer found using PaymentProvider transfer.
     * - Returns NULL if payment provider is not found.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\PaymentProviderTransfer $paymentProviderTransfer
     *
     * @return \Generated\Shared\Transfer\PaymentProviderTransfer|null
     */
    public function findPaymentProvider(PaymentProviderTransfer $paymentProviderTransfer): ?PaymentProviderTransfer;

    /**
     * Specification:
     * - Creates sales payments
     *
     * @api
     *
     * @deprecated Use {@link \Spryker\Zed\SalesPayment\Business\SalesPaymentFacadeInterface::saveOrderPayments} instead.
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponse
     *
     * @return void
     */
    public function savePaymentForCheckout(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponse);

    /**
     * Specification:
     * - Uses OrderTransfer.orderReference, OrderTransfer.currencyIsoCode and order item ids to send the event.
     *
     * @api
     *
     * @param array $orderItemIds
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return void
     */
    public function sendEventPaymentCancelReservationPending(array $orderItemIds, OrderTransfer $orderTransfer): void;

    /**
     * Specification:
     * - Sends event if total count of order items above zero.
     * - Uses orderTransfer.orderReference, orderTransfer.currencyIsoCode, order item ids and total count to send the event.
     *
     * @api
     *
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
    ): void;

    /**
     * Specification:
     * - Sends event if total count of order items above zero.
     * - Uses orderTransfer.orderReference, orderTransfer.currencyIsoCode, order item ids and total count to send the event.
     * - Total items count will be a negative number.
     *
     * @api
     *
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
    ): void;

    /**
     * Specification:
     * - Checks store matching.
     * - Checks if the received event is one of the Preauthorized events (PaymentMessageOmsEventTriggerer::OMS_EVENT_TRANSFERS_APPLIED_FOR_ALL_ORDER_ITEMS_LIST).
     * - Triggers its own event for each received transfer from the PaymentMessageOmsEventTriggerer::SUPPORTED_ORDER_PAYMENT_EVENT_TRANSFERS_LIST.
     * - The first parameter is request transfer as provided by order payment event (e.g. PaymentCancelReservationFailedTransfer).
     *
     * @api
     *
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface $orderPaymentEventTransfer
     *
     * @return void
     */
    public function triggerPaymentMessageOmsEvent(TransferInterface $orderPaymentEventTransfer): void;

    /**
     * Specification:
     *
     * @api
     *
     * @param \Spryker\Shared\Kernel\Transfer\TransferInterface $transfer
     * @param string $eventName
     *
     * @return void
     */
    public function handleEventForOrderItems(TransferInterface $transfer, string $eventName): void;
}
