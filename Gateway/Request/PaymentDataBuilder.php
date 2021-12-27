<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Request;

use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Payment\Model\InfoInterface;
use Prisma\Decidir\Gateway\Helper\DataReader;
use Prisma\Decidir\Model\Config;

/**
 * Builds information related to the Payment
 */
class PaymentDataBuilder implements BuilderInterface
{
    /**
     * @var string
     */
    const BIN = 'bin';

    /**
     * @var string
     */
    const PAYMENT_METHOD_ID = 'payment_method_id';

    /**
     * @var string
     */
    const PAYMENT_TYPE = 'payment_type';

    /**
     * @var string
     */
    const INSTALLMENTS = 'installments';

    /**
     * @var DataReader
     */
    private $reader;

    /**
     * @var Config
     */
    private $config;

    /**
     * Constructor
     *
     * @param DataReader $reader
     * @param Config $config
     */
    public function __construct(
        DataReader $reader,
        Config $config
    ) {
        $this->reader = $reader;
        $this->config = $config;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject): array
    {
        $result = [];
        $payment = $this->reader->readPayment($buildSubject);
        $additional = $payment->getPayment()->getAdditionalInformation();
        $storeId = $payment->getOrder()->getStoreId();

        if (!empty($additional[self::BIN])) {
            $result[self::BIN] = $additional[self::BIN];
        }

        $result[self::INSTALLMENTS] = $this->getDefaultInstallmentAmount($payment->getPayment());

        $result[self::PAYMENT_METHOD_ID] = $additional['cc_type'];

        $result[self::PAYMENT_TYPE] = $this->getPaymentType();

        return $result;
    }

    /**
     * @return string
     */
    private function getPaymentType(): string
    {
        return 'single';
    }

    /**
     * @param InfoInterface $payment
     * @return int
     */
    private function getDefaultInstallmentAmount(InfoInterface $payment): int
    {
        $data = $payment->getAdditionalInformation();
        return (int) $data[self::INSTALLMENTS];
    }
}
