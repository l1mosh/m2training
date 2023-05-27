/*browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/view/payment/default',
        'mage/url',
        'Magento_Checkout/js/action/redirect-on-success'
    ],
    function (
        Component,
        url,
        redirectOnSuccess
    ) {
        'use strict';

        var mode = window.checkoutConfig.payment.super_payment_gateway.mode;
        var debug = window.checkoutConfig.payment.super_payment_gateway.debug;

        return Component.extend({
            defaults: {
                template: 'Superpayments_SuperPayment/payment/form',
                redirectAfterPlaceOrder: true
            },

            initObservable: function () {

                this._super()
                    .observe([
                        'canPlaceOrder'
                    ]);
                return this;
            },

            getCode: function() {
                return 'super_payment_gateway';
            },

            getData: function() {
                return {
                    'method': this.item.method,
                    'additional_data': {
                    }
                };
            },

            // isPlaceOrderActionAllowed: function() {
            //     return true;
            // },

            afterPlaceOrder: function() {
                redirectOnSuccess.redirectUrl = url.build('superpayment/payment/redirect');
                this.redirectAfterPlaceOrder = true;
            },

        });
    }
);
