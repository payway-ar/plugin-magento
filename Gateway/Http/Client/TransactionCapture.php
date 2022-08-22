<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Gateway\Http\Client;

/**
 * Execute a Capture gateway command
 */
class TransactionCapture extends AbstractTransaction
{
    /**
     * @inheritdoc
     */
    protected function process(array $data)
    {
        // @TODO: implement Capture command
        $operationId = '';
        return $this->adapter->capture($operationId, $data);
    }
}
