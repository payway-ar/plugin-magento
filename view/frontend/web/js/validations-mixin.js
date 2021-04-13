/**
 *
 *
 */
define([
    'jquery'
], function($) {
    'use strict';

    return function() {
        /**
         * Adds support for validating Credit Card Document Type value
         * Validation to be used only for in the Checkout Decidir Payment field
         */
        $.validator.addMethod(
            'validate-cc-decidir-doctype',
            function(value, element) {
                var config = window.checkoutConfig.payment.decidir || {},
                    list = config && config.document_types;

                return list.hasOwnProperty(value);
            },
            $.mage.__('Please select a valid Document Type')
        )
    }
});
