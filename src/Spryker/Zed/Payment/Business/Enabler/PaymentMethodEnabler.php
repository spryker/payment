<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment\Business\Enabler;

use Generated\Shared\Transfer\PaymentMethodAddedTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\PaymentProviderTransfer;
use Spryker\Zed\Payment\Business\Generator\PaymentMethodKeyGeneratorInterface;
use Spryker\Zed\Payment\Business\Mapper\PaymentMethodEventMapperInterface;
use Spryker\Zed\Payment\Business\Method\PaymentMethodUpdaterInterface;
use Spryker\Zed\Payment\Business\Writer\PaymentWriterInterface;
use Spryker\Zed\Payment\Dependency\Facade\PaymentToStoreReferenceFacadeInterface;
use Spryker\Zed\Payment\Persistence\PaymentRepositoryInterface;

class PaymentMethodEnabler implements PaymentMethodEnablerInterface
{
    /**
     * @var \Spryker\Zed\Payment\Persistence\PaymentRepositoryInterface
     */
    protected $paymentRepository;

    /**
     * @var \Spryker\Zed\Payment\Business\Writer\PaymentWriterInterface
     */
    protected $paymentWriter;

    /**
     * @var \Spryker\Zed\Payment\Business\Method\PaymentMethodUpdaterInterface
     */
    protected $paymentMethodUpdater;

    /**
     * @var \Spryker\Zed\Payment\Business\Generator\PaymentMethodKeyGeneratorInterface
     */
    protected $paymentMethodKeyGenerator;

    /**
     * @var \Spryker\Zed\Payment\Business\Mapper\PaymentMethodEventMapperInterface
     */
    protected PaymentMethodEventMapperInterface $paymentMethodEventMapper;

    /**
     * @var \Spryker\Zed\Payment\Dependency\Facade\PaymentToStoreReferenceFacadeInterface
     */
    protected PaymentToStoreReferenceFacadeInterface $storeReferenceFacade;

    /**
     * @param \Spryker\Zed\Payment\Persistence\PaymentRepositoryInterface $paymentRepository
     * @param \Spryker\Zed\Payment\Business\Writer\PaymentWriterInterface $paymentWriter
     * @param \Spryker\Zed\Payment\Business\Method\PaymentMethodUpdaterInterface $paymentMethodUpdater
     * @param \Spryker\Zed\Payment\Business\Generator\PaymentMethodKeyGeneratorInterface $paymentMethodKeyGenerator
     * @param \Spryker\Zed\Payment\Business\Mapper\PaymentMethodEventMapperInterface $paymentMethodEventMapper
     * @param \Spryker\Zed\Payment\Dependency\Facade\PaymentToStoreReferenceFacadeInterface $storeReferenceFacade
     */
    public function __construct(
        PaymentRepositoryInterface $paymentRepository,
        PaymentWriterInterface $paymentWriter,
        PaymentMethodUpdaterInterface $paymentMethodUpdater,
        PaymentMethodKeyGeneratorInterface $paymentMethodKeyGenerator,
        PaymentMethodEventMapperInterface $paymentMethodEventMapper,
        PaymentToStoreReferenceFacadeInterface $storeReferenceFacade
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->paymentWriter = $paymentWriter;
        $this->paymentMethodUpdater = $paymentMethodUpdater;
        $this->paymentMethodKeyGenerator = $paymentMethodKeyGenerator;
        $this->paymentMethodEventMapper = $paymentMethodEventMapper;
        $this->storeReferenceFacade = $storeReferenceFacade;
    }




}
