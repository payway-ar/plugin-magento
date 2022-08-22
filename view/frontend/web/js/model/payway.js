/**
 * Copyright © IURCO and PRISMA. All rights reserved.
 */
define([
    'jquery',
    'ko',
    'Prisma_Payway/js/model/config',
    'paywayApi'
], function (
    $,
    ko,
    config
) {
    'use strict';

    /**
     * Builds an HTML input list (which is never injected into the DOM) to be sent to Payway SDK
     *
     * Payway SDK expects to receive a list of inputs, so we'll build it on-the-fly
     * in favor of Magento Payment form compatibility to avoid introducing
     * unnecessary customizations into the Checkout Payment Form
     * If in future the SDK expects a JSON object, this step can be removed
     *
     * @param formValues {Object}
     * @returns {HTMLDivElement}
     * @private
     */
    function _generateDummyForm (formValues) {
        var htmlInputList = '',
            fieldList = [
                {'name': 'card_number', 'value': formValues.cc_number},
                {'name': 'card_expiration_month', 'value':formValues.cc_exp_month},
                {'name': 'card_expiration_year', 'value':formValues.cc_exp_year},
                {'name': 'card_holder_name', 'value':formValues.card_holder_name},
                {'name': 'card_holder_doc_type', 'value':formValues.card_holder_doc_type},
                {'name': 'card_holder_doc_number', 'value':formValues.card_holder_doc_number},
                {'name': 'security_code', 'value':formValues.cc_cid}
            ];

        htmlInputList = document.createElement('div');

        for (var i = 0; i < fieldList.length; i++) {
            var field = document.createElement('input')
            field.setAttribute('type', 'text');
            field.setAttribute('name', fieldList[i].name);
            field.setAttribute('data-decidir', fieldList[i].name);
            field.setAttribute('value', fieldList[i].value);

            htmlInputList.appendChild(field);
        }

        return htmlInputList;
    }

    /**
     * Executes data normalization as Gateway expects to receive
     *
     * @param {Object} object
     * @returns {Object}
     * @private
     */
    function _normalizeData (object) {
        // normalize so both dates are two digits
        object.cc_exp_month = object.cc_exp_month.length === 2
            ? object.cc_exp_month
            : '0' + object.cc_exp_month.toString();
        object.cc_exp_year = object.cc_exp_year.length === 4
            ? object.cc_exp_year.substr(2)
            : object.cc_exp_year;

        return object;
    }

    return {
        loadToken: function (data) {
            // Inclusion of Payway JS SDK file, creates a `Payway` global-scoped variable
            // if CS is active, disableCs must be false and viceversa
            var disableCs = !config.getIsCsActive();

            var api = new Decidir(
                config.getUrl(),
                disableCs
                ),
                tokenData = _generateDummyForm(
                    _normalizeData(data)
                );

            api.setPublishableKey(config.getPublicKey());
            api.setTimeout(0);

            return new Promise(function (resolve, reject) {
                api.createToken(tokenData, function (status, data) {
                    if (status === 200 || status === 201) {
                        resolve(data);
                    }
                    reject(new Error('Token cannot be generated at this moment'));
                });
            });
        }
    }
});
