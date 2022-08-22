<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Gateway\Validator\Cybersource;

use Prisma\Payway\Api\Data\Validator\Cybersource\CybersourceValidatorInterface;
use Prisma\Payway\Api\Data\Validator\ValidatorInterface;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Prisma\Payway\Gateway\Helper\DataReader;
use Prisma\Payway\Gateway\Config\CybersourceConfig;

/**
 * Validates response from Cybersource
 *
 */
class RetailValidator extends AbstractValidator implements ValidatorInterface
{
    /**
     * @var string
     */
    const DEFAULT_ERROR_CODE = '000';

    /**
     * @var array[]
     */
    const CS_DECISION_SUCCESS_VALUES = [
            CybersourceValidatorInterface::DECISION_GREEN,
            CybersourceValidatorInterface::DECISION_YELLOW
        ];

    /**
     * @var DataReader $reader
     */
    private $reader;

    /**
     * @var CybersourceConfig $cybersourceConfig
     */
    private $cybersourceConfig;

    /**
     * MainValidator constructor.
     * @param ResultInterfaceFactory $factory
     * @param DataReader $reader
     * @param CybersourceConfig $cybersourceConfig
     */
    public function __construct(
        ResultInterfaceFactory $factory,
        DataReader $reader,
        CybersourceConfig $cybersourceConfig
    ) {
        parent::__construct($factory);
        $this->reader = $reader;
        $this->cybersourceConfig = $cybersourceConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $validationSubject) :ResultInterface
    {
        $isValid = true;
        $errorMessages = [];
        $errorCodes = [];

        if ($this->cybersourceConfig->isCsActive()) {
            $decision = $this->validateDecision($validationSubject);
            if ($decision['errors']) {
                $isValid = false;
                $errorMessages[] = $decision['description'];
                $errorCodes[] = $decision['reason_code'];
            }
        }
        return $this->createResult($isValid, $errorMessages, $errorCodes);
    }

    /**
     * @param $validationSubject
     * @return array
     */
    private function validateDecision($validationSubject): array
    {
        $decision['errors'] = false;
        $fraudStatusResult = $validationSubject['fraud_detection']['status'];
        // validate if transaction status and decision color are not valid in any combination
        if (
            isset($fraudStatusResult['decision'])
            && !in_array($fraudStatusResult['decision'], self::CS_DECISION_SUCCESS_VALUES)
        ) {
            $decision['errors'] = true;
            $decision['decision'] = $fraudStatusResult['decision'];
            $decision['description'] = $fraudStatusResult['description'];
            $decision['reason_code'] = $fraudStatusResult['reason_code'];

            return $decision;
        }

        return $decision;
    }
}
