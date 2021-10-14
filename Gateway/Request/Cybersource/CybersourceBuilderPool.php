<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Request\Cybersource;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Payment\Gateway\Request\BuilderInterface;
use PayPal\Braintree\Gateway\Data\Order\OrderAdapter;
use Prisma\Decidir\Gateway\Helper\DataReader;
use Prisma\Decidir\Gateway\Config\CybersourceConfig;
use Magento\Sales\Model\OrderRepository;

class CybersourceBuilderPool implements BuilderInterface
{
    /**
     * @var DataReader $reader
     */
    private $reader;
    /**
     * @var array $processors
     */
    private array $processors;
    /**
     * @var CybersourceConfig
     */
    private $csConfig;
    /**
     * @var OrderRepository
     */
    private $orderRepository;
    /**
     * ProcessorPool constructor.
     * @param DataReader $reader
     * @param CybersourceConfig $csConfig
     * @param OrderRepository $orderRepository
     * @param array $processors
     */
    public function __construct(
        DataReader $reader,
        CybersourceConfig $csConfig,
        OrderRepository $orderRepository,
        array $processors
    ) {
        $this->reader = $reader;
        $this->csConfig = $csConfig;
        $this->orderRepository = $orderRepository;
        $this->processors = $processors;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject): array
    {

        $payment = $this->reader->readPayment($buildSubject);

        $result = [];
        /** @var OrderAdapter $order */
        $order = $payment->getOrder();
        foreach ($this->processors as $processor) {
            if (array_key_exists($this->csConfig->getSelectedVertical(), $this->processors)) {
                return $processor->process($order);
            }
        }
        return $result;
    }

}
