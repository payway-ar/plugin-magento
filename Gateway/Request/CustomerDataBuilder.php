<?php
/**
 *
 *
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use PayPal\Braintree\Gateway\Data\Order\OrderAdapter;
use Prisma\Decidir\Gateway\Helper\DataReader;

/**
 * Provides Customer data to the request
 */
class CustomerDataBuilder implements BuilderInterface
{
    /**
     * @var string
     */
    const CUSTOMER = 'customer';

    /**
     * @var string
     */
    const ID = 'id';

    /**
     * @var string
     */
    const EMAIL = 'email';

    /**
     * @var string
     */
    const IP_ADDRESS = 'ip_address';

    /**
     * @var DataReader
     */
    private $reader;

    /**
     * Constructor
     *
     * @param DataReader $reader
     */
    public function __construct(DataReader $reader)
    {
        $this->reader = $reader;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject): array
    {
        $payment = $this->reader->readPayment($buildSubject);

        /** @TODO: improve validation since there could be an scenario where Braintree is disabled */
        /** @var OrderAdapter $order */
        $order = $payment->getOrder();

        return [
            self::CUSTOMER => [
                self::ID => $order->getCustomerId(),
                self::EMAIL => $order->getBillingAddress()->getEmail(),
                self::IP_ADDRESS => $order->getRemoteIp(),
            ]
        ];
    }
}
