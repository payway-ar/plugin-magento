<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Gateway\Http\Client;

use Exception;
use Prisma\Payway\Api\Data\ErrorCodesInterface;
use Prisma\Payway\Model\Adapter\PaywayAdapter;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use Psr\Log\LoggerInterface;

/**
 * Class AbstractTransaction
 *
 */
abstract class AbstractTransaction implements ClientInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var Logger
     */
    protected $customLogger;

    /**
     * @var PaywayAdapter
     */
    protected $adapter;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param Logger $customLogger
     * @param PaywayAdapter $adapter
     */
    public function __construct(
        LoggerInterface $logger,
        Logger $customLogger,
        PaywayAdapter $adapter
    ) {
        $this->logger = $logger;
        $this->customLogger = $customLogger;
        $this->adapter = $adapter;
    }

    /**
     * @inheritdoc
     */
    public function placeRequest(TransferInterface $transferObject)
    {
        $data = $transferObject->getBody();

        $log = ['request' => $data, 'client' => static::class];
        $response['object'] = [];

        try {
            $response['object'] = $this->process($data);

        } catch (\Decidir\Exception\SdkException $e) {
            $message = $e->getMessage();
            $this->logger->critical($message);

            $response['object'] = $e->getData();

            // We add the exception into the response array
            $response['exception'] = new ClientException(
                __($message),
                $e,
                ErrorCodesInterface::CODE_API_ERROR
            );
        } finally {
            $log['response'] = (array) $response;
            $this->customLogger->debug($log, null, true);
        }

        return $response;
    }

    /**
     * Process http request
     *
     * @param array $data
     */
    abstract protected function process(array $data);
}
