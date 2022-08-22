<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Model\Config\Source;

use Prisma\Payway\Model\Config;

/**
 * Configuration Modes
 */
class ModuleModes
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => '', 'label' => __('-- Please Select --')],
            ['value' => Config::MODE_SANDBOX, 'label' => __('Sandbox')],
            ['value' => Config::MODE_PRODUCTION, 'label' => __('Production')]
        ];
    }
}
