<?php
/**
 *
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Prisma\Decidir\Gateway\Helper\DataReader;
use Prisma\Decidir\Model\Config;

/**
 * Extracts Credit Card code from the transaction response
 */
class CreditCardHandler implements HandlerInterface
{
    const PAYMENT_METHOD_ID = 'payment_method_id';

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

    public function handle(array $handlingSubject, array $response)
    {
        $object = $this->reader->readPayment($handlingSubject);

        /** @var OrderPaymentInterface $payment */
        $payment = $object->getPayment();
        $storeId = $object->getOrder()->getStoreId();

        // retrieve data coming from Decidir
        $paymentResponse = $this->reader->readTransaction($response);
        $data = $paymentResponse->getDataField();

        $payment->setCcType(
            $this->getCreditCardType(
                (string) $data[self::PAYMENT_METHOD_ID],
                $storeId
            )
        );
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
