<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Request\Order;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Prisma\Decidir\Gateway\Helper\DataReader;

/**
 * Assigns the Order Id to the request
 */
class SiteTransactionIdDataBuilder implements BuilderInterface
{
    const SITE_TRANSACTION_ID = 'site_transaction_id';

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

        return [
            self::SITE_TRANSACTION_ID => $order->getOrderIncrementId()
        ];
    }
}
