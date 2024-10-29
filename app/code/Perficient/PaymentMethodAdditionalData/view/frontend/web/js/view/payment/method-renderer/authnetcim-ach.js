/**
 * Custom Module to store Additional Payment Data to Quote and Order in Payment Tables
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sachin Badase <sachin.badase@perficient.com>
 * @keywords: Payment Data to Quote and Order in Payment Tables
 */
/* @api */
/*browser:true*/
/*global define*/
define(
    [
        'ko',
        'Perficient_PaymentMethodAdditionalData/js/view/payment/method-renderer/ach'
    ],
    function (ko, Component) {
        'use strict';
        var config = window.checkoutConfig.payment.authnetcim_ach;
        return Component.extend({
            defaults: {
                save: config ? config.canSaveCard && config.defaultSaveCard : false,
                selectedCard: config ? config.selectedCard : '',
                storedCards: config ? config.storedCards : {},
                achAccountTypes: config ? config.achAccountTypes : {},
                logoImage: config ? config.logoImage : false,
                achImage: config ? config.achImage : false
            },

            initVars: function () {
                this.canSaveCard = config ? config.canSaveCard : false;
                this.forceSaveCard = config ? config.forceSaveCard : false;
                this.defaultSaveCard = config ? config.defaultSaveCard : false;
            }
        });
    }
);
