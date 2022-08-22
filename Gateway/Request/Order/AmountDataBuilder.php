<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Gateway\Request\Order;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Prisma\Payway\Gateway\Helper\DataReader;

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
       // send amount as float, payway will handle decimals
        $amount = $order->getGrandTotalAmount();

        return [
            self::AMOUNT => $amount
        ];
    }
}
