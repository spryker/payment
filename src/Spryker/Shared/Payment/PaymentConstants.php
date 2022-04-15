<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\Payment;

/**
 * Declares global environment configuration keys. Do not use it for other class constants.
 */
interface PaymentConstants
{
    /**
     * @see \Spryker\Shared\Sales\SalesConstants::PAYMENT_METHOD_STATEMACHINE_MAPPING
     *
     * @api
     *
     * @var string
     */
    public const PAYMENT_METHOD_STATEMACHINE_MAPPING = 'PAYMENT_METHOD_STATEMACHINE_MAPPING';

    /**
     * @api
     *
     * @var string
     */
    public const OAUTH_PROVIDER_NAME_FOR_PAYMENT_AUTHORIZE = 'PAYMENT:OAUTH_PROVIDER_NAME_FOR_PAYMENT_AUTHORIZE';

    /**
     * @api
     *
     * @var string
     */
    public const OAUTH_GRANT_TYPE_FOR_PAYMENT_AUTHORIZE = 'PAYMENT:OAUTH_GRANT_TYPE_FOR_PAYMENT_AUTHORIZE';

    /**
     * @api
     *
     * @var string
     */
    public const OAUTH_OPTION_AUDIENCE_FOR_PAYMENT_AUTHORIZE = 'PAYMENT:OAUTH_OPTION_AUDIENCE_FOR_PAYMENT_AUTHORIZE';
}
