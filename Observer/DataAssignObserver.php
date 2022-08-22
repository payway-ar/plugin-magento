<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Magento\Quote\Api\Data\PaymentInterface;

/**
 * Ports data into the additional information field for further use
 */
class DataAssignObserver extends AbstractDataAssignObserver
{
    const TOKEN = 'token';
    const BIN = 'bin';
    const CC_TYPE = 'cc_type';
    const CC_EXP_MONTH = 'cc_exp_month';
    const CC_EXP_YEAR = 'cc_exp_year';
    const LAST_FOUR_DIGITS = 'last_four_digits';
    const INSTALLMENTS =  'installments';

    /**
     * @var string[]
     */
    protected $additionalFields = [
        self::TOKEN,
        self::BIN,
        self::CC_TYPE,
        self::CC_EXP_MONTH,
        self::CC_EXP_YEAR,
        self::LAST_FOUR_DIGITS,
        self::INSTALLMENTS,
    ];

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $data = $this->readDataArgument($observer);

        $additionalData = $data->getData(PaymentInterface::KEY_ADDITIONAL_DATA);
        if (!is_array($additionalData)) {
            return;
        }

        $paymentInfo = $this->readPaymentModelArgument($observer);

        foreach ($this->additionalFields as $key) {
            if (isset($additionalData[$key])) {
                $paymentInfo->setAdditionalInformation(
                    $key,
                    $additionalData[$key]
                );
            }
        }
    }
}
