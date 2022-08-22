<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Prisma\Payway\Gateway\Helper\DataReader;

/**
 * Saves response body
 */
class DebugBodyHandler implements HandlerInterface
{
    /**
     * @var DataReader $reader
     */
    private $reader;

    /**
     * DebugBodyHandler constructor.
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

        /** @var OrderPaymentInterface $payment */
        $payment = $object->getPayment();

        if ($payment instanceof OrderPaymentInterface) {
            $paymentResponse = $this->reader->readTransaction($response);

            $payment->setCcDebugResponseBody(
                json_encode($paymentResponse->getDataField())
            );
        }
    }
}
