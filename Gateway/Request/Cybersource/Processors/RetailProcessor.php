<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Request\Cybersource\Processors;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use PayPal\Braintree\Gateway\Data\Order\OrderAdapter;
use Prisma\Decidir\Api\Data\Request\Cybersource\CybersourceProcessorInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Prisma\Decidir\Model\Utility\RegionHandler;

class RetailProcessor implements CybersourceProcessorInterface
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var RegionHandler
     */
    private $regionHandler;

    /**
     * Processor constructor.
     * @param OrderRepository $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CustomerRepositoryInterface $customerRepository
     * @param DateTime $dateTime
     * @param RegionHandler $regionHandler
     */
    public function __construct(
        OrderRepository $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CustomerRepositoryInterface $customerRepository,
        DateTime $dateTime,
        RegionHandler $regionHandler
    ) {
        $this->orderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerRepository = $customerRepository;
        $this->dateTime = $dateTime;
        $this->regionHandler = $regionHandler;
    }

    /**
     *{@inheritdoc}
     */
    public function process(OrderAdapter $order): array
    {
        $result[CybersourceProcessorInterface::FRAUD_DETECTION] = [
            'send_to_cs' => CybersourceProcessorInterface::SEND_TO_CS,
            'channel' => CybersourceProcessorInterface::CHANNEL,
            'dispatch_method' => '',
            'bill_to' => $this->getBillTo($order->getBillingAddress(), $order->getCustomerId()),
            'purchase_totals' => $this->getPurchaseTotals($order),
            'customer_in_site' => $this->getCustomerInSite($order->getCustomerId(), $order->getBillingAddress()),
            'retail_transaction_data' => $this->getRetailTransactionData(
                $this->getShipTo($order->getShippingAddress(),  $order->getCustomerId()),
                $order,
                $this->getItems($order)
            )
        ];

        return $result;
    }

    /**
     * Build billing data
     *
     * @param $billingAddress
     * @param $customerId
     * @return array
     */
    private function getBillTo($billingAddress, $customerId): array
    {
        $billTo = [
            'city' => $billingAddress->getCity(),
            'country' => $billingAddress->getCountryId(),
            'customer_id' => $customerId ? (string) $customerId : $billingAddress->getFirstname() . '_' . $billingAddress->getLastname(),
            'email' => $billingAddress->getEmail(),
            'first_name' => $billingAddress->getFirstname(),
            'last_name' => $billingAddress->getLastname(),
            'phone_number' => $billingAddress->getTelephone(),
            'postal_code' => $billingAddress->getPostcode(),
            'state' => $this->getRegionCode($billingAddress),
            'street1' => $billingAddress->getStreetLine1()
        ];

        return $billTo;
    }

    /**
     * Build shipping data
     *
     * @param $shippingAddress
     * @param $customerId
     * @return array
     */
    private function getShipTo($shippingAddress, $customerId): array
    {
        $shipTo = [
            'city' => $shippingAddress->getCity(),
            'country' => $shippingAddress->getCountryId(),
            'customer_id' => $customerId ? (string) $customerId : $shippingAddress->getFirstname() . '_' . $shippingAddress->getLastname(),
            'email' => $shippingAddress->getEmail(),
            'first_name' => $shippingAddress->getFirstname(),
            'last_name' => $shippingAddress->getLastname(),
            'phone_number' => $shippingAddress->getTelephone(),
            'postal_code' => $shippingAddress->getPostcode(),
            'state' => $this->getRegionCode($shippingAddress),
            'street1' => $shippingAddress->getStreetLine1()
        ];

        return $shipTo;
    }

    /**
     * Build totals data
     *
     * @param $order
     * @return array
     */
    private function getPurchaseTotals($order): array
    {
        $purchaseTotals = [
            'currency' => $order->getCurrencyCode(),
            'amount' => $this->parseAmount($order->getGrandTotalAmount())
        ];

        return $purchaseTotals;
    }

    /**
     * Build customer data
     *
     * @param $customerId
     * @param $billingAddress
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCustomerInSite($customerId, $billingAddress): array
    {
        if ($customerId) {
            $customer = $this->customerRepository->getById($customerId);
        }

        $customerInSite = [
            'days_in_site' => $customerId ? $this->getCustomerDaysInSite($customer->getCreatedAt()) : 0,
            'is_guest' => $customerId ? false : true,
            'password' => '',
            'num_of_transactions' => (int)$this->getCustomerOrdersTotal($customerId),
            'cellphone_number' => $billingAddress->getTelephone(),
            'date_of_birth' => $customerId ? $customer->getDob() : '',
            'street1' => $billingAddress->getStreetLine1()
        ];

        return $customerInSite;
    }

    /**
     * Build transaction data
     *
     * @param $shipTo
     * @param $order
     * @param $items
     * @return array
     */
    private function getRetailTransactionData($shipTo, $order, $items): array
    {
        $retailTransactionData = [
            'ship_to' => $shipTo,
            'days_to_delivery' => '',
            'dispatch_method' => '',
            'tax_voucher_required' => true,
            'customer_loyalty_number' => '',
            'coupon_code' => '',
            'items' => $items
        ];

        return $retailTransactionData;
    }

    /**
     * Build items data
     *
     * @param $order
     * @return array
     */
    private function getItems($order): array
    {
        $items = [];
        foreach ($order->getItems() as $item) {
            $items[] = [
                'code' => $item->getSku(),
                'description' => $item->getDescription(),
                'name' => $item->getName(),
                'sku' =>  $item->getSku(),
                'total_amount' => $this->getTotalProductQty((int) $item->getQtyOrdered(), $item->getPrice()),
                'quantity' => (int)$item->getQtyOrdered(),
                'unit_price' => $this->parseAmount($item->getPrice())
            ];
        }

        return $items;
    }

    /**
     * Gateway accepts only integers
     * will infer last two digits as decimals
     *
     * @param float $amount
     * @return int
     */
    private function parseAmount(float $amount): int
    {
        return (int) str_replace(",", "", str_replace(".", "", $amount));
    }

    /**
     * Get total product amount
     * @param $qty
     * @param $amount
     * @return int
     */
    private function getTotalProductQty($qty, $amount): int
    {
        return $this->parseAmount($amount) * $qty;
    }

    /**
     * Get customer days in site
     *
     * @param $createdAt
     * @return int
     */
    private function getCustomerDaysInSite($createdAt): int
    {
        return  strtotime($this->dateTime->gmtDate()) - strtotime($createdAt);
    }

    /**
     * Get customer orders placed count
     *
     * @param $customerId
     * @return int
     */
    public function getCustomerOrdersTotal($customerId): int
    {
        $this->searchCriteriaBuilder->addFilter('customer_id', $customerId);

        $order = $this->orderRepository
            ->getList($this->searchCriteriaBuilder->create())
            ->getItems();

        return count($order);
    }

    /**
     * Retrieve Region Code for CS request
     *
     * @param $address
     * @return string
     */
    public function getRegionCode($address): string
    {
        return $this->regionHandler->getRegionCode($address);
    }
}
