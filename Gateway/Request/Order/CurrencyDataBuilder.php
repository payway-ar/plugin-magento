<?php
/**
 *
 *
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Request\Order;

use Prisma\Decidir\Gateway\Helper\DataReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

/**
 * Assigns the currency of the Order
 */
class CurrencyDataBuilder implements BuilderInterface
{
    /**
     * @var string
     */

    const CURRENCY = 'currency';

    /**
     * @var DataReader
     */
    private $reader;

    /**
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
        $order = $payment->getOrder();

        return [
            self::CURRENCY => $order->getCurrencyCode()
        ];
    }
}
