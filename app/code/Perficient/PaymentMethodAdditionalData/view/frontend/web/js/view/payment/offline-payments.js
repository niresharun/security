/**
 * Custom Module to store Additional Payment Data to Quote and Order in Payment Tables
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sachin Badase <sachin.badase@perficient.com>
 * @keywords: Payment Data to Quote and Order in Payment Tables
 */
/*browser:true*/
/*global define*/
define(
    [
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
                type: 'checkmo',
                component: 'Perficient_PaymentMethodAdditionalData/js/view/payment/method-renderer/checkmo-method'
            },
            {
                type: 'banktransfer',
                component: 'Perficient_PaymentMethodAdditionalData/js/view/payment/method-renderer/banktransfer-method'
            },
            {
                type: 'cashondelivery',
                component: 'Perficient_PaymentMethodAdditionalData/js/view/payment/method-renderer/cashondelivery-method'
            },
            {
                type: 'purchaseorder',
                component: 'Perficient_PaymentMethodAdditionalData/js/view/payment/method-renderer/purchaseorder-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({});
    }
);
