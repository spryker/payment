<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment\Business\AccessToken;

use Generated\Shared\Transfer\AccessTokenRequestOptionsTransfer;
use Generated\Shared\Transfer\AccessTokenRequestTransfer;
use Generated\Shared\Transfer\AccessTokenResponseTransfer;
use Spryker\Shared\Log\LoggerTrait;
use Spryker\Zed\Payment\Dependency\Facade\PaymentToOauthClientFacadeInterface;
use Spryker\Zed\Payment\PaymentConfig;

class AccessTokenReader implements AccessTokenReaderInterface
{
    use LoggerTrait;

    /**
     * @var \Spryker\Zed\Payment\Dependency\Facade\PaymentToOauthClientFacadeInterface
     */
    protected $oauthClientFacade;

    /**
     * @var \Spryker\Zed\Payment\PaymentConfig
     */
    protected $paymentConfig;

    /**
     * @param \Spryker\Zed\Payment\Dependency\Facade\PaymentToOauthClientFacadeInterface $oauthClientFacade
     * @param \Spryker\Zed\Payment\PaymentConfig $paymentConfig
     */
    public function __construct(
        PaymentToOauthClientFacadeInterface $oauthClientFacade,
        PaymentConfig $paymentConfig
    ) {
        $this->oauthClientFacade = $oauthClientFacade;
        $this->paymentConfig = $paymentConfig;
    }

    /**
     * @return \Generated\Shared\Transfer\AccessTokenResponseTransfer
     */
    public function requestAccessToken(): AccessTokenResponseTransfer
    {
        $accessTokenRequestOptions = (new AccessTokenRequestOptionsTransfer())
            ->setAudience($this->paymentConfig->getOauthOptionAudienceForPaymentAuthorize());

        $accessTokenRequestTransfer = (new AccessTokenRequestTransfer())
            ->setGrantType($this->paymentConfig->getOauthGrantTypeForPaymentAuthorize())
            ->setProviderName($this->paymentConfig->getOauthProviderNameForPaymentAuthorize())
            ->setAccessTokenRequestOptions($accessTokenRequestOptions);

        $oauthClientResponseTransfer = $this->oauthClientFacade->getAccessToken($accessTokenRequestTransfer);

        if (!$oauthClientResponseTransfer->getIsSuccessful()) {
            $this->getLogger()->error(sprintf(
                'Reason: %s; Description: %s.',
                $oauthClientResponseTransfer->getAccessTokenErrorOrFail()->getError(),
                $oauthClientResponseTransfer->getAccessTokenErrorOrFail()->getErrorDescription(),
            ));
        }

        return $oauthClientResponseTransfer;
    }
}
