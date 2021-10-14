<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Response\Cybersource;


use InvalidArgumentException;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Payment\Model\InfoInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Prisma\Decidir\Gateway\Helper\DataReader;
use Prisma\Decidir\Model\Config;
use Prisma\Decidir\Gateway\Config\CybersourceConfig;

/**
 * Assigns all Cybersource data related to the Payment entity
 */
class CybersourceHandler implements HandlerInterface
{
    /**
     * @var string
     */
    const CS_DECISION = 'decision';

    /**
     * @var string
     */
    const STATUS = 'status';

    /**
     * @var string
     */
    const CS_FRAUD_DETECTION = 'fraud_detection';

    /**
     * @var Config $config
     */
    private $config;

    /**
     * @var CybersourceConfig
     */
    private $csConfig;

    /**
     * @var DataReader $reader
     */
    private $reader;

    /**
     * @var State
     */
    private $state;

    /**
     * CybersourceHandler constructor.
     * @param Config $config
     * @param CybersourceConfig $csConfig
     * @param DataReader $reader
     * @param State $state
     */
    public function __construct(
        Config $config,
        CybersourceConfig $csConfig,
        DataReader $reader,
        State $state
    ) {
        $this->config = $config;
        $this->csConfig = $csConfig;
        $this->reader = $reader;
        $this->state = $state;
    }

    /**
     * @inheritDoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        if ($this->csConfig->isCsActive()) {
            $object = $this->reader->readPayment($handlingSubject);

            /** @var InfoInterface $payment */
            $payment = $object->getPayment();

            $response = $this->getDataField($response);

            $fraudDetectionResponsePayload = json_encode($response[self::CS_FRAUD_DETECTION]);
            $decision = $response[self::CS_FRAUD_DETECTION][self::STATUS][self::CS_DECISION];

            $payment->setCcSecureVerify($decision);
            $this->setCsFraudDetectionPayload($payment, $fraudDetectionResponsePayload);

        }
    }

    /**
     * Set Cybersource status details payload
     *
     * @param $payload
     * @param InfoInterface $payment
     */
    public function setCsFraudDetectionPayload(InfoInterface $payment, $payload)
    {
        try {
            $payment->setAdditionalInformation(
                self::CS_FRAUD_DETECTION,
                $payload
            );
            // phpcs:ignore Magento2.CodeAnalysis.EmptyBlock
        } catch (LocalizedException $exception) {
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
