/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
define([
    'jquery',
    'ko',
    'Magento_Payment/js/view/payment/cc-form',
    'mage/validation',
    'Prisma_Decidir/js/model/decidir',
    'Prisma_Decidir/js/model/config',
    'Magento_Ui/js/model/messageList',
    'Magento_Payment/js/model/credit-card-validation/validator',
],function (
    $,
    ko,
    Component,
    $t,
    decidir,
    config,
    messageList
) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Prisma_Decidir/payment/decidir-payment',
            code: 'decidir',
            token: '',
            bin: '',
            lastFourDigits: '',
            installments: '',
            isTokenReady: ko.observable(false),
            additionalData: {}
        },
        // Used to display exceptions during Decidir API interactions
        messageDispatcher: ko.observable(),

        initObservable: function () {
            this._super()
                .observe([
                    'token',
                    'bin',
                    'creditCardHolderName',
                    'creditCardDocumentNumber',
                    'creditCardDocumentType',
                    'lastFourDigits',
                    'installments'
                ])
            ;
            return this;
        },
        /**
         * Get payment method code
         * @returns string
         */
        getCode: function() {
            return config.getCode();
        },
        /**
         * Is method available
         * @returns bool|Boolean
         */
        isActive: function() {
            return config.getIsActive();
        },
        /**
         *
         * @returns {*}
         */
        validate: function () {
            var $form = $('#' + this.getCode() + '-form');
            return $form.validation() && $form.validation('isValid');
        },
        /**
         *
         * @returns {{additional_data: {cc_cid: *, cc_type: *, cc_exp_month: *, card_holder_name: *, installments: *, bin: *, card_holder_doc_type: *, cc_number: *, card_holder_doc_number: *, last_four_digits: *, cc_exp_year: *, token: *}, method: *}}
         */
        getData: function () {
            var data = {
                'method': this.item.method,
                'additional_data': {
                    'cc_cid': this.creditCardVerificationNumber(),
                    'cc_type': this.creditCardType(),
                    'cc_exp_year': this.creditCardExpYear(),
                    'cc_exp_month': this.creditCardExpMonth(),
                    'cc_number': this.creditCardNumber(),
                    'card_holder_name': this.creditCardHolderName(),
                    'card_holder_doc_number': this.creditCardDocumentNumber(),
                    'card_holder_doc_type': this.creditCardDocumentType(),
                    'token': this.token(),
                    'bin': this.bin(),
                    'last_four_digits': this.lastFourDigits(),
                    'installments': this.installments(),
                }
            };

            return data;
        },
        /**
         * Get list of available credit card types
         * @returns {Object}
         */
        getCcAvailableTypes: function () {
            return config.getCcAvailableTypes();
        },
        /**
         * Get list of months
         * @returns {Object}
         */
        getCcMonths: function () {
            return config.getCcMonths();
        },
        /**
         * Get list of years
         * @returns {Object}
         */
        getCcYears: function () {
            return config.getCcYears();
        },
        /**
         *
         * @returns {*}
         */
        getDocumentTypes: function () {
            return config.getDocumentTypes();
        },
        /**
         * Check if current payment has verification
         * @returns {Boolean}
         */
        hasVerification: function () {
            return config.hasVerification();
        },
        /**
         * Get image url for CVV
         * @returns {String}
         */
        getCvvImageUrl: function () {
            return config.getCvvImageUrl();
        },
        /**
         * Get image for CVV
         * @returns {String}
         */
        getCvvImageHtml: function () {
            return '<img src="' + this.getCvvImageUrl() +
                '" alt="' + $t('Card Verification Number Visual Reference') +
                '" title="' + $t('Card Verification Number Visual Reference') +
                '" />';
        },
        /**
         * Get list of available credit card types values
         * @returns {Object}
         */
        getCcAvailableTypesValues: function () {
            return _.map(this.getCcAvailableTypes(), function (value, key) {
                return {
                    'value': key,
                    'type': value
                };
            });
        },
        /**
         * Get list of available month values
         * @returns {Object}
         */
        getCcMonthsValues: function () {
            return _.map(this.getCcMonths(), function (value, key) {
                return {
                    'value': key,
                    'month': value
                };
            });
        },
        /**
         * Get list of available year values
         * @returns {Object}
         */
        getCcYearsValues: function () {
            return _.map(this.getCcYears(), function (value, key) {
                return {
                    'value': key,
                    'year': value
                };
            });
        },
        /**
         *
         * @returns {*}
         */
        getCcDocumentTypes: function () {
            return _.map(this.getDocumentTypes(), function (value, key) {
                return {
                    'value': key,
                    'type': value
                }
            });
        },
        /**
         * Is legend available to display
         * @returns {Boolean}
         */
        isShowLegend: function () {
            return config.isShowLegend();
        },
        /**
         * Get available credit card type by code
         * @param {String} code
         * @returns {String}
         */
        getCcTypeTitleByCode: function (code) {
            var title = '',
                keyValue = 'value',
                keyType = 'type';

            _.each(this.getCcAvailableTypesValues(), function (value) {
                if (value[keyValue] === code) {
                    title = value[keyType];
                }
            });

            return title;
        },
        /**
         * Get payment icons
         * @param {String} type
         * @returns {Boolean}
         */
        getIcons: function (type) {
            return config.getIcons(type);
        },
        /**
         * Get available installments
         * @returns array
         */
        getAvailableInstallments: function () {
            return config.getAvailableInstallments();
        },
        /**
         * Get list of available installments values
         * in this case value is used as text & value
         *
         *  * @returns {Object}
         */
        getAvailableInstallmentsValues: function() {
            return _.map(this.getAvailableInstallments(), function (value, key) {
                return {
                    'value': value,
                    'installment': value
                };
            });
        },
        /**
         * Is CS (cybersource) enable
         * @returns bool|Boolean
         */
        isCsActive: function() {
            return config.getIsCsActive();
        },
        /**
         *
         * @returns {boolean}
         */
        generateToken: function () {
            var self = this;
            if (!this.validate()) {
                return false;
            }
            this.beforePlaceOrder()
                .then(
                    function (result) {
                        this.bin(result.bin);
                        this.token(result.id);
                        this.isTokenReady(true);
                        this.lastFourDigits(result.last_four_digits);
                        return self.placeOrder();
                    }.bind(this),
                    function (error) {
                        console.error(error);
                        this.publishErrorMessage('Payment Token could not be generated at this time.');
                        this.isTokenReady(false);
                    }.bind(this)
                ).finally(function () {
                    }.bind(this)
                );
        },
        /**
         *
         * @param data
         * @returns {Promise}
         */
        beforePlaceOrder: function (data) {
            return decidir.loadToken(this.getData().additional_data)
                .then(
                    function (result) {
                        return Promise.resolve(result);
                    },
                    function (error) {
                        return Promise.reject(error);
                    }
                );
        },
        /**
         *
         * @param message
         */
        publishErrorMessage: function (message) {
            messageList.addErrorMessage({
                message: message
            });
        }
    });
});
