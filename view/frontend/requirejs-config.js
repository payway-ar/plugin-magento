/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
var config = {
    map: {
        '*': {
            paywayApi: 'https://live.decidir.com/static/v2.5/decidir.js'
        }
    },
    mixins: {
        'mage/validation': {
            'Prisma_Payway/js/validation-mixin': true
        }
    }
};
