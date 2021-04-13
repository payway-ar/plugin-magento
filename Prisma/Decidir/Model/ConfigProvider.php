<?php
namespace Prisma\Decidir\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Payment\Model\CcConfig;
use Magento\Framework\View\Asset\Source;

class ConfigProvider implements ConfigProviderInterface
{
    const CODE = 'decidir';
    const DEFAULT_PATTERN = 'payment/decidir/';
    const KEY_ACTIVE = 'active';
    const KEY_ENVIRONMENT = 'sandbox_mode';
    const KEY_SANDBOX_MERCHANT_ID = 'sandbox_site_id';
    const KEY_SANDBOX_PUBLIC_KEY = 'sandbox_public_key';
    const KEY_SANDBOX_PRIVATE_KEY = 'sandbox_private_key';
    const KEY_PROD_MERCHANT_ID = 'prod_site_id';
    const KEY_PROD_PUBLIC_KEY = 'prod_public_key';
    const KEY_PROD_PRIVATE_KEY = 'prod_private_key';
    const KEY_ORDER_STATUS = 'order_status';
    const KEY_SANDBOX_JS_SDK_URL = 'sandbox_js_sdk_url';
    const KEY_PROD_JS_SDK_URL = 'prod_js_sdk_url';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;


    /**
     * @var CcConfig
     */
    protected $ccConfig;


	/**
	 * @var Source
     */
    protected $assetSource;

    /**
     * ConfigProvider constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param CcConfig $ccConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        CcConfig $ccConfig,
        Source $assetSource
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->ccConfig = $ccConfig;
        $this->assetSource = $assetSource;
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
//        return $this->scopeConfig->getValue(
//            self::DEFAULT_PATTERN.self::KEY_ACTIVE,
//            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
//            $this->storeManager->getStore()->getId()
//        ) ?
//        [
//            'payment' => [
//                self::CODE => [
//                    'code' => self::CODE,
//                    'isActive' => 1,
//                    'isSandbox' => $this->scopeConfig->getValue(
//                        self::DEFAULT_PATTERN.self::KEY_ENVIRONMENT,
//                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
//                        $this->storeManager->getStore()->getId()
//                    ),
//                ]
//            ]
//        ] : [];

        return [
            'payment' => [
                self::CODE => [
                    'code' => self::CODE,
                    'isActive' => true,
                    'isSandbox' => (bool) $this->scopeConfig->getValue(
                        self::DEFAULT_PATTERN.self::KEY_ENVIRONMENT,
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                        $this->storeManager->getStore()->getId()
                    ),
                    'availableTypes' => [self::CODE => $this->ccConfig->getCcAvailableTypes()],
                    'months' => [self::CODE => $this->ccConfig->getCcMonths()],
                    'years' => [self::CODE => $this->ccConfig->getCcYears()],
                    'hasVerification' => $this->ccConfig->hasVerification(),

                ]
            ]
        ];
    }
}
