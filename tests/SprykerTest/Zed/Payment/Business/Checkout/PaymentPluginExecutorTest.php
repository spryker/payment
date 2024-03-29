<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Payment\Business\Checkout;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\PaymentTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Payment\Business\Checkout\PaymentPluginExecutor;
use Spryker\Zed\Payment\Business\Order\SalesPaymentSaverInterface;
use Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPluginCollection;
use Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPluginCollectionInterface;
use Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPostCheckPluginInterface;
use Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPreCheckPluginInterface;
use Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutSaveOrderPluginInterface;
use Spryker\Zed\Payment\PaymentDependencyProvider;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Payment
 * @group Business
 * @group Checkout
 * @group PaymentPluginExecutorTest
 * Add your own group annotations below this line
 */
class PaymentPluginExecutorTest extends Unit
{
    /**
     * @var string
     */
    public const TEST_PROVIDER = 'Test';

    /**
     * @return void
     */
    public function testExecutePreCheckPluginPreCheckShouldTriggerTestPaymentPlugin(): void
    {
        /**
         * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPreCheckPluginInterface $preCheckPluginMock
         */
        $preCheckPluginMock = $this->createPreCheckPluginMock();
        $preCheckPluginMock->expects($this->once())->method('execute');

        $paymentPluginExecutor = $this->createPaymentPluginExecutor($preCheckPluginMock);
        $quoteTransfer = $this->createQuoteTransfer();
        $checkoutResponseTransfer = new CheckoutResponseTransfer();

        $paymentPluginExecutor->executePreCheckPlugin($quoteTransfer, $checkoutResponseTransfer);
    }

    /**
     * @return void
     */
    public function testExecuteOrderSaverPluginOrderSaverShouldTriggerTestPaymentPlugin(): void
    {
        /**
         * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutSaveOrderPluginInterface $orderSavePluginMock
         */
        $orderSavePluginMock = $this->createSavePluginMock();
        $orderSavePluginMock->expects($this->once())->method('execute');

        $paymentPluginExecutor = $this->createPaymentPluginExecutor(null, $orderSavePluginMock);
        $quoteTransfer = $this->createQuoteTransfer();
        $checkoutResponseTransfer = new CheckoutResponseTransfer();

        $paymentPluginExecutor->executeOrderSaverPlugin($quoteTransfer, $checkoutResponseTransfer);
    }

    /**
     * @return void
     */
    public function testExecutePostCheckPluginPostCheckShouldTriggerTestPaymentPlugin(): void
    {
        /**
         * @var \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPostCheckPluginInterface $postCheckoutPluginMock
         */
        $postCheckoutPluginMock = $this->createPostSavePluginMock();
        $postCheckoutPluginMock->expects($this->once())->method('execute');

        $paymentPluginExecutor = $this->createPaymentPluginExecutor(null, null, $postCheckoutPluginMock);
        $quoteTransfer = $this->createQuoteTransfer();
        $checkoutResponseTransfer = new CheckoutResponseTransfer();

        $paymentPluginExecutor->executePostCheckPlugin($quoteTransfer, $checkoutResponseTransfer);
    }

    /**
     * @param \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPreCheckPluginInterface|null $preCheckPluginMock
     * @param \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutSaveOrderPluginInterface|null $orderSavePluginMock
     * @param \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPostCheckPluginInterface|null $postCheckPluginMock
     *
     * @return \Spryker\Zed\Payment\Business\Checkout\PaymentPluginExecutor
     */
    protected function createPaymentPluginExecutor(
        $preCheckPluginMock = null,
        $orderSavePluginMock = null,
        $postCheckPluginMock = null
    ): PaymentPluginExecutor {
        $salesSaverMock = $this->createSalesSaverMock();

        $paymentPluginExecutor = new PaymentPluginExecutor(
            $this->createCheckoutPlugins(
                $preCheckPluginMock,
                $orderSavePluginMock,
                $postCheckPluginMock,
            ),
            $salesSaverMock,
        );

        return $paymentPluginExecutor;
    }

    /**
     * @param \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPreCheckPluginInterface|null $preCheckPluginMock
     * @param \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutSaveOrderPluginInterface|null $orderSavePluginMock
     * @param \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPostCheckPluginInterface|null $postCheckPluginMock
     *
     * @return \Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPluginCollectionInterface
     */
    protected function createCheckoutPlugins(
        $preCheckPluginMock = null,
        $orderSavePluginMock = null,
        $postCheckPluginMock = null
    ): CheckoutPluginCollectionInterface {
        $pluginCollection = new CheckoutPluginCollection();
        if ($preCheckPluginMock !== null) {
            $pluginCollection->add($preCheckPluginMock, static::TEST_PROVIDER, PaymentDependencyProvider::CHECKOUT_PRE_CHECK_PLUGINS);
        }
        if ($orderSavePluginMock !== null) {
            $pluginCollection->add($orderSavePluginMock, static::TEST_PROVIDER, PaymentDependencyProvider::CHECKOUT_ORDER_SAVER_PLUGINS);
        }
        if ($postCheckPluginMock !== null) {
            $pluginCollection->add($postCheckPluginMock, static::TEST_PROVIDER, PaymentDependencyProvider::CHECKOUT_POST_SAVE_PLUGINS);
        }

        return $pluginCollection;
    }

    /**
     * @return \Spryker\Zed\Payment\Business\Order\SalesPaymentSaverInterface
     */
    protected function createSalesSaverMock(): SalesPaymentSaverInterface
    {
        return $this->getMockBuilder(SalesPaymentSaverInterface::class)->getMock();
    }

    /**
     * @return \Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPreCheckPluginInterface
     */
    protected function createPreCheckPluginMock(): CheckoutPreCheckPluginInterface
    {
        return $this->getMockBuilder(CheckoutPreCheckPluginInterface::class)->getMock();
    }

    /**
     * @return \Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutSaveOrderPluginInterface
     */
    protected function createSavePluginMock(): CheckoutSaveOrderPluginInterface
    {
        return $this->getMockBuilder(CheckoutSaveOrderPluginInterface::class)->getMock();
    }

    /**
     * @return \Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPostCheckPluginInterface
     */
    protected function createPostSavePluginMock(): CheckoutPostCheckPluginInterface
    {
        return $this->getMockBuilder(CheckoutPostCheckPluginInterface::class)->getMock();
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuoteTransfer(): QuoteTransfer
    {
        $quoteTransfer = new QuoteTransfer();
        $paymentTransfer = new PaymentTransfer();
        $paymentTransfer->setPaymentProvider(static::TEST_PROVIDER);
        $quoteTransfer->setPayment($paymentTransfer);

        return $quoteTransfer;
    }
}
