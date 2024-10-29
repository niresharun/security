/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/* @api */
define([
    'jquery',
    'ko',
    'Perficient_Payment/js/action/checkout/cart/totals',
    'jquery-ui-modules/widget'
], function ($, ko, totals) {
    'use strict';

    var isLoading = ko.observable(false);
    const authnetcimCode = 'authnetcim';

    $.widget('mage.creditCardType', {
        options: {
            typeCodes: ['SS', 'SM', 'SO'] // Type codes for Switch/Maestro/Solo credit cards.
        },
        /**
         * Bind change handler to select element and trigger the event to show/hide
         * the Switch/Maestro or Solo credit card type container for those credit card types.
         * @private
         */
        _create: function () {
            this.element.on('change', $.proxy(this._toggleCardType, this)).trigger('change');

            //Custom Code Start
            var selectElement = $('#authnetcim-card-id').length;
            var radioElement = $('#authnetcim').length;
            if (selectElement && radioElement)
            {
                var authnetcimSelected = $('#authnetcim').is(':checked');
                if (authnetcimSelected) {
                    var selectedCardLabel = $('#authnetcim-card-id :selected').text();
                    var selectedCardLabelArr = selectedCardLabel.split(" XXXX");
                    var selectedCardTitle = selectedCardLabelArr[0];

                    //On Page Load Call
                    if (selectedCardTitle && selectedCardTitle != 'Add new card') {
                        totals(isLoading, authnetcimCode + "::" + selectedCardTitle);
                    }
                }
            }

            //On Change Saved Cards
            this.element.on('change', $.proxy(this._getPaymentMethodFee, this));
            //Custom Code End
        },

        /**
         * Toggle the Switch/Maestro and Solo credit card type
         * container depending on which
         * credit card type is selected.
         * @private
         */
        _toggleCardType: function () {
            $(this.options.creditCardTypeContainer)
                .toggle($.inArray(this.element.val(), this.options.typeCodes) !== -1);
        },

        /**
         * Custom function to call totals with card name
         * based on card selected
         * @private
         */
        _getPaymentMethodFee: function () {
            var selectElement = $('#authnetcim-card-id').length;
            if (selectElement) {
                var selectedCardLabel = $('#authnetcim-card-id :selected').text();
                var selectedCardLabelArr = selectedCardLabel.split(" XXXX");
                var selectedCardTitle = selectedCardLabelArr[0];

                if (selectedCardTitle && selectedCardTitle != 'Add new card') {
                    totals(isLoading, authnetcimCode + "::" + selectedCardTitle);
                } else if (selectedCardTitle && selectedCardTitle == 'Add new card') {
                    var ccNumberElement = $('#authnetcim-cc-number').length;
                    if (ccNumberElement) {
                        var newCardNumber = $('#authnetcim-cc-number').val();
                        if (newCardNumber) {
                            $('#authnetcim-cc-number').val('');
                            /**
                             * Fix for payment method fee not getting calculated when
                             * switch from pay on terms or e-check to creditcard again
                             */
                            $('#authnetcim-cc-number').trigger("change");
                            if ($("li.item").hasClass("_active")) {
                                $('li.item').removeClass('_active');
                            }
                        }
                    }
                }
            }
        }
    });

    return $.mage.creditCardType;
});