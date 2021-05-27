<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Api\Data\Validator;

use Magento\Payment\Gateway\Validator\ResultInterface;

/**
 * Interface ValidatorInterface
 *
 */
interface ValidatorInterface
{
    /**
     * @var string
     */
    const STATUS_APPROVED = 'approved';

    /**
     * @var string
     */
    const STATUS_REJECTED = 'rejected';

    /**
     * @var string
     */
    const STATUS_ANNULLED = 'annulled';

    /**
     * @var string
     */
    const STATUS_REVIEW = 'review';

    /**
     * Validate data
     *
     * @param array $validationSubject
     * @return ResultInterface Magento\Payment\Gateway\Validator\ResultInterface
     */
    public function validate(array $validationSubject) :ResultInterface;
}
