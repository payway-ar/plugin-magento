<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Payway\Api\Data;

/**
 * Gateway RegionSource
 */
interface RegionSourceInterface
{

    /**
     * @var string
     */
    const MAGENTO_DEFAULT = 'magento_default';

    /**
     * @var string
     */
    const MUGAR_MODULE = 'Mugar_ArgentinaRegions';

    /**
     * @var string
     */
    const CUSTOM = 'custom_regions';

    /**
     *
     * Region Code for DECIDIR
     *
     * Here you could map your region code (left value)
     * Value will be extracted from country_directory_region if exists
     * else text value imputed by customer as region sales_order_address
     *
     *  @var string[]
     */
    CONST CUSTOM_REGIONS_MAPPER = [
        'Buenos Aires' => 'AR-B',
        'Ciudad Autónoma de Buenos Aires' => 'AR-C',
        'CABA' => 'AR-C',
        'Catamarca' => 'AR-K',
        'Chaco' => 'AR-H',
        'Chubut' => 'AR-U',
        'Córdoba' => 'AR-X',
        'Corrientes' => 'AR-W',
        'Entre Ríos' => 'AR-E',
        'Formosa' => 'AR-P',
        'Jujuy' => 'AR-Y',
        'La Pampa' => 'AR-L',
        'La Rioja' => 'AR-F',
        'Mendoza' => 'AR-M',
        'Misiones' => 'AR-N',
        'Neuquén' => 'AR-Q',
        'Río Negro'=> 'AR-R',
        'Salta' => 'AR-A',
        'San Juan' => 'AR-J',
        'San Luis' => 'AR-D',
        'Santa Cruz' => 'AR-Z',
        'Santa Fe' => 'AR-S',
        'Santiago del Estero' => 'AR-G',
        'Tierra del Fuego' => 'AR-V',
        'Antártida e Islas del Atlántico Sur' => 'AR-V',
        'Tucumán' => 'AR-T'
    ];


    /**
     * Return region code
     *
     * @param $address
     * @return string
     */
    public function getRegionCode($address): string;

    /**
     * Return Array of custom values
     *
     * @return array
     */
    public function getCustomRegionsArray(): array;

    /**
     * Return code from array
     *
     * @param $customCode
     * @return string
     */
    public function parseCustomRegionCode($customCode): string;

}
