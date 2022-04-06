<?php


/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment\Business\Listener;

use Spryker\Shared\Kernel\Transfer\TransferInterface;

interface PaymentEventTypeListenerInterface
{
    /**
     * @param \Generated\Shared\Transfer\OrderPaymentEventTransfer $transfer
     * @param string $eventName
     *
     * @return void
     */
    public function handle(TransferInterface $transfer, string $eventName): void;
}
