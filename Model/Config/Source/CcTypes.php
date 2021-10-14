<?php
/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
declare(strict_types=1);

namespace Prisma\Decidir\Model\Config\Source;

/**
 * Provides Credit Cards list for backend configuration
 */
class CcTypes
{
    /**
     * @return array
     */
    public function toOptionArray(): array
    {

         return [
            ['value' => '', 'label' => __('-- Please Select --')],
            ['value' => 'AE', 'label' => __('American Express')],
            ['value' => 'VI', 'label' => __('Visa')],
            ['value' => 'MC', 'label' => __('MasterCard')],
            ['value' => 'DN', 'label' => __('Diners Club')],
            ['value' => 'TS', 'label' => __('Tarjeta Shopping')],
            ['value' => 'TN', 'label' => __('Tarjeta Naranja')],
            ['value' => 'IC', 'label' => __('Italcred')],
            ['value' => 'AC', 'label' => __('Argencard')],
            ['value' => 'CP', 'label' => __('Copeplus')],
            ['value' => 'NX', 'label' => __('Nexo')],
            ['value' => 'CM', 'label' => __('Credimás')],
            ['value' => 'TNV', 'label' => __('Tarjeta Nevada')],
            ['value' => 'NAT', 'label' => __('Nativa')],
            ['value' => 'TCS', 'label' => __('Tarjeta Cencosud')],
            ['value' => 'TCCM', 'label' => __('Tarjeta Carrefour / Cetelem')],
            ['value' => 'TPN', 'label' => __('Tarjeta Pyme Nación')],
            ['value' => 'BBPS', 'label' => __('BBPS')],
            ['value' => 'QI', 'label' => __('Qida')],
            ['value' => 'GRA', 'label' => __('Grupar')],
            ['value' => 'PTT', 'label' => __('Patagonia 365')],
            ['value' => 'TCD', 'label' => __('Tarjeta Club Día')],
            ['value' => 'TUY', 'label' => __('Tuya')],
            ['value' => 'TDI', 'label' => __('Distribution')],
            ['value' => 'TLA', 'label' => __('Tarjeta La Anónima')],
            ['value' => 'TCG', 'label' => __('CrediGuia')],
            ['value' => 'TCP', 'label' => __('Cabal Prisma')],
            ['value' => 'TSL', 'label' => __('Tarjeta Sol')],
            ['value' => 'TVC', 'label' => __('FavaCard')],
            ['value' => 'MAP', 'label' => __('MasterCard Prisma')],
            ['value' => 'VD', 'label' => __('Visa Débito')],
            ['value' => 'MDP', 'label' => __('MasterCard Debit Prisma')],
            ['value' => 'MTP', 'label' => __('Maestro Prisma')],
            ['value' => 'CDP', 'label' => __('Cabal Débito Prisma')],
        ];

    }
}
