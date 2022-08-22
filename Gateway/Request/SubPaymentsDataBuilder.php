<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Gateway\Request;

use Prisma\Payway\Gateway\Helper\DataReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Assigns the payments into the request
 */
class SubPaymentsDataBuilder implements BuilderInterface
{
    /**
     * @var string
     */

    const SUB_PAYMENTS = 'sub_payments';

    /**
     * @var DataReader
     */
    private $reader;

    /**
     * @param DataReader $reader
     */
    public function __construct(DataReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject): array
    {
        return [
            self::SUB_PAYMENTS => []
        ];
    }
}
