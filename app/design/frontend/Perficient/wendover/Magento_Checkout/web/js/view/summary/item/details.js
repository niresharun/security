/**
 * File used to retrieve and display the the lead-time notification message at
 * checkout summary and order review summary sections.
 */
define(
    [
        'jquery',
        'uiComponent',
        'mage/translate'
    ],
    function ($, Component) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/summary/item/details'
            },
             defaultViewDetailsArray : [],
            getLeadTime: function(quoteItem) {
                var itemProduct = this.getItemProduct(quoteItem.item_id);
                return $.mage.__(itemProduct.lead_time);
            },
            isCustomized: function(quoteItem) {
                var itemProduct = this.getItemProduct(quoteItem.item_id);
                var customizedFlag = false;
                if(itemProduct.edit_id != '') {
                    customizedFlag = true;
                }
                return customizedFlag;
            },
            isWeightedTopMat: function(quoteItem) {
                var itemProduct = this.getItemProduct(quoteItem.item_id);
                var weightedTopMat = false;
                if(itemProduct.weighted_top_mat) {
                    weightedTopMat = true;
                }
                return weightedTopMat;
            },
            isWeightedBottomMat: function(quoteItem) {
                var itemProduct = this.getItemProduct(quoteItem.item_id);
                var weightedBottomMat = false;
                if(itemProduct.weighted_bottom_mat) {
                    weightedBottomMat = true;
                }

                return weightedBottomMat;
            },
            ifSurchargeItem: function(quoteItem) {
               var surchargeFlag = false;
               if(quoteItem.name == 'Surcharge Product'){
                   surchargeFlag = true;
                }
               return surchargeFlag;
            },
            getItemProduct: function(item_id) {
                var itemElement = null;
                _.each(window.checkoutConfig.totalsData.items, function(element, index) {
                    if (element.item_id == item_id) {
                        itemElement = element;
                    }
                });
                return itemElement;
            },
            getViewDefaultDetails: function(quoteItem) {
                var itemProduct = this.getItemProduct(quoteItem.item_id);
                var view_default_details = itemProduct.view_default_details;
                this.defaultViewDetailsArray = view_default_details;
                if(view_default_details){
                    var result = Object.keys(view_default_details).map((key) =>  [(key), view_default_details[key]]);
                    return result;
                }
            },
            customOptions:function(quoteItem){
                var item = this.getItemProduct(quoteItem.item_id);
                if (typeof item.view_default_details !== 'undefined') {
                    var view_default_details = item.view_default_details;
                } else {
                    var view_default_details = {};
                }
                return view_default_details;
            },
            getProductSku: function(itemId) {
                var itemsData = window.checkoutConfig.quoteItemData;
                var prodSku = null;
                itemsData.forEach(function(item) {
                    if (item.item_id == itemId) {
                        prodSku = item.sku;
                    }
                });
                if (prodSku != null) {
                    return prodSku;
                } else {
                    return '';
                }
            }
        });
    }
);
