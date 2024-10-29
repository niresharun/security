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
], function ($, urlBuilder, translate, customerData) {
    'use strict';

    (function ($) {

        var loadCart = true;
        const classLink = 'add-collection-link';

        $(document).ready(function () {
            $(document).on('click', '#collection-link', function () {
                var productId, currentLayout, optionSelected;

                productId = $('#collection-link').attr('class');
                currentLayout = $('.current_front_layout').attr('data-id');
                optionSelected = null;
                if (productId === classLink) {
                     productId = $('#product_addtocart_form').find('[name^="product"]')[0].value;
                }

                if ($('#product_addtocart_form').find('select.product-custom-option').length) {
                    optionSelected = $('#product_addtocart_form').find('select.product-custom-option')[0].value;
                }

                $('#type_of_projects  optgroup option:selected').removeAttr("selected");
                $.ajax({
                    url: urlBuilder.build('mycheckout/product/addtocollection'),
                    type: 'POST',
                    data: {
                        product: productId,
                        customizer: true,
                        optionSelected: optionSelected,
                        currentLayout: currentLayout
                    },
                    showLoader: true,
                    cache: false,
                    success: function (data) {
                        loadCart = true;
                        /* MiniCart reloading */
                        var sections = ['cart'];

                        customerData.reload(sections, true);

                    },
                    error: function (request, error) {
                        console.log('Error');
                    }
                });
                return false;
            });

            $('#product-addtocart-button').on('click', function () {
                loadCart = true;
            });
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
