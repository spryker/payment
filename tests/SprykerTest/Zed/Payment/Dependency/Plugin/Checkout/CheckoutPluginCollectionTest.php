<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Payment\Dependency\Plugin\Checkout;

use Codeception\Test\Unit;
use Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPluginCollection;
use Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPluginCollectionInterface;
use Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPluginInterface;
use Spryker\Zed\Payment\Exception\CheckoutPluginNotFoundException;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Payment
 * @group Dependency
 * @group Plugin
 * @group Checkout
 * @group CheckoutPluginCollectionTest
 * Add your own group annotations below this line
 */
class CheckoutPluginCollectionTest extends Unit
{
    /**
     * @var string
     */
    public const PROVIDER = 'provider';

    /**
     * @var string
     */
    public const PLUGIN_TYPE = 'plugin type';

    public function testAddShouldReturnInstance(): void
    {
        $checkoutPluginCollection = new CheckoutPluginCollection();
        $pluginMock = $this->getPluginMock();
        $result = $checkoutPluginCollection->add($pluginMock, static::PROVIDER, static::PLUGIN_TYPE);

        $this->assertInstanceOf(CheckoutPluginCollectionInterface::class, $result);
    }

    public function testHasShouldReturnFalse(): void
    {
        $checkoutPluginCollection = new CheckoutPluginCollection();

        $this->assertFalse($checkoutPluginCollection->has(static::PROVIDER, static::PLUGIN_TYPE));
    }

    public function testHasShouldReturnTrue(): void
    {
        $checkoutPluginCollection = new CheckoutPluginCollection();
        $pluginMock = $this->getPluginMock();
        $checkoutPluginCollection->add($pluginMock, static::PROVIDER, static::PLUGIN_TYPE);

        $this->assertTrue($checkoutPluginCollection->has(static::PROVIDER, static::PLUGIN_TYPE));
    }

    public function testGetShouldReturnPluginForGivenProviderAndPluginType(): void
    {
        $checkoutPluginCollection = new CheckoutPluginCollection();
        $pluginMock = $this->getPluginMock();
        $checkoutPluginCollection->add($pluginMock, static::PROVIDER, static::PLUGIN_TYPE);
        $result = $checkoutPluginCollection->get(static::PROVIDER, static::PLUGIN_TYPE);

        $this->assertSame($pluginMock, $result);
    }

    public function testGetShouldThrowExceptionIfProviderNotFound(): void
    {
        $checkoutPluginCollection = new CheckoutPluginCollection();
        $pluginMock = $this->getPluginMock();
        $checkoutPluginCollection->add($pluginMock, static::PROVIDER, static::PLUGIN_TYPE);
        $this->expectException(CheckoutPluginNotFoundException::class);
        $this->expectExceptionMessage('Could not find any plugin for "unknown" provider. You need to add the needed plugins within your DependencyInjector.');

        $checkoutPluginCollection->get('unknown', static::PLUGIN_TYPE);
    }

    public function testGetShouldThrowExceptionIfPluginTypeNotFound(): void
    {
        $checkoutPluginCollection = new CheckoutPluginCollection();
        $pluginMock = $this->getPluginMock();
        $checkoutPluginCollection->add($pluginMock, static::PROVIDER, static::PLUGIN_TYPE);
        $this->expectException(CheckoutPluginNotFoundException::class);
        $this->expectExceptionMessage('Could not find "unknown" plugin type for "provider" provider. You need to add the needed plugins within your DependencyInjector.');

        $checkoutPluginCollection->get(static::PROVIDER, 'unknown');
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPluginInterface
     */
    private function getPluginMock(): CheckoutPluginInterface
    {
        return $this->getMockBuilder(CheckoutPluginInterface::class)->getMock();
    }
}
