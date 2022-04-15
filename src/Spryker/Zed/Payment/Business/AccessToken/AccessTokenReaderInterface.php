<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment\Business\AccessToken;

use Generated\Shared\Transfer\AccessTokenResponseTransfer;
use Generated\Shared\Transfer\StoreTransfer;

interface AccessTokenReaderInterface
{
    /**
     * @return \Generated\Shared\Transfer\AccessTokenResponseTransfer
     */
    public function requestAccessToken(): AccessTokenResponseTransfer;
}
