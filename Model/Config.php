<?php
/**
 *
 */
declare(strict_types=1);

namespace Prisma\Decidir\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * General purpose configuration retrieval tool
 */
class Config
{
    const CREDENTIALS_PUBLIC_KEY = 'public_key';
    const CREDENTIALS_PRIVATE_KEY = 'private_key';
    const CREDENTIALS_JS_URL = 'url';

    const XPATH_BASE = 'payment/decidir/';
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
     * @var ScopeConfigInterface
     */
    private $config;

    /** @var SerializerInterface  */
    private $json;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serialize
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serialize
    ) {
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
     * @param null|int|string $storeId
     * @return bool
     */
    public function isActive($storeId = null): bool
    {
        return $this->getConfigFlag(self::XPATH_MODULE_ACTIVE, $storeId);
    }

    /**
     * @param null|int|string $storeId
     * @return bool
     */
    public function isProductionModeEnabled($storeId = null): bool
    {
        return $this->getConfigFlag(self::XPATH_MODULE_MODE, $storeId)
            === $this->getConfigFlag(self::MODE_PRODUCTION, $storeId);
    }

    /**
     * @param null|int|string $storeId
     * @return string
     */
    public function getProductionPublicKey($storeId = null): string
    {
        return $this->getConfigValue(self::XPATH_PRODUCTION_PUBLIC_KEY, $storeId);
    }

    /**
     * @param null|int|string $storeId
     * @return string
     */
    public function getProductionPrivateKey($storeId = null): string
    {
        return $this->getConfigValue(self::XPATH_PRODUCTION_PRIVATE_KEY, $storeId);
    }

    /**
     * Returns JS URL to be used as initializer of the JS SDK
     *
     * @param null|int|string $storeId
     * @return string
     */
    public function getProductionJsUrl($storeId = null): string
    {
        return $this->getConfigValue(self::XPATH_PRODUCTION_JS_URL, $storeId);
    }

    /**
     * @param null|int|string $storeId
     * @return bool
     */
    public function isSandboxModeEnabled($storeId = null): bool
    {
        return $this->getConfigFlag(self::XPATH_MODULE_MODE, $storeId)
            === $this->getConfigFlag(self::MODE_SANDBOX, $storeId);
    }

    /**
     * @param null|int|string $storeId
     * @return string
     */
    public function getSandboxPublicKey($storeId = null): string
    {
        return $this->getConfigValue(self::XPATH_SANDBOX_PUBLIC_KEY, $storeId);
    }

    /**
     * @param null|int|string $storeId
     * @return string
     */
    public function getSandboxPrivateKey($storeId = null): string
    {
        return $this->getConfigValue(self::XPATH_SANDBOX_PRIVATE_KEY, $storeId);
    }

    /**
     * Returns JS URL to be used as initializer of the JS SDK
     *
     * @param null|int|string $storeId
     * @return string
     */
    public function getSandboxJsUrl($storeId = null): string
    {
        return $this->getConfigValue(self::XPATH_SANDBOX_JS_URL, $storeId);
    }

    /**
     * @param null|int|string $storeId
     * @return array
     */
    public function getSandboxCredentials($storeId = null): array
    {
        return [
            self::CREDENTIALS_JS_URL => $this->getSandboxJsUrl($storeId),
            self::CREDENTIALS_PUBLIC_KEY => $this->getSandboxPublicKey($storeId),
            self::CREDENTIALS_PRIVATE_KEY => $this->getSandboxPrivateKey($storeId)
        ];
    }

    /**
     * @param null|int|string $storeId
     * @return array
     */
    public function getProductionCredentials($storeId = null): array
    {
        return [
            self::CREDENTIALS_JS_URL => $this->getProductionJsUrl($storeId),
            self::CREDENTIALS_PUBLIC_KEY => $this->getProductionPublicKey($storeId),
            self::CREDENTIALS_PRIVATE_KEY => $this->getProductionPrivateKey($storeId)
        ];
    }

    /**
     * Returns credentials depending on the currently configured mode
     *
     * @param null|int|string $storeId
     * @return array
     */
    public function getApiCredentials($storeId = null): array
    {
        if ($this->isSandboxModeEnabled($storeId)) {
            return $this->getSandboxCredentials($storeId);
        }

        if ($this->isProductionModeEnabled($storeId)) {
            return $this->getProductionCredentials($storeId);
        }
    }

    /**
     * Returns credentials to be used for JS SDK in storefront
     *
     * @param null $storeId
     * @return array
     */
    public function getJsSDKCredentials($storeId = null): array
    {
        $credentials = $this->getApiCredentials($storeId);

        // avoid exposing private key into the storefront
        unset($credentials[self::CREDENTIALS_PRIVATE_KEY]);

        return $credentials;
    }

    /**
     * List of comma separated Credit Card type values
     *
     * @param null|int|string $storeId
     * @return string
     */
    public function getConfiguredCcTypes($storeId = null): string
    {
        return $this->getConfigValue(self::XPATH_CCTYPES, $storeId);
    }

    /**
     * Retrieve mapper between Magento and Decidir card types
     *
     * @param null|string|int
     * @return array
     */
    public function getCcTypesMapper($storeId): array
    {

        $result = $this->json->unserialize(
            $this->getConfigValue(self::XPATH_CCTYPES_MAPPER, $storeId)
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
        $config = $this->getCcTypesMapper($storeId);

        return isset($config[$type])
            ? $config[$type]
            : '';
    }

    /**
     * Returns Magento CC type, based on Decidir CC id
     *
     * @param string $type
     * @param null|string|int $storeId
     * @return string
     */
    public function getMagentoCcType(string $type, $storeId = null): string
    {
        return array_search(
            $type,
            $this->getCcTypesMapper($storeId)
        ) ?? '';
    }

    /**
     * @param null|int|string $storeId
     * @return bool
     */
    public function isCybersourceEnabled($storeId = null): bool
    {
        return $this->getConfigFlag(self::XPATH_CYBERSOURCE_ENABLED, $storeId);
    }
}
