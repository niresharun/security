/**
 *
 * This module is used to create custom artwork catalogs
 * This file contains all the JS code related to my-catalog functionality.
 *
 * @category: Magento
 * @package: Perficient/MyCatalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyCatalog JS
 */
define([
    'jquery',
    'mage/translate',
    'mage/url',
    'Magento_Ui/js/modal/confirm',
    'Magento_Ui/js/modal/modal',
    'Magento_Ui/js/modal/alert',
    'jquery/ui',
    'domReady!'
], function (
    $,
    $t,
    urlBuilder,
    confirm,
    modal,
    alertMessage
) {
    'use strict';

    $(".gallery-list-item").on("click", function () {
        window.location.href=$(this).attr('data-href');
        return false;
    });

    // Cancel catalog button clicked.
    $("#cancel_catalog").on("click", function () {
        window.location = urlBuilder.build("mycatalog");
    });

    // Logo upload
    $("#catalog_logo").on("change", function () {
        let formData = new FormData();
        formData.append('logo', $(this)[0].files[0]);
        $.ajax({
            url: urlBuilder.build("mycatalog/index/fileUpload"),
            type: "POST",
            dataType: "JSON",
            contentType: false,
            enctype: 'multipart/form-data',
            processData: false,
            data: formData,
            showLoader: true,
            cache: false,
            success: function (response) {
                if (typeof(response.error) != 'undefined') {
                    if (response.error != '') {
                        alert(response.error);
                        return false;
                    } else {
                        let src = mediaUrl + 'custom_catalog/logos/' + response.name;
                        $('#wendover_logo').val(response.name);
                        $('.mycatalog_placeholder').html('<img src="' + src + '" height="100%" />').css('padding', '0');
                        $('#upload-notice').html($t('Image uploaded'));
                    }
                }
            },
            error: function (e) {
                alert(e);
            }
        });
    });

    // Delete catalog
    $(".delete-catalog").on("click", function () {
        let deleteUrl = $(this).attr("data-url");
        confirm({
            title: $t("Delete Catalog"),
            content: $t("You are about to delete your catalog. This action cannot be undone. Do you want to proceed?"),
            modalClass: "classModal",
            actions: {
                /** @inheritdoc */
                confirm: function () {
                    $.ajax({
                        url: deleteUrl,
                        showLoader: true,
                        success: function (response) {
                            window.location.href = urlBuilder.build("mycatalog/");
                        },
                        error: function (xhr, status, errorThrown) {
                            alert($t('Unable to delete catalog. Please try again later.'));
                        }
                    });
                },

                /** @inheritdoc */
                always: function (e) {
                    e.stopImmediatePropagation();
                    return false;
                }
            }
        });
    });

    // Delete Page
    $('#catalog_delete_button').on('click', function () {
        let pageData = {
            page_num: jQuery('#wendover_page_id').val(),
            catalog_id: jQuery('#wendover_catalog_id').val()
        };
        confirm({
            title: $t("Delete Page"),
            content: $t("Are you sure you want to delete this page?"),
            modalClass: "classModal",
            actions: {
                /** @inheritdoc */
                confirm: function () {
                    $.ajax({
                        url: deletePageUrl,
                        type: "POST",
                        dataType: "JSON",
                        data: pageData,
                        showLoader: true,
                        success: function (response) {
                            location.reload(true);
                        },
                        error: function (xhr, status, errorThrown) {
                            alert($t('Unable to delete catalog. Please try again later.'));
                        }
                    });
                },

                /** @inheritdoc */
                always: function (e) {
                    e.stopImmediatePropagation();
                    return false;
                }
            }
        });
    });

    // Email catalog
    $(document).ready(function () {
        let catalogId;
        $(".email-catalog").on("click", function () {
            catalogId = $(this).attr('data-id');
            $("#email-catalog-modal").modal('openModal');
        });

        // Send Email
        $("#send_email").on("click", function () {
            let isFormValidated = $('#email_catalog_form').validation('isValid');
            if (isFormValidated) {
                let formData = {
                    recipient: $('#recipient').val(),
                    message: $('#message').val(),
                    catalog_id: catalogId
                };

                $.ajax({
                    url: urlBuilder.build("mycatalog/index/index"),
                    type: "POST",
                    dataType: "JSON",
                    data: formData,
                    showLoader: true,
                    beforeSend: function() {
                        $('body').trigger('processStart');
                    },
                    success: function (response) {
                        if (response.success == false) {
                            alertMessage({
                                content: 'Unable to send email catalog. Please try again later.',
                                actions: {
                                    always: function(){}
                                }
                            });
                        } else if (response.large_file_size == true) {
                            var limit = response.limit;
                            var currentUrl = window.location.href;
                            if (currentUrl.indexOf("catalog_id") !== -1) {
                                alertMessage({
                                    content: 'Your catalog file exceeds the maximum limit of '+limit+'MB. Please use "Create PDF" option to download the file.',
                                    actions: {
                                        always: function(){
                                            $("#email-catalog-modal").modal("closeModal");
                                        }
                                    }
                                });
                            } else {
                                alertMessage({
                                    content: 'Your catalog file exceeds the maximum limit of '+limit+'MB. Please use "Download" option to download the file.',
                                    actions: {
                                        always: function(){
                                            $("#email-catalog-modal").modal("closeModal");
                                        }
                                    }
                                });
                            }
                        } else {
                            $("#email-catalog-modal").modal("closeModal");
                        }
                        $('body').trigger('processStop');
                    },
                    error: function (xhr, status, errorThrown) {
                        alertMessage({
                            content: 'Unable to send email catalog. Please try again later.',
                            actions: {
                                always: function(){}
                            }
                        });
                        $('body').trigger('processStop');
                    }
                });
            }
        });

        //
    });

    //create new catalog popup
    if ($('#catalog-popup').length > 0){
        var options = {
            type: 'popup',
            responsive: true,
            modalClass: 'my-catalog-popup',
            focus: '.action-close',
            title: 'Create New Catalog',
            innerScroll: true,
            opened: function($Event) {
                $(".modal-footer").remove();
            }
        };

        var popup = modal(options, $('#catalog-popup'));
        $("#createnew-catalog").click(function() {
            $('#catalog-popup').modal('openModal');
            return false;
        });
    }
});
