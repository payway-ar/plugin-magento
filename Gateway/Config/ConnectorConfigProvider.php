<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Gateway\Config;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Prisma\Payway\Api\Data\RestClientInterface;

/**
 * Isolates specific Connector's configuration values
 */
class ConnectorConfigProvider
{
    /**
     * @var Config
     */
    protected $config;

    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Returns which environment name needs to be used
     * when initializing the Connector
     *
     * @return string
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function getEnvironmentType(): string
    {
        $mode = $this->config->getMode();

        return $mode === Config::MODE_PRODUCTION
            ? RestClientInterface::CONNECTOR_ENVIRONMENT_PRODUCTION
            : RestClientInterface::CONNECTOR_ENVIRONMENT_SANDBOX;
    }
}
