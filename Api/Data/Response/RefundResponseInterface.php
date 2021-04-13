<?php
/**
 *
 */
declare(strict_types=1);

namespace Prisma\Decidir\Api\Data\Response;

/**
 * Interface for Refund and Void response operations
 * @see https://decidirv2.api-docs.io/1.0/operaciones-sobre-transacciones-simples
 */
interface RefundResponseInterface
{
    const ID = 'id';

    const STATUS = 'status';

    const AMOUNT = 'amount';
}
