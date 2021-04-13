<?php
/**
 *
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
     * Validate data
     *
     * @param array $validationSubject
     * @return ResultInterface Magento\Payment\Gateway\Validator\ResultInterface
     */
    public function validate(array $validationSubject) :ResultInterface;

}
