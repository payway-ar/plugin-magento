<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Gateway\Http\Client;

use Prisma\Payway\Api\Data\Request\RefundDataInterface;

/**
 * Executes a Refund Gateway operation
 */
class TransactionRefund extends AbstractTransaction
{
    /**
     * @inheritdoc
     */
    protected function process(array $data)
    {
        $amount = $data[RefundDataInterface::FIELD_AMOUNT];

        return $this->adapter->refund(
            $data[RefundDataInterface::FIELD_ID],
            [RefundDataInterface::FIELD_AMOUNT => $amount]
        );
    }
}
