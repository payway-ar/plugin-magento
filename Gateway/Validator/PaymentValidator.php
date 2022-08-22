<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Gateway\Validator;

use Prisma\Payway\Api\Data\Validator\ValidatorInterface;
use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Prisma\Payway\Gateway\Helper\DataReader;
use Psr\Log\InvalidArgumentException;

/**
 * Class PaymentValidator
 *
 */
class PaymentValidator extends AbstractValidator implements ValidatorInterface
{
    /**
     * @var string
     */
    const DEFAULT_ERROR_CODE = '000';

    /**
     * @var DataReader $reader
     */
    private $reader;

    /**
     * MainValidator constructor.
     * @param ResultInterfaceFactory $factory
     * @param DataReader $reader
     */
    public function __construct(
        ResultInterfaceFactory $factory,
        DataReader $reader

    ) {
        parent::__construct($factory);
        $this->reader = $reader;
    }

    /**
     * {@inheritdoc}
     */
    public function validate(array $validationSubject) :ResultInterface
    {

        // TODO: Implement validate() method.
        $isValid = true;
        $errorMessages = [];
        $errorCodes = [];

        if (isset($validationSubject['status_details']['error']['type'])) {
            $isValid = false;
            $errorMessages[] = $validationSubject['status_details']['error']['reason']['description'];
            $errorCodes[] = $validationSubject['status_details']['error']['reason']['id'];
        }
        return $this->createResult($isValid, $errorMessages, $errorCodes);
    }

}
