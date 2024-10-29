/*global define*/
define(
    [
        'jquery',
        'ko',
        'Magento_Checkout/js/model/quote',
        'Perficient_Payment/js/action/checkout/cart/totals'
    ],
    function($, ko ,quote, totals) {
        'use strict';

        var isLoading = ko.observable(false);
        const authnetcimCode = 'authnetcim';

        return function (paymentMethod) {
            if (paymentMethod) {
                paymentMethod.__disableTmpl = {
                    title: true
                };
            }
            quote.paymentMethod(paymentMethod);

            /**
             * If payment method other then authcimnet the we are not passing card title
             * so that totals is getting called without card title
             * Else if payment method is authcimnet then we are calculating payment method fee
             * based on card type so we are passing card title in totals
             */
            if (paymentMethod['method'] != authnetcimCode) {
                totals(isLoading, paymentMethod['method']);
            } else if(paymentMethod['method'] == authnetcimCode){
                var selectElement = $('#authnetcim-card-id').length;
                if (selectElement) {
                    var selectedCardLabel = $('#authnetcim-card-id :selected').text();
                    var selectedCardLabelArr = selectedCardLabel.split(" XXXX");
                    var selectedCardTitle = selectedCardLabelArr[0];

                    if (selectedCardTitle && selectedCardTitle != 'Add new card') {
                        totals(isLoading, paymentMethod['method'] + "::" + selectedCardTitle);
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
                } else {
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
    }
);