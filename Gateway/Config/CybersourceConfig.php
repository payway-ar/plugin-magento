<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Config;

use Prisma\Decidir\Gateway\Config\Config as Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Prisma\Decidir\Model\StoreConfigResolver;
use Prisma\Decidir\Model\Config as ModuleConfig;


/**
 * Cybersource Config provider
 */
class CybersourceConfig extends Config
{
    /**
     * @var StoreConfigResolver
     */
    private $storeConfigResolver;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param StoreConfigResolver $storeConfigResolver
     * @param ScopeConfigInterface $scopeConfig
     * @param ModuleConfig $moduleConfig
     * @param Config $config
     *
     */
    public function __construct(
        StoreConfigResolver $storeConfigResolver,
        ScopeConfigInterface $scopeConfig,
        ModuleConfig $moduleConfig,
        Config $config
    ) {
        parent::__construct($storeConfigResolver, $scopeConfig, $moduleConfig);
        $this->scopeConfig = $scopeConfig;
        $this->storeConfigResolver = $storeConfigResolver;
        $this->moduleConfig = $moduleConfig;
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getSelectedVertical(): string
    {
        return $this->config->getSelectedCsVertical();
    }

    /**
     * @return string
     */
    public function getSelectedRegionSource(): string
    {
        return $this->config->getSelectedCsRegionSource();
    }
}
