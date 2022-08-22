/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
/**
 * Makes Payway Payment method available into the Checkout Payment step
 */
define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
], function (Component, rendererList) {
    'use strict';

    rendererList.push({
        type: 'payway',
        component: 'Prisma_Payway/js/view/payment/method-renderer/payway-method'
    });

    return Component.extend({});
});
