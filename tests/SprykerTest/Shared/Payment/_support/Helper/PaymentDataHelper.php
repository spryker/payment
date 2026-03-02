<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Shared\Payment\Helper;

use Codeception\Module;
use Generated\Shared\DataBuilder\AddPaymentMethodBuilder;
use Generated\Shared\DataBuilder\DeletePaymentMethodBuilder;
use Generated\Shared\DataBuilder\PaymentMethodBuilder;
use Generated\Shared\DataBuilder\PaymentProviderBuilder;
use Generated\Shared\DataBuilder\UpdatePaymentMethodBuilder;
use Generated\Shared\Transfer\AddPaymentMethodTransfer;
use Generated\Shared\Transfer\DeletePaymentMethodTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\PaymentProviderTransfer;
use Generated\Shared\Transfer\UpdatePaymentMethodTransfer;
use Orm\Zed\Payment\Persistence\SpyPaymentMethodQuery;
use Orm\Zed\Payment\Persistence\SpyPaymentMethodStoreQuery;
use Orm\Zed\Payment\Persistence\SpyPaymentProviderQuery;

class PaymentDataHelper extends Module
{
    public function ensurePaymentMethodTableIsEmpty(): void
    {
        SpyPaymentMethodStoreQuery::create()->deleteAll();
        SpyPaymentMethodQuery::create()->deleteAll();
    }

    public function ensurePaymentMethodStoreTableIsEmpty(): void
    {
        SpyPaymentMethodStoreQuery::create()->deleteAll();
    }

    public function ensurePaymentProviderTableIsEmpty(): void
    {
        SpyPaymentMethodStoreQuery::create()->deleteAll();
        SpyPaymentMethodQuery::create()->deleteAll();
        SpyPaymentProviderQuery::create()->deleteAll();
    }

    public function havePaymentMethodWithPaymentProviderPersisted(array $seed = []): PaymentMethodTransfer
    {
        $paymentProviderTransfer = $this->havePaymentProvider($seed[PaymentMethodTransfer::PAYMENT_PROVIDER] ?? $seed);

        $seed[PaymentMethodTransfer::PAYMENT_PROVIDER] = $paymentProviderTransfer;
        $seed[PaymentMethodTransfer::ID_PAYMENT_PROVIDER] = $paymentProviderTransfer->getIdPaymentProvider();

        return $this->havePaymentMethod($seed);
    }

    public function havePaymentProvider(array $override = []): PaymentProviderTransfer
    {
        $paymentProviderTransfer = (new PaymentProviderBuilder())->seed($override)->build();

        $paymentProviderEntity = SpyPaymentProviderQuery::create()
            ->filterByPaymentProviderKey($paymentProviderTransfer->getPaymentProviderKey())
            ->findOneOrCreate();

        $paymentProviderEntity->fromArray($paymentProviderTransfer->modifiedToArray());
        $paymentProviderEntity->save();

        $paymentProviderTransfer->setIdPaymentProvider($paymentProviderEntity->getIdPaymentProvider());

        return $paymentProviderTransfer;
    }

    public function havePaymentMethod(array $override = []): PaymentMethodTransfer
    {
        $paymentMethodTransfer = (new PaymentMethodBuilder())->seed($override)->build();
        $paymentMethodEntity = SpyPaymentMethodQuery::create()
            ->filterByPaymentMethodKey($paymentMethodTransfer->getPaymentMethodKey() ?? $paymentMethodTransfer->getMethodName())
            ->filterByName($paymentMethodTransfer->getName())
            ->findOneOrCreate();

        $modifiedPaymentMethodData = $paymentMethodTransfer->modifiedToArray();

        if (isset($modifiedPaymentMethodData['payment_method_app_configuration'])) {
            $modifiedPaymentMethodData['payment_method_app_configuration'] = json_encode($modifiedPaymentMethodData['payment_method_app_configuration']);
        }

        $paymentMethodEntity->setFkPaymentProvider($paymentMethodTransfer->getIdPaymentProvider());

        if ($paymentMethodTransfer->getPaymentProvider()) {
            $paymentMethodEntity->setGroupName($paymentMethodTransfer->getPaymentProvider()->getName());
        }

        $paymentMethodEntity->fromArray($modifiedPaymentMethodData);

        $paymentMethodEntity->save();

        $paymentMethodTransfer->setIdPaymentMethod($paymentMethodEntity->getIdPaymentMethod());
        $storeRelationTransfer = $paymentMethodTransfer->getStoreRelation();

        if (!$storeRelationTransfer) {
            return $paymentMethodTransfer;
        }

        foreach ($storeRelationTransfer->getIdStores() as $idStore) {
            SpyPaymentMethodStoreQuery::create()
                ->filterByFkPaymentMethod($paymentMethodTransfer->getIdPaymentMethod())
                ->filterByFkStore($idStore)
                ->findOneOrCreate()
                ->save();
        }

        return $paymentMethodTransfer;
    }

    public function findPaymentMethodById(int $idPaymentMethod): ?PaymentMethodTransfer
    {
        $paymentMethodEntity = SpyPaymentMethodQuery::create()
            ->findOneByIdPaymentMethod($idPaymentMethod);

        if (!$paymentMethodEntity) {
            return null;
        }

        $paymentMethodTransfer = (new PaymentMethodTransfer())->fromArray($paymentMethodEntity->toArray(), true);
        $paymentMethodTransfer->setIdPaymentProvider($paymentMethodEntity->getFkPaymentProvider());

        return $paymentMethodTransfer;
    }

    public function findPaymentMethod(PaymentMethodTransfer $paymentMethodTransfer): ?PaymentMethodTransfer
    {
        $paymentMethodEntity = SpyPaymentMethodQuery::create()
            ->filterByArray($paymentMethodTransfer->modifiedToArrayNotRecursiveCamelCased())
            ->findOne();

        if (!$paymentMethodEntity) {
            return null;
        }

        $paymentMethodTransfer->fromArray($paymentMethodEntity->toArray(), true);
        $paymentMethodTransfer->setIdPaymentProvider($paymentMethodEntity->getFkPaymentProvider());

        return $paymentMethodTransfer;
    }

    /**
     * @param array<mixed> $seedData
     * @param array<mixed> $messageAttributesSeedData
     *
     * @return \Generated\Shared\Transfer\AddPaymentMethodTransfer
     */
    public function haveAddPaymentMethodTransfer(array $seedData = [], array $messageAttributesSeedData = []): AddPaymentMethodTransfer
    {
        return (new AddPaymentMethodBuilder($seedData))
            ->withMessageAttributes($messageAttributesSeedData)
            ->build();
    }

    /**
     * @param array<mixed> $seedData
     *
     * @return \Generated\Shared\Transfer\UpdatePaymentMethodTransfer
     */
    public function haveUpdatePaymentMethodTransfer(array $seedData = []): UpdatePaymentMethodTransfer
    {
        return (new UpdatePaymentMethodBuilder($seedData))
            ->withMessageAttributes()
            ->build();
    }

    /**
     * @param array<mixed> $seedData
     *
     * @return \Generated\Shared\Transfer\DeletePaymentMethodTransfer
     */
    public function haveDeletePaymentMethodTransfer(array $seedData = []): DeletePaymentMethodTransfer
    {
        return (new DeletePaymentMethodBuilder($seedData))->build();
    }
}
