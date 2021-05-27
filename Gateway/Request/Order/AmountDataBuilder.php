<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Request\Order;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Prisma\Decidir\Gateway\Helper\DataReader;

/**
 * Assigns the amount of the Order into the request
 */
class AmountDataBuilder implements BuilderInterface
{
    const AMOUNT = 'amount';

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
        $order = $payment->getOrder();

        $amount = $this->parseGrandTotal($order->getGrandTotalAmount());

        return [
            self::AMOUNT => $amount
        ];
    }

    /**
     * Gateway accepts only integers
     * will infer last two digits as decimals
     *
     * @param float $amount
     * @return int
     */
    public function parseGrandTotal(float $amount): int
    {
        return (int) str_replace(",", "", str_replace(".", "", $amount));
    }
}
