<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Payment;

use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\Payment\Dependency\Client\PaymentToZedRequestClientInterface;
use Spryker\Client\Payment\Dependency\External\PaymentToHttpClientAdapterInterface;
use Spryker\Client\Payment\Dependency\Service\PaymentToUtilEncodingServiceInterface;
use Spryker\Client\Payment\Executor\PaymentRequestExecutor;
use Spryker\Client\Payment\Executor\PaymentRequestExecutorInterface;
use Spryker\Client\Payment\Zed\PaymentStub;
use Spryker\Client\Payment\Zed\PaymentStubInterface;

/**
 * @method \Spryker\Client\Payment\PaymentConfig getConfig()
 */
class PaymentFactory extends AbstractFactory
{
    public function createZedStub(): PaymentStubInterface
    {
        return new PaymentStub(
            $this->getZedRequestClient(),
        );
    }

    public function createPaymentRequestExecutor(): PaymentRequestExecutorInterface
    {
        return new PaymentRequestExecutor(
            $this->getUtilEncodingService(),
            $this->getHttpClient(),
            $this->getConfig(),
        );
    }

    public function getUtilEncodingService(): PaymentToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(PaymentDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    public function getHttpClient(): PaymentToHttpClientAdapterInterface
    {
        return $this->getProvidedDependency(PaymentDependencyProvider::CLIENT_HTTP);
    }

    protected function getZedRequestClient(): PaymentToZedRequestClientInterface
    {
        /** @var \Spryker\Client\Payment\Dependency\Client\PaymentToZedRequestClientInterface $zedStub */
        $zedStub = $this->getProvidedDependency(PaymentDependencyProvider::CLIENT_ZED_REQUEST);

        return $zedStub;
    }
}
