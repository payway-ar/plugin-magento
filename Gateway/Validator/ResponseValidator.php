<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Validator;

use Decidir\Payment\PaymentResponse;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Prisma\Decidir\Gateway\Helper\DataReader;
use Psr\Log\InvalidArgumentException;

/**
 * Validates response coming from the Gateway
 */
class ResponseValidator extends AbstractValidator
{
    /**
     * @var DataReader $reader
     */
    private $reader;

    /**
     * @var ErrorCodesProvider $errorCodeProvider
     */
    private $errorCodesProvider;

    /**
     * <code>
     * 400  malformed_request_error Error en el armado del json
     * 401  authentication_error    ApiKey Inválido
     * 402  invalid_request_error   Error por datos inválidos
     * 404  not_found_error         Error con datos no encontrados
     * 409  api_error               Error inesperado en la API REST
     * </code>
     *
     * @var array
     */
    const ERROR_CODES = [
        'unknown_error' => 0,
        'malformed_request_error' => 400,
        'authentication_error' => 401,
        'invalid_request_error' => 402,
        'not_found_error' => 404,
        'api_error' => 409
    ];

    /**
     * ResponseValidator constructor.
     * @param ResultInterfaceFactory $factory
     * @param DataReader $reader
     * @param ErrorCodesProvider $errorProvider
     */
    public function __construct(
        ResultInterfaceFactory $factory,
        DataReader $reader,
        ErrorCodesProvider $errorProvider
    ) {
        parent::__construct($factory);
        $this->reader = $reader;
        $this->errorCodesProvider = $errorProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $validationSubject): ResultInterface
    {

        $isValid = true;
        $errorMessages = [];
        $errorCodes = [];

        // @TODO add validation when response does not contain anything else than a message

        // append the rest of the incoming error data
        if (isset($validationSubject['validation_errors'])) {
            // SDK does not returns an object when request fails
            // if `error_type` is present request has failed
            $isValid = false;
            $errorMessages[] = 'Gateway Error';
            $errorMessages[] .= ' ' . json_encode($validationSubject['validation_errors']);

            // validate first position due SDK response format
            $errorCodes[] = isset($validationSubject['validation_errors'][0]['code'])
            ? $validationSubject['validation_errors'][0]['code']
            : self::ERROR_CODES['unknown_error'];

        }

        return $this->createResult($isValid, $errorMessages, $errorCodes);
    }

}
