<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */

namespace Prisma\Payway\Model;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Request\Http as RequestHttp;
use Magento\Sales\Model\OrderRepository;
use Magento\Backend\Model\Session\Quote as SessionQuote;
use Magento\Setup\Exception;
use Magento\TestFramework\Exception\NoSuchActionException;
use Magento\TestFramework\Integrity\Library\PhpParser\Throws;

/**
 * Class StoreConfigResolver
 *
 */
class StoreConfigResolver
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var RequestHttp
     */
    protected $request;

    /**
     * @var OrderRepository
     */
    protected $orderRepository;

    /**
     * @var SessionQuote
     */
    protected $sessionQuote;

    /**
     * StoreConfigResolver constructor.
     *
     * @param StoreManagerInterface $storeManager    StoreManager
     * @param RequestHttp           $request         HTTP request
     * @param OrderRepository       $orderRepository Order repository
     * @param SessionQuote          $sessionQuote    Session quote
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        RequestHttp $request,
        OrderRepository $orderRepository,
        SessionQuote $sessionQuote
    ) {
        $this->orderRepository = $orderRepository;
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->sessionQuote = $sessionQuote;
    }

    /**
     * Get store id for config values
     *
     * @return int|null
     *
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function getStoreId()
    {
        $currentStoreId = null;
        $orderId = $this->request->getParam('order_id');
        if ($orderId) {
            try {
                return $this->orderRepository->get($orderId)->getStoreId();
            } catch (NoSuchEntityException $exception) {
                Throw new NoSuchEntityException();
            } catch (Exception $exception) {
                Throw new Exception($exception->getMessage());
            }
        }
        $currentStoreIdInAdmin = $this->sessionQuote->getStoreId();
        if (!$currentStoreIdInAdmin) {
            $currentStoreId = $this->storeManager->getStore()->getId();
        }

        return $currentStoreId ?: $currentStoreIdInAdmin;
    }
}
