<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Model\Config\Source;

use Prisma\Payway\Api\Data\RegionSourceInterface;

/**
 * Configuration Region Source
 */
class RegionSource
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => RegionSourceInterface::MAGENTO_DEFAULT, 'label' => __('Magento default')],
            ['value' => RegionSourceInterface::MUGAR_MODULE, 'label' => __('MugAr ArgentinaRegions Module')]
        ];
    }
}
