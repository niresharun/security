/**
 *
 * @category: Magento
 * @package: Perficient/Wishlist
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Amin Akhtar <Amin.akhtar@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Wishlist JS
 */
define([
    'jquery',
    'mage/translate',
    'mage/url',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/modal',
    'jquery/ui',
    'domReady!'
], function (
    $,
    $t,
    urlBuilder,
    confirm,
    modal
) {
    'use strict';
    //Add By SKU popup
    if ($('#add-by-sku').length > 0) {
        var options = {
            type: 'popup',
            responsive: true,
            innerScroll: true,
            title: 'Add Products By SKU',
            opened: function ($Event) {
                $(".modal-footer").remove();
            }
        };

        var popup = modal(options, $('#add-by-sku-popup'));
        $("#add-by-sku").on('click', function (e) {
            e.preventDefault();
            $(".modal-footer").remove();
            $("#add-by-sku-popup").modal("openModal");
        });

        $("#close_add_by_sku").on('click', function () {
            $("#add-by-sku-popup").modal("closeModal");
        });

        $("#add-by-sku-form").on('submit', function (d) {
            d.preventDefault();
            var skus = $('#sku_to_add').val();
            var postData = $(this).serialize();
            var actionUrl = $(this).attr('action');

            if (skus && actionUrl) {
                $.ajax({
                    url: actionUrl,
                    type: "POST",
                    dataType: "JSON",
                    showLoader: true,
                    data: postData,
                    success: function (jdata) {
                        $("#add-by-sku-popup").modal("closeModal");
                        setTimeout(function () {
                            if (jdata.redirectUrl) {
                                window.location.href = jdata.redirectUrl;
                            } else {
                                location.reload();
                            }
                        },3000);
                    },
                    error: function (xhr, status, errorThrown) {
                        $("#add-by-sku-popup").modal("closeModal");
                        $('body').trigger('processStop');
                    }
                });
            }
        })
    }
});
