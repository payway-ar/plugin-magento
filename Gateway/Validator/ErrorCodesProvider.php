<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Gateway\Validator;

use Prisma\Payway\Api\Data\ErrorCodesInterface;

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
