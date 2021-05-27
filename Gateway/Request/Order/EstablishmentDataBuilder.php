<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Request\Order;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Magento\Store\Api\Data\StoreConfigInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Prisma\Decidir\Gateway\Helper\DataReader;

/**
 * Provides the Store Name
 */
class EstablishmentDataBuilder implements BuilderInterface
{
    /**
     * @var string
     */

    const ESTABLISHMENT_NAME = 'establishment_name';

    /**
     * @var DataReader
     */
    private $reader;

    /**
     * @var StoreRepositoryInterface
     */
    private $repository;

    /**
     * @param DataReader $reader
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        DataReader $reader,
        StoreRepositoryInterface $storeRepository
    ) {
        $this->reader = $reader;
        $this->repository = $storeRepository;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject): array
    {
        $payment = $this->reader->readPayment($buildSubject);
        $order = $payment->getOrder();

        try {
            $name = $this->repository
                    ->getById($order->getStoreId())
                    ->getName();
        } catch (NoSuchEntityException $exception) {
            $name = 'Default Store Name';
        }

        return [
            self::ESTABLISHMENT_NAME => $name
        ];
    }
}
