<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;
use Magento\Payment\Gateway\Validator\ResultInterface;
use Magento\Payment\Gateway\Validator\ResultInterfaceFactory;
use Magento\Setup\Exception;
use Prisma\Decidir\Gateway\Helper\DataReader;
use Psr\Log\InvalidArgumentException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;

/**
 * Validates response coming from the Gateway
 */
class ValidatorPool extends AbstractValidator
{
    /**
     * @var DataReader $reader
     */
    private $reader;
    /**
     * @var array $validators
     */
    private array $validators;
    /**
     * @var ManagerInterface $messageManager
     */
    private ManagerInterface $messageManager;

    /**
     * MainValidator constructor.
     * @param ResultInterfaceFactory $factory
     * @param DataReader $reader
     * @param array $validators
     * @param ManagerInterface $messageManager
     */
    public function __construct(
        ResultInterfaceFactory $factory,
        DataReader $reader,
        ManagerInterface $messageManager,
        array $validators

    ) {
        parent::__construct($factory);
        $this->reader = $reader;
        $this->messageManager = $messageManager;
        $this->validators = $validators;
    }

    /**
     * @param array $validationSubject
     * @return ResultInterface
     */
    public function validate(array $validationSubject): ResultInterface
    {
        $isValid = true;
        $response = $this->reader->readResponse($validationSubject);

        // @TODO create a result provider to avoid this type of nasty workarounds
        // depending on how it fails, SDK returns different signature
        // this normalizes that, use case: repeated token error
        if (isset($response['object'])) {
            $response = $response['object'];
        }

        $response = is_array($response) ? $response : $response->getDataField();

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
