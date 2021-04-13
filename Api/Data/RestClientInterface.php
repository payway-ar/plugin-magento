<?php
/**
 *
 */
declare(strict_types=1);

namespace Prisma\Decidir\Api\Data;

/**
 * Interface to hold static values used during request initialization
 * and Connector instantiation, which currently PHP SDK does not exposes
 *
 * @see \Decidir\RESTClient
 */
interface RestClientInterface
{
    /**
     * RestClient fields used to hold public key
     * @see \Decidir\RESTClient::setKey()
     * @var string
     */
    const CONNECTOR_PUBLIC_KEY = 'public_key';

    /**
     * RestClient fields used to hold private key
     * @see \Decidir\RESTClient::setKey()
     * @var string
     */
    const CONNECTOR_PRIVATE_KEY = 'private_key';

    /**
     * Value name to indicate Sandbox env
     * used in SDK during Connector creation
     *
     * @see \Decidir\RESTClient::__construct()
     * @var string
     */
    const CONNECTOR_ENVIRONMENT_SANDBOX = 'test';

    /**
     * Value name to indicate Production env
     * used in SDK during Connector creation
     *
     * @see \Decidir\RESTClient::__construct()
     * @var string
     */
    const CONNECTOR_ENVIRONMENT_PRODUCTION = 'prod';

    /**
     * Status codes to consider a request to be successful
     *
     * @var int[]
     */
    const SUCCESS_STATUS_CODES = [200, 201, 204];
}