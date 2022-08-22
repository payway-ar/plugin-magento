<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Api\Data\Request\Cybersource;

use PayPal\Braintree\Gateway\Data\Order\OrderAdapter;

/**
 * Process order and return required values for cybersource
 *
 * Class CybersourceProcessorInterface
 * @see https://decidirv2.api-docs.io/1.0/prevencion-de-fraude-by-cybersource/flujo-de-una-transaccion-con-cybersource
 */
interface CybersourceProcessorInterface
{
    /**
     * @var string
     */
    const FRAUD_DETECTION = 'fraud_detection';

    /**
     * @var string
     */
    const SEND_TO_CS = true;

    /**
     * @var string
     */
    const CHANNEL = 'Web';

    /**
     * Process an Order and return values required for cybersource
     *
     * @param OrderAdapter $order
     * @return array
     */
    public function process(OrderAdapter $order): array;
}
