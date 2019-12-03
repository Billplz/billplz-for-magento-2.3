/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default'
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            redirectAfterPlaceOrder: false,

            defaults: {
                template: 'Billplz_BillplzPaymentGateway/payment/form',
                // transactionResult: ''
            },

            // initObservable: function () {

            //     this._super()
            //         .observe([
            //             'transactionResult'
            //         ]);
            //     return this;
            // },

            getCode: function() {
                return 'billplz_gateway';
            },

            getData: function() {
                return {
                    'method': this.item.method,
                    // 'additional_data': {
                    //     'transaction_result': this.transactionResult()
                    // }
                };
            },

            afterPlaceOrder: function () {
                window.location.replace("https://www.billplz.com");
            },

            // getTransactionResults: function() {
            //     return _.map(window.checkoutConfig.payment.billplz_gateway.transactionResults, function(value, key) {
            //         return {
            //             'value': key,
            //             'transaction_result': value
            //         }
            //     });
            // }
        });
    }
);