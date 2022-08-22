<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Api\Data;

/**
 * Gateway errors
 */
interface ErrorCodesInterface
{
    /** @var int */
    const CODE_UNKNOWN = 0;

    /** @var int */
    const CODE_MALFORMED_REQUEST = 400;

    /** @var int */
    const CODE_AUTHENTICATION = 401;

    /** @var int */
    const CODE_INVALID_REQUEST = 402;

    /** @var int */
    const CODE_NOT_FOUND = 404;

    /** @var int */
    const CODE_API_ERROR = 409;

    /** @var string  */
    const KEY_UNKNOWN = 'unknown_error';

    /** @var string  */
    const KEY_MALFORMED_REQUEST = 'malformed_request_error';

    /** @var string  */
    const KEY_AUTHENTICATION = 'authentication_error';

    /** @var string  */
    const KEY_INVALID_REQUEST = 'invalid_request_error';

    /** @var string  */
    const KEY_NOT_FOUND = 'not_found_error';

    /** @var string  */
    const KEY_API_ERROR = 'api_error';

    /**
     *
     * @var int[]
     */
    const ERROR_CODES = [
        self::CODE_UNKNOWN,
        self::CODE_MALFORMED_REQUEST,
        self::CODE_AUTHENTICATION,
        self::CODE_INVALID_REQUEST,
        self::CODE_NOT_FOUND,
        self::CODE_API_ERROR
    ];

    /** @var string[] */
    const ERROR_KEYS = [
        self::KEY_UNKNOWN,
        self::KEY_MALFORMED_REQUEST,
        self::KEY_AUTHENTICATION,
        self::KEY_INVALID_REQUEST,
        self::KEY_NOT_FOUND,
        self::KEY_API_ERROR
    ];

    /**
     * Returns list of error codes with their matching Gateway error key
     *
     * Method provided for future implementation.
     * Currently they are matched by `error_type`
     * because SDK does not contain Http error codes
     *
     * @return array
     */
    public function getErrorCodes(): array;
}
