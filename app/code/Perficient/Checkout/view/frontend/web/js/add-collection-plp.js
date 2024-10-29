<!--
/**
 * This module is used to prepare add to collection configurable url on checkout
 *
 * @category: Magento
 * @package: Perficient/Checkout
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Trupti Bobde <trupti.bobde@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Checkout
 */
-->
require([
    'jquery',
    'mage/url',
    'mage/translate',
    'Magento_Customer/js/customer-data'
], function ($,urlBuilder, translate,customerData) {
    'use strict';

    (function ($) {

        var loadCart = true;

        $(document).ready(function () {
            $(document).on('click','#collection-link',function () {
                var productId, itemId, optionSelected, reload, pageUrl, match;

                productId =  $(this).attr('class');
                itemId =  $(this).attr('item-id');
                optionSelected = null;
                reload = false;
                pageUrl = $(location).attr('href');
                match = 'checkout/cart';
                if (pageUrl.indexOf(match) != -1) {
                    reload = true;
                }
                $.ajax({
                    url: urlBuilder.build('mycheckout/product/addtocollection'),
                    type: 'POST',
                    data: {
                        product : productId,
                        customizer : true,
                        optionSelected: optionSelected,
                        id: itemId
                    },
                    showLoader: true,
                    cache: false,
                    success: function (data) {
                        loadCart = true;

                        /* MiniCart reloading */
                        var sections = ['cart'];

                        customerData.reload(sections,true);
                        if (reload) {
                            location.reload();
                        }

                    },
                    error: function (request, error)
                    {
                        console.log('Error');
                    }
                });
                return false;            });
        });


        $(document).ajaxComplete(function (event, xhr, settings) {
            if (loadCart) {
                if (settings.url.indexOf('customer/section/load/?sections=cart') > 0) {

                    loadCart = false;

                    /* MiniCart reloading */
                    var sections = ['cart'];

                    customerData.reload(sections, true);
                }
            }
        });
    })(jQuery);

});
