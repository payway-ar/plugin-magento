<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Prisma\Payway\Gateway\Helper\DataReader;
use Prisma\Payway\Model\Config;

/**
 * Extracts Credit Card code from the transaction response
 */
class CreditCardHandler implements HandlerInterface
{
    /**
     * @var string
     */
    const PAYMENT_METHOD_ID = 'payment_method_id';

    /**
     * @var string
     */
    const RESPONSE_CARD_BRAND = 'card_brand';

    /**
     * @var string
     */
    const RESPONSE_NO_CARD_MESSAGE = 'Card not informed by Payway';

    /**
     * @var Config $config
     */
    private $config;

    /**
     * @var DataReader $reader
     */
    private $reader;

    /**
     * CreditCardHandler constructor.
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

        /** @var OrderPaymentInterface $payment */
        $payment = $object->getPayment();
        $storeId = $object->getOrder()->getStoreId();

        // retrieve data coming from Payway
        $paymentResponse = $this->reader->readTransaction($response);
        $data = $paymentResponse->getDataField();

        $ccType = $data[self::RESPONSE_CARD_BRAND] ?? self::RESPONSE_NO_CARD_MESSAGE;

        $payment->setCcType($ccType);
    }

    /**
     * @param string $type
     * @param null|int|string $orderId
     * @return string
     */
    private function getCreditCardType(string $type, $orderId): string
    {
        return $this->config->getMagentoCcType($type, $orderId);
    }
}
