<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Block\Adminhtml;

use Magento\Payment\Block\Info\Cc;
use Magento\Framework\App\State;

/**
 * Class Info
 *
 */
class Info extends Cc
{
    /**
     * @var string
     */
    const AREA_CODE_ADMIN = 'adminhtml';

    /**
     * Payment config model
     *
     * @var \Magento\Payment\Model\Config
     */
    protected $_paymentConfig;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_state;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Payment\Model\Config $paymentConfig
     * @param array $data
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Payment\Model\Config $paymentConfig,
        array $data = [],
        State $state
    ) {
        parent::__construct($context, $paymentConfig, $data);
        $this->_paymentConfig = $paymentConfig;
        $this->_state = $state;
    }

    /**
     * Retrieve credit card type name
     *
     * @return string
     */
    public function getCcTypeName()
    {
        $types = $this->_paymentConfig->getCcTypes();
        $ccType = $this->getInfo()->getCcType();
        if (isset($types[$ccType])) {
            return $types[$ccType];
        }
        return empty($ccType) ? __('N/A') : $ccType;
    }

    /**
     * Whether current payment method has credit card expiration info
     *
     * @return int
     */
    public function hasCcExpDate()
    {
        return (int)$this->getInfo()->getCcExpMonth() || (int)$this->getInfo()->getCcExpYear();
    }

    /**
     * Retrieve CC expiration month
     *
     * @return string
     */
    public function getCcExpMonth()
    {
        $month = $this->getInfo()->getCcExpMonth();
        if ($month < 10) {
            $month = '0' . $month;
        }
        return $month;
    }

    /**
     * Retrieve CC expiration date
     *
     * @return \DateTime
     */
    public function getCcExpDate()
    {
        $date = new \DateTime('now', new \DateTimeZone($this->_localeDate->getConfigTimezone()));
        $date->setDate(
            $this->getInfo()->getCcExpYear(),
            $this->getInfo()->getCcExpMonth() + 1,
            0);
        return $date;
    }

    /**
     * Prepare credit card related payment info
     *
     * @param \Magento\Framework\DataObject|array $transport
     * @return \Magento\Framework\DataObject
     */
    protected function _prepareSpecificInformation($transport = null)
    {
        if (null !== $this->_paymentSpecificInformation) {
            return $this->_paymentSpecificInformation;
        }

        $transport = parent::_prepareSpecificInformation($transport);
        $data = [];

        if ($ccType = $this->getCcTypeName()) {
            $data[(string)__('Credit Card Type')] = $ccType;
        }
        if ($this->getInfo()->getCcLast4()) {
            $data[(string)__('Credit Card Number')] = sprintf('xxxx-%s', $this->getInfo()->getCcLast4());
        }
        if ($this->getInfo()->getAdditionalInformation('installments')) {
            $data[(string)__('Installments')] = $this->getInfo()->getAdditionalInformation('installments');
        }

        if ($this->_state->getAreaCode() == self::AREA_CODE_ADMIN) {

            if ($ccStatus = $this->getInfo()->getCcStatus()) {
                $data[(string)__('Status')] = sprintf('%s',$ccStatus);
                if ($ccStatusDescription = $this->getInfo()->getCcStatusDescription()) {
                    $data[(string)__('Status')] .= sprintf('%s', ' - ('. $ccStatusDescription .')');
                }
            }
            if ($this->getCcExpMonth()) {
                $data[(string)__('Cc Expiration Month')] = sprintf('%s', $this->getCcExpMonth());
            }

            if ($this->getInfo()->getAdditionalInformation('cc_exp_year')) {
                $data[(string)__('Cc Expiration Year')] = $this->getInfo()->getAdditionalInformation('cc_exp_year');
            }
            if ($this->getInfo()->getAddressStatus()) {
                $data[(string)__('Address Status Code')] = $this->getInfo()->getAddressStatus();
            }
            if ($ccTicket = $this->getInfo()->getAdditionalInformation('ticket')) {
                $data[(string)__('Ticket')] = $ccTicket;
            }
            if ($ccAuthCode = $this->getInfo()->getAdditionalInformation('card_authorization_code')) {
                $data[(string)__('Cc Auth Code')] = $ccAuthCode;
            }
            if ($ccSecureVerify = $this->getInfo()->getCcSecureVerify()) {
                $data[(string)__('CS Decision')] = strtoupper($ccSecureVerify);
            }

        }

        return $transport->setData(array_merge($data, $transport->getData()));
    }

    /**
     * Format year/month on the credit card
     *
     * @param string $year
     * @param string $month
     * @return string
     */
    protected function _formatCardDate($year, $month)
    {
        return sprintf('%s/%s', sprintf('%02d', $month), $year);
    }
}
