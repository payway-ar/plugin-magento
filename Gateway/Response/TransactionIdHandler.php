<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;
use Prisma\Payway\Gateway\Helper\DataReader;
use Prisma\Payway\Model\Config;

/**
 * Retrieves the payment transaction id from the response
 */
class TransactionIdHandler implements HandlerInterface
{
    /**
     * @var string
     */
    const FIELD_ID = 'id';

    /**
     * @var Config $config
     */
    private $config;

    /**
     * @var DataReader $reader
     */
    private $reader;

    /**
     * TransactionIdHandler constructor.
     * @param Config $config
     * @param DataReader $reader
     */
    public function __construct(
        Config $config,
        DataReader $reader
    ) {
        $this->config = $config;
        $this->reader = $reader;
    }

    /**
     * @param array $handlingSubject
     * @param array $response
     */
    public function handle(array $handlingSubject, array $response)
    {
        $object = $this->reader->readPayment($handlingSubject);

        /** @var Payment $payment */
        $payment = $object->getPayment();

        if ($payment instanceof Payment) {
            $paymentResponse = $this->reader->readTransaction($response);
            $data = $paymentResponse->getDataField();

            // @TODO ensure for all other Gateway Commands there's always a transaction id
            if (!isset($data[self::FIELD_ID])) {
                // request has failed given that
                // we don't need a transaction id on Payway side
                return;
            }

            $payment->setCcTransId($data[self::FIELD_ID]);

            /**
             * we don't have to set the last transaction id
             * Transaction Builder, will check there's a transaction_id configured
             * and create one (since there isn't), whilst assigns `setLastTransId`
             */
            $payment->setTransactionId($data['id']);
        }
    }
}
