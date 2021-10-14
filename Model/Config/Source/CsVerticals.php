<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Model\Config\Source;

/**
 * Provides CS Vertical list for backend configuration
 */
class CsVerticals
{

    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'retail', 'label' => __('Retail')]
        ];
    }
}
