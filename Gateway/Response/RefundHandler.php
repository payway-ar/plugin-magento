<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Prisma\Decidir\Gateway\Helper\DataReader;
use Magento\Sales\Model\Order\Payment;

class RefundHandler implements HandlerInterface
{
    /**
     * @var DataReader
     */
    protected $reader;

    /**
     * @param DataReader $reader
     */
    public function __construct(
        DataReader $reader
    ) {
        $this->reader = $reader;
    }

    /**
     * @param array $handlingSubject
     * @param array $response
     */
    public function handle(array $handlingSubject, array $response)
    {
        $object = $this->reader->readPayment($handlingSubject);
        $orderPayment = $object->getPayment();

        if ($orderPayment instanceof Payment) {
            $transaction = $this->reader->readTransaction($response);
            $data = $transaction->getDataField();

//            $orderPayment->setTransactionId($data['id']);
//            $orderPayment->setIsTransactionClosed(true);
        }
    }
}
