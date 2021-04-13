<?php
/**
 *
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Model\Order\Payment;
use Prisma\Decidir\Gateway\Helper\DataReader;
use Prisma\Decidir\Model\Config;

/**
 * Retrieves the payment transaction id from the response
 */
class TransactionIdHandler implements HandlerInterface
{
    const FIELD_ID = 'id';

    /**
     * @var Config $config
     */
    private $config;

    /**
     * @var DataReader $reader
     */
    private $reader;

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
                // we don't need a transaction id on Decidir side
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
