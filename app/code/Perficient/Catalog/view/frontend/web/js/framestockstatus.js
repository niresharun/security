/**
 * JS to get response from CheckFrameStock Controller and display
 * Stock Status on PDP.
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Amin Akhtar
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
define([
    "jquery",
    "Magento_Customer/js/customer-data",
    "jquery/ui"
], function ($, customerData) {
    "use strict";

    let defaultConfig = {};

    function main(config, element) {
        config = $.extend(defaultConfig, config);
        // Due to some issue customer.isLoggedIn() not working
        // So checked loggedIn by first name
        if (config.customerIsLogin) {
            var AjaxUrl = config.AjaxUrl;
            var defaultFrameSku = config.defaultFrameSku;
            var currentPdpUrl = window.location.href.indexOf(config.currentPdpUrl) !== -1
                ? window.location.href
                : config.currentPdpUrl;

            if (AjaxUrl && defaultFrameSku && currentPdpUrl) {
                    $.ajax({
                        url: AjaxUrl,
                        type: "POST",
                        dataType: 'json',
                        data: {defaultFrameSku: defaultFrameSku, currentPdpUrl: currentPdpUrl},
                    }).done(function (response) {
                        if (response && response.error === true) {
                            return;
                        }
                        if (response && response.is_in_stock === false) {
                            $('#frame-stock-status-main').show();
                            if (response.notify_url) {
                                var notifyElement = $('#notify_url_id');
                                if (notifyElement) {
                                    notifyElement.attr('href', response.notify_url);
                                }
                            }
                            if (response.days_to_in_stock === true && response.message_one && response.message_two) {
                                $('#bis_main_two').hide();
                                $('#bis_one_mess_one').text(response.message_one);
                                $('#bis_one_mess_two').text(response.message_two);
                                $('.bis_main_colone_hide').show();
                            } else {
                                if (response.message_one) {
                                    $('.bis_main_colone_hide').hide();
                                    $('#bis_main_two').show();
                                    $('#bis_two_mess_one').text(response.message_one);
                                }
                            }
                        } else {
                            $('#frame-stock-status-main').hide();
                        }
                    });
            }
        }
    }

    return main;
});
