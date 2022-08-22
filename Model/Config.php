<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Prisma\Payway\Model\StoreConfigResolver;

/**
 * General purpose configuration retrieval tool
 */
class Config
{
    const CREDENTIALS_PUBLIC_KEY = 'public_key';
    const CREDENTIALS_PRIVATE_KEY = 'private_key';
    const CREDENTIALS_JS_URL = 'url';

    const XPATH_BASE = 'payment/payway/';
    const XPATH_MODULE_ACTIVE = 'active';

    const MODE_SANDBOX = 'sandbox';
    const MODE_PRODUCTION = 'production';

    const XPATH_MODULE_MODE = 'module_mode';
    const XPATH_SANDBOX_PUBLIC_KEY = 'sandbox_public_key';
    const XPATH_SANDBOX_PRIVATE_KEY = 'sandbox_private_key';
    const XPATH_SANDBOX_JS_URL = 'sandbox_js_url';
    const XPATH_PRODUCTION_PUBLIC_KEY = 'production_public_key';
    const XPATH_PRODUCTION_PRIVATE_KEY = 'production_private_key';
    const XPATH_PRODUCTION_JS_URL = 'production_js_url';

    const XPATH_CCTYPES = 'cctypes';
    const XPATH_CCTYPES_MAPPER = 'cctypes_mapper';

    const XPATH_CYBERSOURCE_ENABLED = 'cybersource_enabled';

    /**
     * @var StoreConfigResolver
     */
    private $storeConfigResolver;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /** @var SerializerInterface  */
    private $json;

    /**
     * @param StoreConfigResolver $storeConfigResolver
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serialize
     */
    public function __construct(
        StoreConfigResolver $storeConfigResolver,
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serialize
    ) {
        $this->storeConfigResolver = $storeConfigResolver;
        $this->config = $scopeConfig;
        $this->json = $serialize;
    }

    /**
     * @param string $xpath
     * @param null|int|string $storeId
     * @return mixed
     */
    public function getConfigValue($xpath, $storeId = null)
    {
        return $this->config->getValue(
            self::XPATH_BASE . $xpath,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $storeId
        );
    }

    /**
     * @param string $xpath
     * @param null|int|string $storeId
     * @return bool
     */
    public function getConfigFlag($xpath, $storeId = null): bool
    {
        return $this->config->isSetFlag(
            self::XPATH_BASE . $xpath,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $storeId
        );
    }

    /**
     * Whether module is enabled or not
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->getConfigFlag(
            self::XPATH_MODULE_ACTIVE,
            $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     * @return bool
     */
    public function isProductionModeEnabled(): bool
    {
        return $this->getConfigFlag(
            self::XPATH_MODULE_MODE,
            $this->storeConfigResolver->getStoreId()
            )
            === $this->getConfigFlag(
                self::MODE_PRODUCTION,
                $this->storeConfigResolver->getStoreId()
            );
    }

    /**
     * @return string
     */
    public function getProductionPublicKey(): string
    {
        return $this->getConfigValue(
            self::XPATH_PRODUCTION_PUBLIC_KEY,
            $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     * @return string
     */
    public function getProductionPrivateKey(): string
    {
        return $this->getConfigValue(
            self::XPATH_PRODUCTION_PRIVATE_KEY,
            $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     * Returns JS URL to be used as initializer of the JS SDK
     *
     * @return string
     */
    public function getProductionJsUrl(): string
    {
        return $this->getConfigValue(
            self::XPATH_PRODUCTION_JS_URL,
            $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     * @return bool
     */
    public function isSandboxModeEnabled(): bool
    {
        return $this->getConfigFlag(
            self::XPATH_MODULE_MODE,
                $this->storeConfigResolver->getStoreId()
            )
            === $this->getConfigFlag(
                self::MODE_SANDBOX,
                $this->storeConfigResolver->getStoreId()
            );
    }

    /**
     * @return string
     */
    public function getSandboxPublicKey(): string
    {
        return $this->getConfigValue(
            self::XPATH_SANDBOX_PUBLIC_KEY,
            $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     * @return string
     */
    public function getSandboxPrivateKey(): string
    {
        return $this->getConfigValue(
            self::XPATH_SANDBOX_PRIVATE_KEY,
            $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     * Returns JS URL to be used as initializer of the JS SDK
     *
     * @return string
     */
    public function getSandboxJsUrl(): string
    {
        return $this->getConfigValue(
            self::XPATH_SANDBOX_JS_URL,
            $this->storeConfigResolver->getStoreId()
        );
    }

    /**

     * @return array
     */
    public function getSandboxCredentials(): array
    {
        return [
            self::CREDENTIALS_JS_URL => $this->getSandboxJsUrl($this->storeConfigResolver->getStoreId()),
            self::CREDENTIALS_PUBLIC_KEY => $this->getSandboxPublicKey($this->storeConfigResolver->getStoreId()),
            self::CREDENTIALS_PRIVATE_KEY => $this->getSandboxPrivateKey($this->storeConfigResolver->getStoreId())
        ];
    }

    /**
     * @return array
     */
    public function getProductionCredentials(): array
    {
        return [
            self::CREDENTIALS_JS_URL => $this->getProductionJsUrl($this->storeConfigResolver->getStoreId()),
            self::CREDENTIALS_PUBLIC_KEY => $this->getProductionPublicKey($this->storeConfigResolver->getStoreId()),
            self::CREDENTIALS_PRIVATE_KEY => $this->getProductionPrivateKey($this->storeConfigResolver->getStoreId())
        ];
    }

    /**
     * Returns credentials depending on the currently configured mode
     *
     * @return array
     */
    public function getApiCredentials(): array
    {
        if ($this->isSandboxModeEnabled($this->storeConfigResolver->getStoreId())) {
            return $this->getSandboxCredentials($this->storeConfigResolver->getStoreId());
        }

        if ($this->isProductionModeEnabled($this->storeConfigResolver->getStoreId())) {
            return $this->getProductionCredentials($this->storeConfigResolver->getStoreId());
        }
    }

    /**
     * Returns credentials to be used for JS SDK in storefront
     *
     * @return array
     */
    public function getJsSDKCredentials(): array
    {
        $credentials = $this->getApiCredentials($this->storeConfigResolver->getStoreId());

        // avoid exposing private key into the storefront
        unset($credentials[self::CREDENTIALS_PRIVATE_KEY]);

        return $credentials;
    }

    /**
     * List of comma separated Credit Card type values
     *
     * @return string
     */
    public function getConfiguredCcTypes(): string
    {
        return $this->getConfigValue(
            self::XPATH_CCTYPES,
            $this->storeConfigResolver->getStoreId()
        );
    }


    /**
     * @return bool
     */
    public function isCybersourceEnabled(): bool
    {
        return $this->getConfigFlag(
            self::XPATH_CYBERSOURCE_ENABLED,
            $this->storeConfigResolver->getStoreId()
        );
    }
}
