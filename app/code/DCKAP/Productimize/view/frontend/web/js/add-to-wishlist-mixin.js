/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/url',
    'jquery-ui-modules/widget'
], function ($, url) {
    'use strict';


    var pzQty = Math.floor(Math.random() * 5);

    const widgetMixin = {
        options: {
            bundleInfo: 'div.control [name^=bundle_option]',
            configurableInfo: '.super-attribute-select',
            groupedInfo: '#super-product-table input',
            downloadableInfo: '#downloadable-links-list input',
            customOptionsInfo: '.product-custom-option',
            qtyInfo: '#qty',
            actionElement: '[data-action="add-to-wishlist"]'
        },

        /** @inheritdoc */
        _create: function () {
            this._bind();
        },

        /**
         * @private
         */
        _bind: function () {
            var options = this.options,
                dataUpdateFunc = '_updateWishlistData',
                validateProductQty = '_validateWishlistQty',
                changeCustomOption = 'change ' + options.customOptionsInfo,
                changeQty = 'change ' + options.qtyInfo,
                updateWishlist = 'click ' + options.actionElement,
                events = {},
                key;

            if ('productType' in options) {
                if (typeof options.productType === 'string') {
                    options.productType = [options.productType];
                }
            } else {
                options.productType = [];
            }

            events[changeCustomOption] = dataUpdateFunc;
            events[changeQty] = dataUpdateFunc;
            events[updateWishlist] = validateProductQty;

            for (key in options.productType) {
                if (options.productType.hasOwnProperty(key) && options.productType[key] + 'Info' in options) {
                    events['change ' + options[options.productType[key] + 'Info']] = dataUpdateFunc;
                }
            }
            this._on(events);
        },

        /**
         * @param {jQuery.Event} event
         * @private
         */
        _updateWishlistData: function (event) {
            console.log("updatewishlist data is calling")
            var dataToAdd = {},
                isFileUploaded = false,
                self = this;

            if (event.handleObj.selector == this.options.qtyInfo) { //eslint-disable-line eqeqeq
                this._updateAddToWishlistButton({}, 0);
                event.stopPropagation();

                return;
            }
            $(event.handleObj.selector).each(function (index, element) {
                if ($(element).is('input[type=text]') ||
                    $(element).is('input[type=email]') ||
                    $(element).is('input[type=number]') ||
                    $(element).is('input[type=hidden]') ||
                    $(element).is('input[type=checkbox]:checked') ||
                    $(element).is('input[type=radio]:checked') ||
                    $(element).is('textarea') ||
                    $('#' + element.id + ' option:selected').length
                ) {
                    if ($(element).data('selector') || $(element).attr('name')) {
                        dataToAdd = $.extend({}, dataToAdd, self._getElementData(element));
                    }

                    return;
                }

                if ($(element).is('input[type=file]') && $(element).val()) {
                    isFileUploaded = true;
                }
            });

            if (isFileUploaded) {
                this.bindFormSubmit();
            }
            this._updateAddToWishlistButton(dataToAdd);
            event.stopPropagation();
        },

        /**
         * @param {Object} dataToAdd
         * @private
         */
        _updateAddToWishlistButton: function (dataToAdd, executeSaveCanvas = 1) {
            console.log("_updateAddToWishlistButton is calling")
            var self = this;

            if (executeSaveCanvas == 1 && typeof getArtwork == 'function' && document.getElementsByName('edit_id')) {
                var dataUrl = getArtworkImage('jpeg');
                var saveCanvasUrl = url.build('productimize/index/savecanvas');
                //this.assignPZCartProperties("");
                var cartPropties = setPzCartPropertiesData();
                console.log(pzSelectedOptions, pzSelectedOptions)
                var glassDimention = getGlassDimention(null);
                let artworkData = {};
                artworkData['glassDimention'] = glassDimention;
                artworkData['pzSelectedOptions'] = pzSelectedOptions;
                if (jQuery('.medium-select-elem').length > 0 && jQuery('.medium-select-elem option:selected').val()) {
                    artworkData['pzSelectedOptions']['medium'] = {
                        'sku': jQuery('.medium-select-elem option:selected').val()
                    }
                }
                if (jQuery('.treatment-select-elem').length > 0 && jQuery('.treatment-select-elem option:selected').val()) {
                    artworkData['pzSelectedOptions']['treatment'] = {
                        'sku': jQuery('.treatment-select-elem option:selected').val()
                    }
                }
                artworkData['productUrl'] = productImg;
                artworkData['productId'] = productId;

                $.ajax({
                    showLoader: true,
                    url: saveCanvasUrl,
                    data: {
                        artworkData: JSON.stringify(artworkData)
                        //dataUrl: dataUrl,

                    },
                    type: "POST",
                    async: false,
                    cache: false,
                    success: function (imgData) {
                        //self.assignPZCartProperties(data);
                        console.log("Savecanvas Data", imgData)
                        // var cartPropties = JSON.parse($('#pz_cart_properties').val());
                        // var imgValue = (imgData  && imgData.imageUrl) ? imgData : null;
                        // if (imgValue && 'imageUrl' in imgValue) {

                        //     cartPropties['CustomImage'] = imgValue['imageUrl'];
                        //     console.log("cartPropties ", cartPropties)
                        //     console.log("cartPropties ", cartPropties['CustomImage'])

                        // }
                        cartPropties['CustomImage'] = (imgData && imgData.imageUrl) ? imgData.imageUrl : productImg;
                        $('[data-action="add-to-wishlist"]').each(function (index, element) {
                            var params = $(element).data('post');

                            if (!params) {
                                params = {
                                    'data': {}
                                };
                            }

                            params.data = $.extend({}, params.data, dataToAdd, {
                                'qty': $(self.options.qtyInfo).val()
                            });

                            if ($('#pz_cart_properties').val() != '') {

                                params.data = $.extend({}, params.data, dataToAdd, {
                                    'pz_cart_properties': JSON.stringify(cartPropties)
                                });

                                params.data = $.extend({}, params.data, dataToAdd, {
                                    'edit_id': 1
                                });

                                params.data = $.extend({}, params.data, dataToAdd, {
                                    'configurator_price': $('#selling_price').val()
                                });
                                /*params.data = $.extend({}, params.data, dataToAdd, {
                                     'params_addtocart': $('#params_addtocart').val()
                                });*/

                            }
                            $(element).data('post', params);
                        });

                    }
                });
            }
            else {
                $('[data-action="add-to-wishlist"]').each(function (index, element) {
                    var params = $(element).data('post');

                    if (!params) {
                        params = {
                            'data': {}
                        };
                    }

                    params.data = $.extend({}, params.data, dataToAdd, {
                        'qty': $(self.options.qtyInfo).val()
                    });
                    $(element).data('post', params);
                });
            }
        },


        /**
         * Validate product quantity before updating Wish List
         *
         * @param {jQuery.Event} event
         * @private
         */
        _validateWishlistQty: function (event) {

            console.log("_validateWishlistQty is calling")

            var element = $(this.options.qtyInfo);

            if (!(element.validation() && element.validation('isValid'))) {
                event.preventDefault();
                event.stopPropagation();

                return;
            }
            if (typeof getArtwork == 'function' && document.getElementsByName('edit_id')) {
                console.log(pzQty, "pzQty")
                /*if (pzQty == $(this.options.qtyInfo).val()) {
                    $(this.options.qtyInfo).val(parseInt(pzQty)+1)
                }*/

                this._updateAddToWishlistButton({});

            }
        },

        /* customize button */
        getPZConfLabels: function() {
            //var value = ", Gold, <span class='hint'><i class='fa fa-info-circle' aria-hidden='true'></i> Weighted<span class='pz-tooltip-content'>Left: 1.5 Top: 1.5 Right: 1.5 Bottom: 1.5</span></span>"
            var parsedConfLabels = (pzConfigurationLabel) ? JSON.parse(pzConfigurationLabel) : null;

            var mediaLabel = (parsedConfLabels && 'medium_default_sku' in parsedConfLabels) ? parsedConfLabels["medium_default_sku"] : "Medium";
            var treatmentLabel = (parsedConfLabels && 'treatment_default_sku' in parsedConfLabels) ? parsedConfLabels["treatment_default_sku"] : "Treatment";
            var sizeLabel = (parsedConfLabels && 'size_default_sku' in parsedConfLabels) ? parsedConfLabels["size_default_sku"] : "Size";
            var frameLabel = (parsedConfLabels && 'frame_default_sku' in parsedConfLabels) ? parsedConfLabels["frame_default_sku"] : "Frame";
            var topMatLabel = (parsedConfLabels && 'top_mat_default_sku' in parsedConfLabels) ? parsedConfLabels["top_mat_default_sku"] : "Top Mat";
            var bottomMatLabel = (parsedConfLabels && 'bottom_mat_default_sku' in parsedConfLabels) ? parsedConfLabels["bottom_mat_default_sku"] : "Bottom Mat";
            var linerLabel = (parsedConfLabels && 'liner_default_sku' in parsedConfLabels) ? parsedConfLabels["liner_default_sku"] : "Liner";

            return {'mediumLabel' : mediaLabel, 'treatmentLabel': treatmentLabel, 'sizeLabel':sizeLabel, 'frameLabel': frameLabel, 'topMatLabel': topMatLabel, 'bottomMatLabel': bottomMatLabel, 'linerLabel' : linerLabel}
        },
        assignPZCartProperties: function (imageValue) {
            var encodedPZCartProperties = document.getElementById('pz_cart_properties').value;
            var imageData = (imageValue) ? JSON.parse(imageValue) : null;
            var pzConfLabels = this.getPZConfLabels();

            var pzCartProperties = null;

            if (encodedPZCartProperties) {
                pzCartProperties = JSON.parse(encodedPZCartProperties)
            }

            var outputData = {}, pzCartOutputData = {};
            var loopInc = 0;


            for (const property in pzSelectedOptions) {
                loopInc++;
                const data = pzSelectedOptions[property];

                if (data.sku) {
                    outputData[property] = data.sku;

                    outputData[property] += data['color'] ? ', ' + data['color'] : '';
                    if (property == "frame") {
                        outputData['frame'] += data['width'] ? ', ' + data['width'] + "″" : '';
                    } else if (property == "topMat" || property == "bottomMat") {
                        if (data.width) {
                            if (Object.keys(data.width).length > 0 && data.width.left == data.width.right && data.width.right == data.width.top && data.width.top == data.width.bottom) {
                                outputData[property] += data['width'] ? ', ' + data['width']['left'] + "″" : '';
                            } else {
                                var padding = "<span class='hint'><i class='fa fa-info-circle' aria-hidden='true'></i> Weighted<span class='pz-tooltip-content'>";
                                padding += 'Left: ' + data.width.left + "″, " + 'Right: ' + data.width.right + "″, " + 'Top: ' + data.width.top + "″, " + 'Bottom: ' + data.width.bottom + "″ ";
                                padding += "</span></span>";
                                outputData[property] += padding;
                            }

                        }
                    }
                }
            }
            var glassDimention = getGlassDimention(null);
            if (loopInc == Object.keys(pzSelectedOptions).length) {


                pzCartOutputData[pzConfLabels.mediumLabel] = (jQuery('.medium-select-elem')) ? jQuery('.medium-select-elem option:selected').text() : 'No Medium'
                pzCartOutputData[pzConfLabels.treatmentLabel] = (jQuery('.treatment-select-elem')) ? jQuery('.treatment-select-elem  option:selected').text() : 'No Treatment'
                pzCartOutputData[pzConfLabels.sizeLabel] = ((glassDimention[0]) ? glassDimention[0] : 100) + '×' + ((glassDimention[1]) ? glassDimention[1] : 100);
                pzCartOutputData[pzConfLabels.frameLabel] = (outputData['frame']) ? outputData['frame'] : 'No Frame';
                pzCartOutputData[pzConfLabels.topMatLabel] = (outputData['topMat']) ? outputData['topMat'] : 'No Top Mat';
                pzCartOutputData[pzConfLabels.bottomMatLabel] = (outputData['bottomMat']) ? outputData['bottomMat'] : 'No Bottom Mat';
                pzCartOutputData[pzConfLabels.linerLabel] = (outputData['liner']) ? outputData['liner'] : 'No Liner';
                pzCartOutputData['Artwork Color'] = (jQuery('#pz-text')) ? jQuery('#pz-text').val() : 'No Artwork Color';
                pzCartOutputData['Sidemark'] = (jQuery('.pz-textarea')) ? jQuery('.pz-textarea').val() : 'No Sidemark';


                if (imageData &&  'imageUrl' in imageData) {
                    pzCartOutputData['CustomImage'] = imageData.imageUrl;
                }
                document.getElementById('pz_cart_properties').value = JSON.stringify(pzCartOutputData);
            }
        }
    };

    return function (widget) {
        $.widget('mage.addToWishlist', widget, widgetMixin);

        return $.mage.addToWishlist;
    }
});
