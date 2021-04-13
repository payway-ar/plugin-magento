<?php
/**
 *
 *
 */
declare(strict_types=1);

namespace Prisma\Decidir\Gateway\Http\Client;

use Exception;
use Prisma\Decidir\Api\Data\ErrorCodesInterface;
use Prisma\Decidir\Model\Adapter\DecidirAdapter;
use Magento\Payment\Gateway\Http\ClientException;
use Magento\Payment\Gateway\Http\ClientInterface;
use Magento\Payment\Gateway\Http\TransferInterface;
use Magento\Payment\Model\Method\Logger;
use Psr\Log\LoggerInterface;

/**
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
     * @var DecidirAdapter
     */
    protected $adapter;

    /**
     * Constructor
     *
     * @param LoggerInterface $logger
     * @param Logger $customLogger
     * @param DecidirAdapter $adapter
     */
    public function __construct(
        LoggerInterface $logger,
        Logger $customLogger,
        DecidirAdapter $adapter
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
