<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Model\Adapter;

use Decidir\Connector;
use Decidir\PartialRefund\PaymentPartialRefund;
use Decidir\Payment\PaymentResponse;
use Decidir\PaymentInfo\PaymentInfoResponse;
use Decidir\Refund\PaymentRefundResponse;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Prisma\Payway\Api\Data\RestClientInterface;
use Prisma\Payway\Gateway\Config\Config;
use Prisma\Payway\Gateway\Config\ConnectorConfigProvider;

/**
 * Layer between Magento and Payway PHP SDK operations
 */
class PaywayAdapter
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Connector
     */
    protected $connector;

    /**
     * @var array
     */
    protected $credentials = [];

    /**
     * @var ConnectorConfigProvider
     */
    protected $connectorConfiguration;

    /**
     * Environment type to be used when instantiating the Connector
     *
     * @var string
     */
    protected $type;

    /**
     * @param Config $config
     * @param ConnectorConfigProvider $connector
     */
    public function __construct(
        Config $config,
        ConnectorConfigProvider $connector
    ) {
        $this->config = $config;
        $this->connectorConfiguration = $connector;

        $this->initCredentials()
            ->setupConnector();
    }

    /**
     * Loads credentials and the environment type
     *
     * @return $this
     * @throws InputException
     * @throws NoSuchEntityException
     */
    protected function initCredentials()
    {
        $keys = $this->config->getApiCredentials();

        $this->credentials = [
            RestClientInterface::CONNECTOR_PUBLIC_KEY => $keys[Config::CREDENTIALS_PUBLIC_KEY],
            RestClientInterface::CONNECTOR_PRIVATE_KEY => $keys[Config::CREDENTIALS_PRIVATE_KEY]
        ];

        $this->type = $this->connectorConfiguration->getEnvironmentType();

        return $this;
    }

    /**
     * Initializes the Connector with credentials and environment type
     *
     * @return $this
     */
    protected function setupConnector()
    {
        $this->connector = new Connector(
            $this->credentials,
            $this->type,
            RestClientInterface::CONNECTOR_DEVELOPER,
            RestClientInterface::CONNECTOR_GROUPER,
            RestClientInterface::CONNECTOR_SERVICE
        );

        return $this;
    }

    /**
     * @param string $operationId
     * @param array $attributes
     *
     * @return PaymentInfoResponse
     */
    public function capture(string $operationId, array $attributes)
    {
        return $this->connector
            ->payment()
            ->CapturePayment($operationId, $attributes);
    }

    /**
     * Executes a Payment operation
     *
     * @param array $attributes
     * @return PaymentResponse
     */
    public function sale(array $attributes)
    {
        return $this->connector
            ->payment()
            ->ExecutePayment($attributes);
    }

    /**
     * Executes a partial refund against a specific Payment
     *
     * Additional `$data` param, must contain the `amount` field with
     * signature of the param reads as follows, where the `amount` value
     * assigned is integer typed:
     * <code>
     * array (
     *      'amount' => 100
     * )
     * </code>
     *
     * @param string $paymentId Id of the payment to be refunded
     * @param array $data
     * @return PaymentPartialRefund
     * @throws \Exception
     */
    public function refund(string $paymentId, array $data): PaymentPartialRefund
    {
        return $this->connector
            ->payment()
            ->partialRefund($data, $paymentId);
    }


    /**
     * Executes a full refund against a specific Payment
     *
     * Additional `$data` param, may contain the `amount` field with
     * signature of the param reads as follows, where the `amount` value
     * assigned is integer typed:
     * <code>
     * array (
     *      'amount' => 100
     * )
     * </code>
     *
     * @param string $paymentId Id of the payment to be refunded
     * @param array $data
     * @return PaymentRefundResponse
     * @throws \Exception
     */
    public function void(string $paymentId, array $data): PaymentRefundResponse
    {
        return $this->connector
            ->payment()
            ->Refund($data, $paymentId);
    }
}
