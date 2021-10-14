<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Api\Data\Validator\Cybersource;

/**
 * Validate order cybersource interface
 *
 * Class CybersourceProcessorInterface
 * @see https://decidirv2.api-docs.io/1.0/prevencion-de-fraude-by-cybersource/flujo-de-una-transaccion-con-cybersource
 */
interface CybersourceValidatorInterface
{
    /**
     * @var string
     */
    const DECISION_GREEN = 'green';

    /**
     * @var string
     */
    const DECISION_RED = 'red';

    /**
     * @var string
     */
    const DECISION_YELLOW = 'yellow';

    /**
     * @var string
     */
    const DECISION_BLUE = 'BLUE';

    /**
     * @var string
     */
    const DECISION_BLACK = 'black';

    /**
     * @var string
     */
    const CYBERSOURCE_ERROR = 'cybersource_error';
}
