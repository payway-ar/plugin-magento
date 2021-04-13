<?php
/**
 *
 *
 */
declare(strict_types=1);

namespace Prisma\Decidir\Api\Data\Request;

/**
 * Represents payload for a Refund request
 * @see https://decidirv2.api-docs.io/1.0/operaciones-sobre-transacciones-simples
 */
interface RefundDataInterface
{
    /**
     * @var string
     */
    const FIELD_ID = 'payment_id';

    /**
     * @var string
     */
    const FIELD_AMOUNT = 'amount';
}
