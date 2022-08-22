<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Gateway\Helper;

use Decidir\Data\Response;
use Decidir\Payment\PaymentResponse;
use InvalidArgumentException;
use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Data\PaymentDataObjectInterface;

/**
 * Wrapper to interact with the data response layer
 */
class DataReader
{
    /**
     * Wraps implementation of readResponseInvalidArgumentException
     *
     * @param array $subject
     * @return array
     */
    public function readResponse(array $subject): array
    {
        return SubjectReader::readResponse($subject);
    }

    /**
     * Reads response object from subject
     *
     * @param array $subject
     * @return object
     */
    public function readResponseObject(array $subject)
    {
        if (!isset($subject['object']) || !is_object($subject['object'])) {
            throw new InvalidArgumentException('Response object does not exist');
        }

        return $subject['object'];
    }

    /**
     * Reads payment from subject
     *
     * @param array $subject
     * @return PaymentDataObjectInterface
     */
    public function readPayment(array $subject): PaymentDataObjectInterface
    {
        return SubjectReader::readPayment($subject);
    }

    /**
     * @param array $subject
     * @return Response
     */
    public function readTransaction(array $subject): Response
    {
        $response = $this->readResponseObject($subject);

        if (!$response instanceof Response
            && !$response->getDataField()
        ) {
            throw new InvalidArgumentException('Response object is not a class \Decidir\Data\Response.');
        }

        return $response;
    }

    /**
     * Reads amount from subject
     *
     * @param array $subject
     * @return mixed
     */
    public function readAmount(array $subject)
    {
        return SubjectReader::readAmount($subject);
    }
}
