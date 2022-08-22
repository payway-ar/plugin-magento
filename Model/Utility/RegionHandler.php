<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Model\Utility;

use Magento\Directory\Model\RegionFactory;
use Prisma\Payway\Gateway\Config\CybersourceConfig;
use Prisma\Payway\Api\Data\RegionSourceInterface;
use Magento\Framework\Module\Manager as ModuleManager;

/**
 * Class RegionHandler
 *
 */
class RegionHandler implements RegionSourceInterface
{
    /**
     * @var RegionFactory
     */
    private $regionFactory;

    /**
     * @var CybersourceConfig
     */
    private $csConfig;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * RegionHandler constructor.
     *
     * @param RegionFactory $regionFactory
     * @param CybersourceConfig $csConfig
     * @param ModuleManager $moduleManager
     */
    public function __construct(
        RegionFactory $regionFactory,
        CybersourceConfig $csConfig,
        ModuleManager $moduleManager
    ) {
        $this->regionFactory = $regionFactory;
        $this->csConfig = $csConfig;
        $this->moduleManager = $moduleManager;
    }

    /**
     * {@inheridoc}
     */
    public function getCustomRegionsArray(): array
    {
        return RegionSourceInterface::CUSTOM_REGIONS_MAPPER;
    }

    /**
     * {@inheridoc}
     */
    public function getRegionCode($address): string
    {
        $regionSource = $this->csConfig->getSelectedRegionSource();
        switch ($regionSource) {
            case RegionSourceInterface::MUGAR_MODULE:
                $code = $this->parseMugarRegionCode($address->getRegionCode());
                break;
            case RegionSourceInterface::CUSTOM:
                $code = $this->parseCustomRegionCode($address->getRegionCode());
                break;
            case RegionSourceInterface::MAGENTO_DEFAULT:
            default:
                $code = substr($address->getRegionCode(), 3);
                break;
        }
        return $code;
    }

    /**
     * Validate if MugarModule exists and is enable
     *
     * @return bool
     */
    public function isMugarModuleEnabled(): bool
    {
       return $this->moduleManager->isEnabled(RegionSourceInterface::MUGAR_MODULE);
    }

    /**
     *{@inheritdoc}
     */
    public function parseCustomRegionCode($customCode): string
    {
        $regionsArray = $this->getCustomRegionsArray();
        return isset($regionsArray[$customCode])
            ? substr($regionsArray[$customCode], 3)
            : $customCode;
    }

    /**
     * Parse MugarModule Region Code
     *
     * @param $code
     * @return string
     */
    public function parseMugarRegionCode($code): string
    {
        // use offset 3 due code format "AR-X"
        return $this->isMugarModuleEnabled() ? substr($code, 3) : $code;
    }
}

