<?php
/**
 *
 *
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Http\Client;

/**
 * Executes a Refund Gateway operation
 */
class TransactionVoid extends AbstractTransaction
{
    /**
     * @inheritdoc
     */
    protected function process(array $data)
    {
        return $this->adapter->void(
            $data['transaction_id'],
            $data
        );
    }
}
