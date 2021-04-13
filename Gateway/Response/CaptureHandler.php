<?php
/**
 *
 *
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Response;

use Magento\Payment\Gateway\Response\HandlerInterface;
use Prisma\Decidir\Gateway\Helper\DataReader;

/**
 * Handler for Capture Gateway command
 */
class CaptureHandler implements HandlerInterface
{
    const DEFAULT_TITLE = 'Prisma Decidir (Capture Handler)';

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
    public function handle(array $handlingSubject, array $response)
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
    public function getTitle($payment)
    {
        if ($payment->getAdditionalInformation('method_title')) {
            return $payment->getAdditionalInformation('method_title');
        }

        return self::DEFAULT_TITLE;
    }
}
