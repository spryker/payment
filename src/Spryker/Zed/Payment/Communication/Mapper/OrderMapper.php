<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Payment\Communication\Mapper;

use Generated\Shared\Transfer\OrderTransfer;
use Orm\Zed\Sales\Persistence\SpySalesOrder;

class OrderMapper implements OrderMapperInterface
{
    public function mapSalesOrderEntityToSalesOrderTransfer(
        SpySalesOrder $orderSales,
        OrderTransfer $orderTransfer
    ): OrderTransfer {
        return $orderTransfer->fromArray($orderSales->toArray(), true);
    }
}
