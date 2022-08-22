<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Gateway\Request\Order;

use Prisma\Payway\Gateway\Helper\DataReader;
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
