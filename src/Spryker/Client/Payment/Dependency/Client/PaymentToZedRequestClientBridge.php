<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Payment\Dependency\Client;

use Spryker\Shared\Kernel\Transfer\TransferInterface;

class PaymentToZedRequestClientBridge implements PaymentToZedRequestClientInterface
{
    /**
     * @var \Spryker\Client\ZedRequest\ZedRequestClientInterface ZedRequestClientInterface
     */
    protected $zedRequestClient;

    /**
     * @param \Spryker\Client\ZedRequest\ZedRequestClientInterface $zedRequestClient
     */
    public function __construct($zedRequestClient)
    {
        $this->zedRequestClient = $zedRequestClient;
    }

    public function call(string $url, TransferInterface $object, ?int $timeoutInSeconds = null): TransferInterface
    {
        return $this->zedRequestClient->call($url, $object, $timeoutInSeconds);
    }
}
