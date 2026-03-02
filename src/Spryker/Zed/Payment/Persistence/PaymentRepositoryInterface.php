<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment\Persistence;

use Generated\Shared\Transfer\PaymentMethodCollectionTransfer;
use Generated\Shared\Transfer\PaymentMethodCriteriaTransfer;
use Generated\Shared\Transfer\PaymentMethodsTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\PaymentProviderCollectionTransfer;
use Generated\Shared\Transfer\PaymentProviderCriteriaTransfer;
use Generated\Shared\Transfer\PaymentProviderTransfer;
use Generated\Shared\Transfer\StoreRelationTransfer;

interface PaymentRepositoryInterface
{
    public function findPaymentMethodById(int $idPaymentMethod): ?PaymentMethodTransfer;

    public function findPaymentMethod(PaymentMethodTransfer $paymentMethodTransfer): ?PaymentMethodTransfer;

    public function getStoreRelationByIdPaymentMethod(int $idPaymentMethod): StoreRelationTransfer;

    public function getAvailablePaymentProvidersForStore(string $storeName): PaymentProviderCollectionTransfer;

    public function getPaymentMethodsWithStoreRelation(): PaymentMethodsTransfer;

    public function findPaymentProviderByKey(string $paymentProviderKey): ?PaymentProviderTransfer;

    public function getPaymentProviderCollection(PaymentProviderCriteriaTransfer $paymentProviderCriteriaTransfer): PaymentProviderCollectionTransfer;

    public function getPaymentMethodCollection(PaymentMethodCriteriaTransfer $paymentMethodCriteriaTransfer): PaymentMethodCollectionTransfer;

    public function hasPaymentMethod(PaymentMethodCriteriaTransfer $paymentMethodCriteriaTransfer): bool;

    public function hasPaymentProvider(PaymentProviderCriteriaTransfer $paymentProviderCriteriaTransfer): bool;
}
