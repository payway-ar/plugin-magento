<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Gateway\Request\Order;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Prisma\Payway\Gateway\Helper\DataReader;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
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
     * @var TimezoneInterface
     */
    protected $timezone;

    /**
     * Constructor
     *
     * @param DataReader $reader
     * @param TimezoneInterface $timezone
     */
    public function __construct(DataReader $reader, TimezoneInterface $timezone)
    {
        $this->reader = $reader;
        $this->timezone = $timezone;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject): array
    {
        $payment = $this->reader->readPayment($buildSubject);
        $order = $payment->getOrder();

        return [
            self::SITE_TRANSACTION_ID => $order->getOrderIncrementId() . '_' . $this->timezone->scopeTimeStamp()
        ];
    }
}
