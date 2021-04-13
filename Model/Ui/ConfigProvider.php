<?php
/**
 *
 *
 */
namespace Prisma\Decidir\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Payment\Model\CcConfig;
use Magento\Framework\View\Asset\Source;
//use Magento\Payment\Helper\Data as PaymentHelper;
//use Magento\Payment\Model\CcGenericConfigProvider;
//use Prisma\Decidir\Model\Decidir;
//use Prisma\Decidir\Model\Config;
use Prisma\Decidir\Gateway\Config\Config;

/**
 * Checkout configuration data provider
 *
 * @TODO: avoid extending from `CcGenericConfigProvider`, refactor day/month/availableCcTypes methods
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var string
     */
    const CODE = 'decidir';

    /**
     * @var array
     */
    private $icons = [];

    /**
     * @var Config
     */
    private $config;

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
     *
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     * @param CcConfig $ccConfig
     * @param Source $assetSource
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager,
        CcConfig $ccConfig,
        Source $assetSource
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->ccConfig = $ccConfig;
        $this->assetSource = $assetSource;
    }

    /**
     * @inheritDoc
     */
    public function getConfig(): array
    {
        return [
            'payment' => [
                self::CODE => [
                    'code' => self::CODE,
                    'is_active' => $this->config->isActive(),
                    'env' => $this->getEnvironmentConfiguration(),
                    'available_types' => $this->getCcAvailableTypes(),
                    'months' => $this->getCcMonths(),
                    'years' => $this->getCcYears(),
                    'document_types' => $this->getCcAvailableDocumentTypes(),
                    'has_verification' => $this->hasVerification(),
                    'cvv_image_url' => $this->getCvvImageUrl(),
                    'icons' => $this->getIcons(),
                    'installments' => $this->getCcAvailableInstallments()
                ]
            ]
        ];
    }

    /**
     * Retrieve credit card expire months
     *
     * @return array
     */
    protected function getCcMonths()
    {
        return $this->ccConfig->getCcMonths();
    }

    /**
     * Retrieve credit card expire years
     *
     * @return array
     */
    protected function getCcYears()
    {
        return $this->ccConfig->getCcYears();
    }

    /**
     * Retrieve CVV tooltip image url
     *
     * @return string
     */
    protected function getCvvImageUrl()
    {
        return $this->ccConfig->getCvvImageUrl();
    }

    /**
     * @return array
     */
    public function getCcAvailableTypes()
    {
        $types = $this->ccConfig->getCcAvailableTypes();
        $availableTypes = $this->config->getConfiguredCcTypes();

        if ($availableTypes) {
            $availableTypes = explode(',', $availableTypes);

            // TODO ensure this loop is strictly necessary, otherwise return `$availableTypes` directly
            foreach (array_keys($types) as $code) {
                if (!in_array($code, $availableTypes)) {
                    unset($types[$code]);
                }
            }
        }
        return $types;
    }

    /**
     * Retrieve has verification configuration
     *
     * @return bool
     */
    protected function hasVerification()
    {
//        $result = ;
//        $configData = $this->methods[$methodCode]->getConfigData('useccv');
//        if ($configData !== null) {
//            $result = (bool)$configData;
//        }
        return $this->ccConfig->hasVerification();
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isSandboxMode()
    {
        return $this->config->isSandboxModeEnabled();
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCredentials(): array
    {
        return $this->config->getJsSDKCredentials();
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getEnvironmentConfiguration(): array
    {
        $config = $this->getCredentials();
        $config['is_sandbox'] = $this->isSandboxMode();

        return $config;
    }

    /**
     * @return string[]
     */
    public function getCcAvailableDocumentTypes(): array
    {
        // TODO: implement it's own data provider
        return ['DNI' => 'DNI'];
    }

    /**
     * Get icons for available payment methods
     *
     * @return array
     */
    public function getIcons()
    {
        if (!empty($this->icons)) {
            return $this->icons;
        }

        $types = $this->ccConfig->getCcAvailableTypes();
        foreach ($types as $code => $label) {
            if (!array_key_exists($code, $this->icons)) {
                $asset = $this->ccConfig->createAsset('Prisma_Decidir::images/cc/' . strtolower($code) . '.png');
                $placeholder = $this->assetSource->findSource($asset);
                if ($placeholder) {
                    list($width, $height) = getimagesize($asset->getSourceFile());
                    $this->icons[$code] = [
                        'url' => $asset->getUrl(),
                        'width' => $width,
                        'height' => $height,
                        'title' => __($label),
                    ];
                }
            }
        }

        return $this->icons;
    }

    /**
     * Get congigured installments as array
     *
     * @return array
     */
    public function getCcAvailableInstallments(): array
    {
        $installments = $this->config->getConfiguredInstallments();

        return $installments = explode(',', $installments);

    }
}
