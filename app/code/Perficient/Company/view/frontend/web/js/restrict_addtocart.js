/**
 * Company module for add to cart restrict .
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani <Manish.Bhojwani@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */
define(
    [
        'uiComponent',
        'jquery'
    ],
    function (
        Component,
        $
    ) {
        'use strict';

        var main = {
            // Function to restrict add to cart for guest, customers customer, 0x price multiplier.
            restrictAddtoCart: function () {
                var $addToCart, $addToCartPDP, $qtyPDP, $relatedProduct, base_url, cartRestrictUrl;

                $addToCart = $('.product-item-actions .actions-primary');
                $addToCartPDP = $('.product-add-form .box-tocart .primary.tocart');
                $qtyPDP = $('.product-add-form .box-tocart .field.qty');
                $relatedProduct = $('.add-collection-link');
                if ($addToCart.length || $addToCartPDP.length || $relatedProduct.length) {
                    base_url = window.location.origin;
                    cartRestrictUrl = base_url + '/perficientcompany/cart/restrict';
                    $.ajax(
                        {
                            url: cartRestrictUrl,
                            type: 'get',
                            data: [],
                            dataType: 'json',
                            context: $('body'),
                            beforeSend: function () {
                                $addToCart.hide();
                                $addToCartPDP.hide();
                                $qtyPDP.hide();
                            }
                        }
                    ).done(
                        function (response) {
                            if (response.showcart === true) {
                                $addToCart.show();
                                $addToCartPDP.show();
                                $qtyPDP.show();
                                $relatedProduct.show();
                            } else {
                                $relatedProduct.hide();
                            }
                        }
                    );
                }
            },

            restrictWidgetAddtoCart: function () {
                main.restrictAddtoCart();
            }
        };


        $(function () {
            main.restrictAddtoCart();
        });

        return main;
    }
);
