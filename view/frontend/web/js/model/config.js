/**
 *
 */
/**
 * Provides config values based on the current configuration mode
 */
define([], function () {
    'use strict';

    var config = window.checkoutConfig.payment.decidir;

    return {
        /**
         * @returns string
         */
        getUrl: function() {
            return config.env.url;
        },

        /**
         * @returns string
         */
        getPublicKey: function() {
            return config.env.public_key;
        },

        isSandboxEnabled: function () {
            return config.env.is_sandbox
        },
        /**
         * @returns string
         */
        getCode: function () {
            return config.code
        },
        /**
         * @returns bool
         */
        getIsActive: function() {
            return config.is_active;
        },
        /**
         * Get list of available credit card types
         * @returns {Object}
         */
        getCcAvailableTypes: function () {
            return config.available_types;
        },
        /**
         * Get list of months
         * @returns {Object}
         */
        getCcMonths: function () {
            return config.months;
        },
        /**
         * Get list of years
         * @returns {Object}
         */
        getCcYears: function () {
            return config.years;
        },
        /**
         * Get available document types
         * @returns array
         */
        getDocumentTypes: function () {
            return config.document_types;
        },
        /**
         * Check if current payment has verification
         * @returns {Boolean}
         */
        hasVerification: function () {
            return config.has_verification;
        },
        /**
         * Get image url for CVV
         * @returns {String}
         */
        getCvvImageUrl: function () {
            return config.cvv_image_url;
        },

        /**
         * Is legend available to display
         * @returns {Boolean}
         */
        isShowLegend: function () {
            return true;
        },
        /**
         * Get payment icons
         * @param {String} type
         * @returns {Boolean}
         */
        getIcons: function (type) {
            return config.icons.hasOwnProperty(type)
                ? config.icons[type]
                : false;
        },
        /**
         * Get available installments
         * @returns array
         */
        getAvailableInstallments: function () {
            return config.installments;
        },
    }
});
