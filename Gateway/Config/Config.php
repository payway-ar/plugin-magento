<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Payment\Model\InfoInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Store\Model\ScopeInterface;
use Prisma\Decidir\Model\StoreConfigResolver;
use Prisma\Decidir\Model\Config as ModuleConfig;

/**
 * Payment Configuration provider
 */
class Config extends \Magento\Payment\Gateway\Config\Config
{
    const XPATH_MODULE_ACTIVE = 'active';

    const XPATH_USE_CVV = 'useccv';
    const XPATH_CC_TYPES = 'cctypes';
    const XPATH_CC_TYPES_MAPPER = 'cctypes_mapper';
    const XPATH_INSTALLMENTS = 'installments';

    const KEY_ENVIRONMENT = 'environment';
    const CREDENTIALS_PUBLIC_KEY = 'public_key';
    const CREDENTIALS_PRIVATE_KEY = 'private_key';
    const CREDENTIALS_JS_URL = 'url';
    const MODE_SANDBOX = 'sandbox';
    const MODE_PRODUCTION = 'production';
    const XPATH_MODULE_MODE = 'module_mode';
    const XPATH_SANDBOX_PUBLIC_KEY = 'sandbox_public_key';
    const XPATH_SANDBOX_PRIVATE_KEY = 'sandbox_private_key';
    const XPATH_SANDBOX_JS_URL = 'sandbox_js_url';
    const XPATH_PRODUCTION_PUBLIC_KEY = 'production_public_key';
    const XPATH_PRODUCTION_PRIVATE_KEY = 'production_private_key';
    const XPATH_PRODUCTION_JS_URL = 'production_js_url';
    const XPATH_CS_ACTIVE = 'cs_active';
    const XPATH_CS_VERTICALS = 'cs_vertical';
    const XPATH_CS_REGION_SOURCE = 'cs_region_source';

    /**
     * @var StoreConfigResolver
     */
    private $storeConfigResolver;

    /**
     * @var Json
     */
    private $serializer;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ModuleConfig
     */
    private $moduleConfig;

    /**
     * @param StoreConfigResolver $storeConfigResolver
     * @param ScopeConfigInterface $scopeConfig
     * @param ModuleConfig $moduleConfig
     * @param null $methodCode
     * @param string $pathPattern
     * @param Json|null $serializer
     */
    public function __construct(
        StoreConfigResolver $storeConfigResolver,
        ScopeConfigInterface $scopeConfig,
        ModuleConfig $moduleConfig,
        $methodCode = null,
        $pathPattern = self::DEFAULT_PATH_PATTERN,
        Json $serializer = null
    ) {
        parent::__construct($scopeConfig, $methodCode, $pathPattern);
        $this->scopeConfig = $scopeConfig;
        $this->storeConfigResolver = $storeConfigResolver;
        $this->moduleConfig = $moduleConfig;
        $this->serializer = $serializer ?: ObjectManager::getInstance()
            ->get(Json::class);
    }

    /**
     * Retrieve available credit card types
     *
     * @return array
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function getAvailableCardTypes(): array
    {
        // @TODO implement Store Id
        $ccTypes = $this->getValue(self::XPATH_CC_TYPES);

        return !empty($ccTypes)
            ? explode(',', $ccTypes)
            : [];
    }

    /**
     * Retrieve mapper between Magento and Gateway card types
     *
     * @return array
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function getCcTypesMapper(): array
    {
        $result = json_decode(
            $this->getValue(self::XPATH_CC_TYPES_MAPPER),
            true
        );

        return is_array($result) ? $result : [];
    }

    /**
     * Returns Decidir CC id based on Magento CC type
     *
     * @param string $type
     * @param null|string|int $storeId
     * @return string
     */
    public function getDecidirCcType(string $type, $storeId = null): string
    {
        $config = $this->getCcTypesMapper();

        return isset($config[$type])
            ? $config[$type]
            : '';
    }

    /**
     * Returns Magento CC type, based on Decidir CC id
     *
     * @param string $type
     * @return string
     */
    public function getMagentoCcType(string $type): string
    {
        return array_search(
            $type,
            $this->getCcTypesMapper()
        ) ?? '';
    }

    /**
     * Check if cvv field is enabled
     *
     * @return boolean
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function isCvvEnabled(): bool
    {
        return (bool) $this->getValue(
            self::XPATH_USE_CVV,
            $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     * Check if cvv field is enabled for vaulted cards
     *
     * @return boolean
     */
    public function isCvvEnabledVault(): bool
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isActive(int $storeId = null)
    {
        return (bool) $this->getvalue(
            self::XPATH_MODULE_ACTIVE,
            $storeId ?? $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     * Whether Production mode is the current mode
     *
     * @return bool
     */
    public function isProductionModeEnabled(): bool
    {
        $storeId = $this->storeConfigResolver->getStoreId();

        return $this->getValue(
            self::XPATH_MODULE_MODE,
            $storeId
        ) === self::MODE_PRODUCTION;
    }

    /**
     * @return string
     */
    public function getProductionPublicKey(): string
    {
        return $this->getValue(
            self::XPATH_PRODUCTION_PUBLIC_KEY,
            $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     * @return string
     */
    public function getProductionPrivateKey(): string
    {
        return $this->getValue(
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
        return $this->getValue(
            self::XPATH_PRODUCTION_JS_URL,
            $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     * @return bool
     */
    public function isSandboxModeEnabled(): bool
    {
        return $this->getValue(
            self::XPATH_MODULE_MODE,
            $this->storeConfigResolver->getStoreId()
        ) === self::MODE_SANDBOX;
    }

    /**
     * @return string
     */
    public function getSandboxPublicKey(): string
    {
        return $this->getValue(
            self::XPATH_SANDBOX_PUBLIC_KEY,
            $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     * @return string
     */
    public function getSandboxPrivateKey(): string
    {
        return $this->getValue(
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
        return $this->getValue(
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
            self::CREDENTIALS_JS_URL => $this->getSandboxJsUrl(),
            self::CREDENTIALS_PUBLIC_KEY => $this->getSandboxPublicKey(),
            self::CREDENTIALS_PRIVATE_KEY => $this->getSandboxPrivateKey()
        ];
    }

    /**
     * @return array
     */
    public function getProductionCredentials(): array
    {
        return [
            self::CREDENTIALS_JS_URL => $this->getProductionJsUrl(),
            self::CREDENTIALS_PUBLIC_KEY => $this->getProductionPublicKey(),
            self::CREDENTIALS_PRIVATE_KEY => $this->getProductionPrivateKey()
        ];
    }

    /**
     * Returns credentials based on which mode the module is configured
     *
     * <pre>
     * array(
     *      'url' => '',
     *      'public_key' => '',
     *      'private_key' => ''
     * )
     * </pre>
     *
     * @return string[]
     */
    public function getApiCredentials()
    {
        if ($this->isSandboxModeEnabled()) {
            return $this->getSandboxCredentials();
        }

        if ($this->isProductionModeEnabled()) {
            return $this->getProductionCredentials();
        }

        return [];
    }

    /**
     * Returns credentials to be used for JS SDK in storefront
     *
     * @return array
     */
    public function getJsSDKCredentials(): array
    {
        $credentials = $this->getApiCredentials();

        // Avoid exposing private key into the storefront
        unset($credentials[self::CREDENTIALS_PRIVATE_KEY]);

        return $credentials;
    }

    /**
     * Get environment
     *
     * @return string
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function getMode(): string
    {
        return $this->getValue(
            self::XPATH_MODULE_MODE,
            $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     * List of comma separated Credit Card type values
     *
     * @return string
     */
    public function getConfiguredCcTypes(): string
    {
        return $this->getValue(
            self::XPATH_CC_TYPES,
            $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     * List of comma separated installments values
     *
     * @return string
     *
     */
    public function getConfiguredInstallments(): string
    {
        return $this->getValue(
            self::XPATH_INSTALLMENTS,
            $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     * Is CS (cybersource) active
     *
     * @param int | null $storeId
     * @return bool
     */
    public function isCsActive(int $storeId = null): bool
    {
        return $this->moduleConfig->getConfigFlag(
            self::XPATH_CS_ACTIVE,
            $storeId ?? $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     *
     * Get Selected Vertical
     *
     * @return string
     *
     */
    public function getSelectedCsVertical(): string
    {
        return $this->getValue(
            self::XPATH_CS_VERTICALS,
            $this->storeConfigResolver->getStoreId()
        );
    }

    /**
     *
     * Get Selected Region Source
     *
     * @return string
     */
    public function getSelectedCsRegionSource(): string
    {
        return $this->getValue(
            self::XPATH_CS_REGION_SOURCE,
            $this->storeConfigResolver->getStoreId()
        );
    }
}
