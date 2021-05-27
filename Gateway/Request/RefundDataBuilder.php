<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Request;

use InvalidArgumentException;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Magento\Sales\Model\Order\Payment;
use Prisma\Decidir\Api\Data\Request\RefundDataInterface;
use Prisma\Decidir\Gateway\Helper\DataReader;
use Psr\Log\LoggerInterface;

/**
 * Class RefundDataBuilder
 *
 */
class RefundDataBuilder implements BuilderInterface
{
    /**
     * @var DataReader $reader
     */
    private $reader;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     *
     * @param DataReader $reader
     * @param LoggerInterface $logger
     */
    public function __construct(
        DataReader $reader,
        LoggerInterface $logger
    ) {
        $this->reader = $reader;
        $this->logger = $logger;
    }

    /**
     * @param array $buildSubject
     * @return array
     */
    public function build(array $buildSubject)
    {
        $object = $this->reader->readPayment($buildSubject);

        /** @var Payment $payment */
        $payment = $object->getPayment();

        $amount = null;

        try {
            $amount = $this->reader->readAmount($buildSubject);
        } catch (InvalidArgumentException $e) {
            $this->logger->critical(__METHOD__);
            $this->logger->critical($e->getMessage());
        }

        /**
         * We should remember that Payment sets Capture txn id of current Invoice into ParentTransactionId Field
         * We should also support previous implementations -
         * and cut off '-refund' postfix from transaction ID to support backward compatibility
         */
        $id = str_replace(
            '-' . TransactionInterface::TYPE_REFUND,
            '',
            $payment->getTransactionId()
        );

        return [
            RefundDataInterface::FIELD_ID => $id,
            RefundDataInterface::FIELD_AMOUNT => $amount
        ];
    }
}
