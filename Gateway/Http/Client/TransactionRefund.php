<?php
/**
 *
 *
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Http\Client;

use Prisma\Decidir\Api\Data\Request\RefundDataInterface;

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
