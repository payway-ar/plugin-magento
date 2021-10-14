<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Prisma\Decidir\Gateway\Helper\DataReader;

/**
 * Validates presence of an exception within the response result
 *
 * @see \Prisma\Decidir\Gateway\Http\Client\AbstractTransaction::placeRequest()
 */
class GeneralResponseValidator extends AbstractValidator
{
    /**
     * @var DataReader $reader
     */
    private $reader;

    /**
     * GeneralResponseValidator constructor.
     * @param ResultInterfaceFactory $resultFactory
     * @param DataReader $reader
     */
    public function __construct(
        ResultInterfaceFactory $resultFactory,
        DataReader $reader
    ) {
        parent::__construct($resultFactory);
        $this->reader = $reader;
    }

    /**
     * @param array $validationSubject
     * @return ResultInterface
     */
    public function validate(array $validationSubject): ResultInterface
    {
        $response = $this->reader->readResponse($validationSubject);

        $isValid = true;
        $errorMessages = [];

        foreach ($this->getResponseValidators() as $validator) {
            $validationResult = $validator($response);

            if (!$validationResult[0]) {
                $isValid = $validationResult[0];
                $errorMessages[] = $validationResult[1];
            }
        }

        return $this->createResult($isValid, $errorMessages);
    }

    /**
     * @return array
     */
    protected function getResponseValidators(): array
    {
        return [
            static function ($response) {
                return [
                    !key_exists('exception', $response),
                    __('Error while executing the operation')
                ];
            }
        ];
    }
}
