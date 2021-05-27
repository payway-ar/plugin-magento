<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Response;

use Decidir\Payment\PaymentResponse;
use InvalidArgumentException;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Prisma\Decidir\Gateway\Helper\DataReader;
use Prisma\Decidir\Model\Config;

/**
 * Assings all payment data related to the Payment entity
 */
class PaymentDetailsHandler implements HandlerInterface
{
    /**
     * @var string
     */
    const CC_TYPE = 'payment_method_id';

    /**
     * @var string
     */
    const INSTALLMENTS = 'installments';

    /**
     * @var string
     */
    const TRANSACTION_AREA = 'area';

    /**
     * @var string
     */
    const CC_EXP_MONTH = 'cc_exp_month';

    /**
     * @var string
     */
    const CC_EXP_YEAR = 'cc_exp_year';

    /**
     * @var string
     */
    const CC_STATUS = 'status';

    /**
     * @var string
     */
    const CC_AUTH_CODE = 'card_authorization_code';

    /**
     * @var string
     */
    const CC_TICKET = 'ticket';

    /**
     * @var string
     */
    const CC_LAST_FOUR_DIGITS = 'last_four_digits';


    /**
     * @var string
     */
    const STATUS_DETAILS = 'status_details';

    /**
     * @var string[]
     */
    const ADDITIONAL_FIELDS_TO_REMOVE = [
        'bin'
    ];

    /**
     * @var Config $config
     */
    private $config;

    /**
     * @var DataReader $reader
     */
    private $reader;

    /**
     * @var State
     */
    private $state;

    /**
     * PaymentDetailsHandler constructor.
     * @param Config $config
     * @param DataReader $reader
     * @param State $state
     */
    public function __construct(
        Config $config,
        DataReader $reader,
        State $state
    ) {
        $this->config = $config;
        $this->reader = $reader;
        $this->state = $state;
    }

    /**
     * @inheritDoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $object = $this->reader->readPayment($handlingSubject);

        /** @var InfoInterface $payment */
        $payment = $object->getPayment();
        $additional = $payment->getAdditionalInformation();

        $payment->setCcExpMonth($additional[self::CC_EXP_MONTH]);
        $payment->setCcExpYear($additional[self::CC_EXP_YEAR]);
        $payment->setCcLast4($additional[self::CC_LAST_FOUR_DIGITS]);

        $this->setCreditCardStatus($payment, $response);
        $this->setTransactionSource($payment);
        $this->setInstallments($payment, $response);
        $this->setCcAuthCode($payment, $response);
        $this->setCcTicket($payment, $response);
    }

    /**
     *
     * @param InfoInterface $payment
     */
    public function setTransactionSource(InfoInterface $payment)
    {
        try {
            $payment->setAdditionalInformation(
                self::TRANSACTION_AREA,
                $this->state->getAreaCode()
            );
            // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
        } catch (LocalizedException $exception) {
            // pass
        }
    }

    /**
     * Sets the status of the transaction
     *
     * @param $payment
     * @param $response
     */
    public function setCreditCardStatus($payment, $response)
    {
        try {
            $data = $this->getDataField($response);

            $status = isset($data[self::CC_STATUS])
                ? $data[self::CC_STATUS]
                : '';

            $payment->setCcStatus($status);
            $payment->setCcStatusDescription($status);

            // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
        } catch (InvalidArgumentException $exception) {
            // pass
        }
    }

    /**
     * Remove fields from additional information
     *
     * @param InfoInterface $payment
     */
    public function removeAdditionalInformation(InfoInterface $payment)
    {
        foreach (self::ADDITIONAL_FIELDS_TO_REMOVE as $key) {
            $payment->unsAdditionalInformation($key);
        }
    }

    /**
     * Sets payment installments
     *
     * @param $payment
     * @param $response
     */
    public function setInstallments($payment, $response)
    {
        try {
            $data = $this->getDataField($response);
            $payment->setAdditionalInformation(
                self::INSTALLMENTS,
                $data[self::INSTALLMENTS]
            );

            // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
        } catch (InvalidArgumentException $exception) {
            // pass
        }
    }

    /**
     * Sets cc card authorization code
     *
     * @param $payment
     * @param $response
     */
    public function setCcAuthCode($payment, $response)
    {
        try {
            $data = $this->getDataField($response);
            $statusDetails = $data[self::STATUS_DETAILS];

            $payment->setAdditionalInformation(
                self::CC_AUTH_CODE,
                $statusDetails[self::CC_AUTH_CODE]
            );

            // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
        } catch (InvalidArgumentException $exception) {
            // pass
        }
    }

    /**
     * Sets cc card ticket number
     *
     * @param $payment
     * @param $response
     */
    public function setCcTicket($payment, $response)
    {
        try {
            $data = $this->getDataField($response);
            $statusDetails = $data[self::STATUS_DETAILS];

            $payment->setAdditionalInformation(
                self::CC_TICKET,
                $statusDetails[self::CC_TICKET]
            );

            // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
        } catch (InvalidArgumentException $exception) {
            // pass
        }
    }

    /**
     * Return response data array
     *
     * @param $response
     * @return array
     */
    private function getDataField($response)
    {
        try {
            $paymentResponse = $this->reader->readTransaction($response);
            $data = $paymentResponse->getDataField();

            return $data;
            // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
        } catch (InvalidArgumentException $exception) {
            // pass
        }

    }
}
