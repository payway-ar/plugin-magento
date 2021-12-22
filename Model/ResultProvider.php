<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Model;

use Prisma\Decidir\Gateway\Helper\DataReader;

/**
 * Validates response coming from the Gateway
 */
class ResultProvider
{
    /**
     * @var DataReader $reader
     */
    private $reader;

    /**
     * Result constructor.
     * @param DataReader $reader
     */
    public function __construct( DataReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @param array $validationSubject
     * @return array
     */
    public function normalizeResponse(array $validationSubject): array
    {
        $response = $this->reader->readResponse($validationSubject);

        if (isset($response['object'])) {
            $response = $response['object'];
        }

        return is_array($response) ? $response : $response->getDataField();

    }
}
