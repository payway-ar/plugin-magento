<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Prisma\Payway\Gateway\Helper\DataReader;

/**
 * Handles the Sale response
 */
class SaleHandler implements HandlerInterface
{
    /**
     * @var string
     */
    const DEFAULT_TITLE = 'Prisma Payway (Sale Handler)';

    /**
     * @var DataReader
     */
    private $subjectReader;

    /**
     * @param DataReader $subjectReader
     */
    public function __construct(
        DataReader $subjectReader
    ) {
        $this->subjectReader = $subjectReader;
    }

    /**
     * Handles response
     *
     * @param array $handlingSubject
     * @param array $response
     * @return string
     */
    public function handle(array $handlingSubject, array $response): string
    {
        if (!isset($handlingSubject['payment'])) {
            return self::DEFAULT_TITLE;
        }

        /** @var \Magento\Quote\Model\Quote\Payment $payment */
        $payment = $handlingSubject['payment']->getPayment();

        return $this->getTitle($payment);
    }

    /**
     * @param $payment
     * @return string
     */
    public function getTitle($payment): string
    {
        if ($payment->getAdditionalInformation('method_title')) {
            return $payment->getAdditionalInformation('method_title');
        }

        return self::DEFAULT_TITLE;
    }
}
