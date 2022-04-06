<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment;

use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\LocaleTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Shared\Payment\PaymentConstants;
use Spryker\Zed\Kernel\AbstractBundleConfig;

class PaymentConfig extends AbstractBundleConfig
{
    /**
     * @uses \Spryker\Shared\Application\ApplicationConstants::BASE_URL_YVES
     *
     * @var string
     */
    protected const BASE_URL_YVES = 'APPLICATION:BASE_URL_YVES';

    /**
     * @var string
     */
    public const PAYMENT_FOREIGN_PROVIDER = 'foreignPayments';

    /**
     * Specification:
     * - Returns a map of the payment methods and state machine's processes names.
     *
     * @api
     *
     * @example The format of returned array is:
     * [
     *    'PAYMENT_METHOD_A' => 'StateMachineProcess01',
     *    'PAYMENT_METHOD_B' => 'StateMachineProcess02',
     * ]
     *
     * @return array<string, string>
     */
    public function getPaymentStatemachineMappings()
    {
        return $this->get(PaymentConstants::PAYMENT_METHOD_STATEMACHINE_MAPPING, []);
    }

    /**
     * @api
     *
     * @return string
     */
    public function getSuccessRoute(): string
    {
        return '/payment/order-success';
    }

    /**
     * @api
     *
     * @return string
     */
    public function getCancelRoute(): string
    {
        return '/payment/order-cancel';
    }

    /**
     * @api
     *
     * @return string
     */
    public function getCheckoutSummaryPageRoute(): string
    {
        return '/checkout/summary';
    }

    /**
     * @api
     *
     * @return string
     */
    public function getBaseUrlYves(): string
    {
        return $this->get(static::BASE_URL_YVES);
    }

    /**
     * @api
     *
     * @return string
     */
    public function getTenantIdentifier(): string
    {
        $beHostInsteadOfTenantIdentifier = getenv('SPRYKER_BE_HOST') !== false ? getenv('SPRYKER_BE_HOST') : 'TENANT_IDENTIFIER_UNDEFINED';

        return getenv('TENANT_IDENTIFIER') !== false ? getenv('TENANT_IDENTIFIER') : $beHostInsteadOfTenantIdentifier;
    }

    /**
     * @api
     *
     * @example
     * [
     *     QuoteTransfer::ORDER_REFERENCE => 'orderReference',
     *     QuoteTransfer::ITEMS => [
     *         ItemTransfer::NAME => 'itemName',
     *         ItemTransfer::ABSTRACT_SKU => 'abstractSku',
     *     ],
     * ]
     *
     * @return array<mixed>
     */
    public function getQuoteFieldsAllowedForSending(): array
    {
        return [
            QuoteTransfer::ORDER_REFERENCE => 'orderReference',
            QuoteTransfer::STORE => [
                StoreTransfer::NAME => 'storeName',
            ],
            QuoteTransfer::CUSTOMER => [
                CustomerTransfer::LOCALE => [
                    LocaleTransfer::LOCALE_NAME => 'localeName',
                ],
            ],
            QuoteTransfer::BILLING_ADDRESS => [
                AddressTransfer::ISO2_CODE => 'countryCode',
                AddressTransfer::FIRST_NAME => 'customerFirstName',
                AddressTransfer::LAST_NAME => 'customerLastName',
            ],
            QuoteTransfer::CURRENCY => [
                CurrencyTransfer::CODE => 'currencyCode',
            ],
            QuoteTransfer::PAYMENT => [
                PaymentTransfer::AMOUNT => 'grandTotal',
                PaymentTransfer::PAYMENT_METHOD => 'paymentMethod',
            ],
            QuoteTransfer::ITEMS => [
                ItemTransfer::ID_SALES_ORDER_ITEM => 'idSalesOrderItem',
                ItemTransfer::NAME => 'name',
            ],
        ];
    }
}
