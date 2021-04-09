define([
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'decidir', //payment method code
                component: 'Prisma_Decidir/js/view/payment/method-renderer/decidir-method'
            }
        );

        /** Add view logic here if needed */
        return Component.extend({});
    });
