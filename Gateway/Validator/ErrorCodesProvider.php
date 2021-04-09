<?php
/**
 *
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Validator;

use Prisma\Decidir\Api\Data\ErrorCodesInterface;

/**
 * Provides a list of errors
 */
class ErrorCodesProvider implements ErrorCodesInterface
{
    /**
     * @inheritDoc
     */
    public function getErrorCodes(): array
    {
        return self::ERROR_CODES;
    }
}
