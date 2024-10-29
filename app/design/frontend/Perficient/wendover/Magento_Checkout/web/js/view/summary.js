/**
 * File used to display the lead-time notification
 * message at checkout summary
 */
define([
    'uiComponent',
    'Magento_Checkout/js/model/totals'
], function (Component, totals) {
    'use strict';

    return Component.extend({
        isLoading: totals.isLoading,
        getLeadTime: window.checkoutConfig.quoteData.lead_time
    });
});
