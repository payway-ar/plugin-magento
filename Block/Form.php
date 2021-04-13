<?php
/**
 *
 *
 */
namespace Prisma\Decidir\Block;

use Magento\Backend\Model\Session\Quote;
use Prisma\Decidir\Model\Config as GatewayConfig;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template\Context;
use Magento\Payment\Block\Form\Cc;
use Magento\Payment\Helper\Data;
use Magento\Payment\Model\Config;
use Psr\Log\LoggerInterface;

class Form extends Cc
{
    /**
     * Purchase order template
     *
     * @var string
     */
    protected $_template = 'Prisma_Decidir::form/default.phtml';

    /**
     * @var Quote
     */
    protected $sessionQuote;

    /**
     * @var Config
     */
    protected $gatewayConfig;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Data
     */
    private $paymentDataHelper;

    /**
     * @param Context $context
     * @param Config $paymentConfig
     * @param Quote $sessionQuote
     * @param GatewayConfig $gatewayConfig
     * @param LoggerInterface $logger
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $paymentConfig,
        Quote $sessionQuote,
        GatewayConfig $gatewayConfig,
        LoggerInterface $logger,
        array $data = []
    ) {
        parent::__construct($context, $paymentConfig, $data);

        $this->sessionQuote = $sessionQuote;
        $this->gatewayConfig = $gatewayConfig;
        $this->logger = $logger;
    }

    /**
     * Get list of available card types of order billing address country
     *
     * @inheritDoc
     */
//    public function getCcAvailableTypes(): array
//    {
//        try {
//            return $this->getConfiguredCardTypes();
//        } catch (InputException | NoSuchEntityException $e) {
//            $this->logger->critical($e->getMessage());
//        }
//
//        return [];
//    }

    /**
     * Get card types available for Braintree
     * @return array
     * @throws InputException
     * @throws NoSuchEntityException
     */
//    private function getConfiguredCardTypes(): array
//    {
//        return explode(',', $this->gatewayConfig->getConfiguredCcTypes());
//    }
}
