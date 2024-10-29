/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/url',
    'jquery-ui-modules/widget',
], function ($, url) {
    'use strict';

    return function (widget) {

        $.widget('mage.catalogAddToCart', widget, {
            submitForm: function (form) {
                var self = this;
                console.log("submit before");
                var saveCanvasUrl = url.build('productimize/index/savecanvas');
                //var canvas = document.getElementById("pz-canvas");
                //var dataUrl = canvas.toDataURL("image/jpeg");
                if (typeof getArtwork == 'function' && document.getElementsByName('edit_id')) {
                    var color = '';
                    var ischecked = $(".showartworktext").is(':checked');
                    if (ischecked == false) {
                        color = '';
                    }   else {
                        if($('#pz-text').val().length == 0) {
                            $('.pz-custom-items').removeClass('open');
                            $('.pz-custom-image-color').parents('.pz-custom-items').addClass('open');
                            $('.pz-custom-image-color').append('<div class="customred">*Please enter Artwork Color to continue</div>')
                            $('.customred').fadeOut(5000, function () {
                                $(this).remove();
                            });
                            return false;
                        }
                    }
                    var dataUrl = getArtworkImage('jpeg');

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
                    var pzCartData = setPzCartPropertiesData();


                    $.ajax({
                        showLoader: true,
                        url: saveCanvasUrl,
                        data: {
                            artworkData: JSON.stringify(artworkData)
                            //dataUrl: dataUrl
                        },
                        type: "POST",
                        async:false,
                        cache:false,
                        success: function (data) {
                            console.log("submit 1");
                            console.log(data);
                            /*self.assignPZCartProperties(data);
                            setTimeout(function () {
                                self.ajaxSubmit(form);
                            }, 3000)*/                            
                            pzCartData['CustomImage'] = (data && data.imageUrl) ? data.imageUrl : productImg;
                            console.log("pzCartData ", pzCartData);
                            document.getElementById('pz_cart_properties').value = JSON.stringify(pzCartData);
                            setTimeout(function () {
                                self.ajaxSubmit(form);
                            }, 100)
                            console.log("submit last");
                        }
                    });
                } else {
                    self.ajaxSubmit(form);
                }
            },
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
                var imageData = (imageValue && imageValue.imageUrl) ? imageValue : null;
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


                    if (imageData.imageUrl) {
                        pzCartOutputData['CustomImage'] = imageData.imageUrl;
                    }
                    document.getElementById('pz_cart_properties').value = JSON.stringify(pzCartOutputData);
                }
            }
        });

        return $.mage.catalogAddToCart;
    }
});
