<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Api\Data;

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
        'Buenos Aires' => 'B',
        'Ciudad Autónoma de Buenos Aires' => 'C',
        'CABA' => 'C',
        'Catamarca' => 'K',
        'Chaco' => 'H',
        'Chubut' => 'U',
        'Córdoba' => 'X',
        'Corrientes' => 'W',
        'Entre Ríos' => 'E',
        'Formosa' => 'P',
        'Jujuy' => 'Y',
        'La Pampa' => 'L',
        'La Rioja' => 'F',
        'Mendoza' => 'M',
        'Misiones' => 'N',
        'Neuquén' => 'Q',
        'Río Negro'=> 'R',
        'Salta' => 'A',
        'San Juan' => 'J',
        'San Luis' => 'D',
        'Santa Cruz' => 'Z',
        'Santa Fe' => 'S',
        'Santiago del Estero' => 'G',
        'Tierra del Fuego' => 'V',
        'Antártida e Islas del Atlántico Sur' => 'V',
        'Tucumán' => 'T'
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
