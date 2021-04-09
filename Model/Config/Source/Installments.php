<?php
/**
*
*/
declare(strict_types=1);

namespace Prisma\Decidir\Model\Config\Source;

/**
* Provides Installments list for backend configuration
*/
class Installments
{
    const MAX_INSTALLMENTS = 18;

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        $installments = [];
        $i = 1;

        for ($i; $i <= self::MAX_INSTALLMENTS; $i++) {
            $installments[] = ['value' => $i, 'label' => $i];
        }

        return $installments;
    }
}
