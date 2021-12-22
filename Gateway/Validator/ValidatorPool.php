<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Prisma\Decidir\Model\ResultProvider;

/**
 * Validates response coming from the Gateway
 */
class ValidatorPool extends AbstractValidator
{
    /**
     * @var ResultProvider $resultProvider
     */
    private $resultProvider;

    /**
     * @var array $validators
     */
    private array $validators;


    /**
     * MainValidator constructor.
     * @param ResultInterfaceFactory $factory
     * @param ResultProvider $resultProvider
     * @param array $validators
     */
    public function __construct(
        ResultInterfaceFactory $factory,
        ResultProvider $resultProvider,
        array $validators

    ) {
        parent::__construct($factory);
        $this->resultProvider = $resultProvider;
        $this->validators = $validators;
    }

    /**
     * @param array $validationSubject
     * @return ResultInterface
     */
    public function validate(array $validationSubject): ResultInterface
    {
        // depending on how it fails, SDK returns different signature
        // this normalizes that, use case: repeated token error
        $response = $this->resultProvider->normalizeResponse($validationSubject);
        $isValid = true;

        foreach ($this->validators as $validator) {
            $result = $validator->validate($response);
            // return result to show mapped error messages at checkout
            if (!$result->isValid()) {
                return $result;
            }
        }
        // return valid result if no errors
        return $this->createResult($isValid);

    }
}
