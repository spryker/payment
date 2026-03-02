<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment\Business\PaymentProviderValidator;

use Generated\Shared\Transfer\PaymentProviderCollectionResponseTransfer;

interface PaymentProviderValidatorInterface
{
    public function validate(PaymentProviderCollectionResponseTransfer $paymentProviderCollectionResponseTransfer): PaymentProviderCollectionResponseTransfer;
}
