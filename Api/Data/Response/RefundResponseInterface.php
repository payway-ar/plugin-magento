<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Api\Data\Response;

/**
 * Interface for Refund and Void response operations
 * @see https://decidirv2.api-docs.io/1.0/operaciones-sobre-transacciones-simples
 */
interface RefundResponseInterface
{
    /**
     * @var string
     */
    const ID = 'id';

    /**
     * @var string
     */
    const STATUS = 'status';

    /**
     * @var string
     */
    const AMOUNT = 'amount';
}
