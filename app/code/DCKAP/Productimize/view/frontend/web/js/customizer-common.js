const getMatPaddingData = (matType, pzDefaultConf = null) => {
    if (!pzDefaultConf) {
        var pzDefaultConf = null;

        if (decodedArtworkData && decodedArtworkData.default_configuration) {
            pzDefaultConf = JSON.parse(decodedArtworkData.default_configuration);
            pzDefaultConf = {...pzDefaultConf, ...decodedArtworkData}
        }
    }

    var selectedMedia = jQuery('.medium-select-elem').val();
    var selectedTreatment = jQuery('.treatment-select-elem').val();

    var matLeftPadding = 1, matRightPadding = 1, matTopPadding = 1, matBottomPadding = 1;
    if (pzDefaultConf && 'medium_default_sku' in pzDefaultConf && 'treatment_default_sku' in pzDefaultConf && pzDefaultConf.medium_default_sku != selectedMedia && pzDefaultConf.treatment_default_sku != selectedTreatment) {
        var customData = document.getElementById('pz_platform_custom_returndata').value;
        customData = (customData) ? JSON.parse(customData) : null;

        if (customData && selectedMedia != "" && selectedTreatment != "") {


            var customArtworkData = customData[selectedMedia]['treatment'][selectedTreatment]
            console.log("customArtworkData ", customArtworkData)
            if (matType == 'topMat') {
                matLeftPadding = customArtworkData['new_top_mat_size_left']
                matRightPadding = customArtworkData['new_top_mat_size_right']
                matTopPadding = customArtworkData['new_top_mat_size_top']
                matBottomPadding = customArtworkData['new_top_mat_size_bottom']
            } else {
                matLeftPadding = customArtworkData['new_bottom_mat_size_left']
                matRightPadding = customArtworkData['new_bottom_mat_size_right']
                matTopPadding = customArtworkData['new_bottom_mat_size_top']
                matBottomPadding = customArtworkData['new_bottom_mat_size_bottom']
            }
        }
        //}
    } else if (pzArtworkData) {
        console.log("pzArtworkData ", pzArtworkData)
        var decodedArtworkData = JSON.parse(pzArtworkData);
        console.log("decodedArtworkData ", decodedArtworkData)
        if (decodedArtworkData && decodedArtworkData.default_configuration) {
            pzDefaultConf = JSON.parse(decodedArtworkData.default_configuration);
            pzDefaultConf = {...pzDefaultConf, ...decodedArtworkData}

            console.log("pzDefaultConf ", pzDefaultConf)

            if (matType == "topMat") {
                matLeftPadding = pzDefaultConf['top_mat_size_left']
                matRightPadding = pzDefaultConf['top_mat_size_right']
                matTopPadding = pzDefaultConf['top_mat_size_top']
                matBottomPadding = pzDefaultConf['top_mat_size_bottom']

                matLeftPadding = (matLeftPadding > 0) ? matLeftPadding : 0;
                matRightPadding = (matRightPadding > 0) ? matRightPadding : 0;
                matTopPadding = (matTopPadding > 0) ? matTopPadding : 0;
                matBottomPadding = (matBottomPadding > 0) ? matBottomPadding : 0;
            } else {
                matLeftPadding = pzDefaultConf['bottom_mat_size_left']
                matRightPadding = pzDefaultConf['bottom_mat_size_right']
                matTopPadding = pzDefaultConf['bottom_mat_size_top']
                matBottomPadding = pzDefaultConf['bottom_mat_size_bottom'];

                matLeftPadding = (matLeftPadding > 0) ? matLeftPadding : 0;
                matRightPadding = (matRightPadding > 0) ? matRightPadding : 0;
                matTopPadding = (matTopPadding > 0) ? matTopPadding : 0;
                matBottomPadding = (matBottomPadding > 0) ? matBottomPadding : 0;
            }
        }
    }
    return [matLeftPadding, matRightPadding, matTopPadding, matBottomPadding];
}
const setPzCartPropertiesData = (customCartImage = null) => {
    console.log("pzCartPropertiesData ==> ", pzCartPropertiesData)
    let currPZCartPropertiesData = (pzCartPropertiesData) ? JSON.parse(pzCartPropertiesData) : '';
    var pzDefaultConf = null;
    var decodedArtworkData = JSON.parse(pzArtworkData);
    console.log("decodedArtworkData ", decodedArtworkData)
    if (decodedArtworkData && decodedArtworkData.default_configuration) {
        pzDefaultConf = JSON.parse(decodedArtworkData.default_configuration);
        pzDefaultConf = {...pzDefaultConf, ...decodedArtworkData}
    }

    if (currPZCartPropertiesData) {
        var returnData = {}, artworkData = {}, imageSizeParam = {};
        var elementClassNames = {
            'frame': '.frameli li.selectedFrame',
            'topMat': '.topmatli li.selectedFrame',
            'bottomMat': '.bottommatli li.selectedFrame',
            'liner': '.linerli li.selectedFrame'
        }

        if (currPZCartPropertiesData.treatment) {
            returnData[currPZCartPropertiesData.treatment] = (jQuery('.treatment-select-elem')) ? jQuery('.treatment-select-elem  option:selected').val() : 'No Treatment';
        }
        if (currPZCartPropertiesData.medium) {
            returnData[currPZCartPropertiesData.medium] = (jQuery('.medium-select-elem')) ? jQuery('.medium-select-elem option:selected').val() : 'No Medium';
        }
        // Size
        var glassDimention = getGlassDimention(null);
        if (currPZCartPropertiesData.glass_width) {
            returnData[currPZCartPropertiesData.glass_width] = (glassDimention[0]) ? glassDimention[0] : 0;
        }
        if (currPZCartPropertiesData.glass_height) {
            returnData[currPZCartPropertiesData.glass_height] = (glassDimention[1]) ? glassDimention[1] : 0;
        }

        if (currPZCartPropertiesData.image_width) {
            returnData[currPZCartPropertiesData.image_width] = returnData[currPZCartPropertiesData.glass_width] ? returnData[currPZCartPropertiesData.glass_width] :((pzDefaultConf.image_width) ? pzDefaultConf.image_width : 0);
        }
        if (currPZCartPropertiesData.image_height) {
            returnData[currPZCartPropertiesData.image_height] = returnData[currPZCartPropertiesData.glass_height] ? returnData[currPZCartPropertiesData.glass_height] :((pzDefaultConf.image_height) ? pzDefaultConf.image_height : 0);
        }

        for (const currProp in elementClassNames) {

            var currElement = jQuery(elementClassNames[currProp]);
            if (currElement) {
                if (currProp == 'frame') {
                    returnData[currPZCartPropertiesData.frame_default_sku] = jQuery(currElement).data('sku') ? jQuery(currElement).data('sku') : '';
                    returnData[currPZCartPropertiesData.frame_width] = (jQuery(currElement).data('sku') && jQuery(currElement).data('width')) ? jQuery(currElement).data('width') : '';
                    returnData[currPZCartPropertiesData.default_frame_depth] = (jQuery(currElement).data('sku') && jQuery(currElement).data('depth')) ? jQuery(currElement).data('depth') : '';
                    returnData[currPZCartPropertiesData.default_frame_color] = (jQuery(currElement).data('sku') && jQuery(currElement).data('color-frame')) ? jQuery(currElement).data('color-frame') : '';


                    artworkData['frameWidth'] = returnData[currPZCartPropertiesData.frame_width];
                    artworkData['frameType'] = (jQuery(currElement).data('sku') && jQuery(currElement).data('type')) ? jQuery(currElement).data('type') : '';

                } else if (currProp == 'topMat') {
                    var matData = getMatPaddingData("topMat", pzDefaultConf);

                    returnData[currPZCartPropertiesData.top_mat_default_sku] = jQuery(currElement).data('sku') ? jQuery(currElement).data('sku') : '';
                    returnData[currPZCartPropertiesData.top_mat_size_left] = jQuery(currElement).data('sku') ? matData[0] : '';
                    returnData[currPZCartPropertiesData.top_mat_size_right] = jQuery(currElement).data('sku') ? matData[1] : '';
                    returnData[currPZCartPropertiesData.top_mat_size_top] = jQuery(currElement).data('sku') ? matData[2] : '';
                    returnData[currPZCartPropertiesData.top_mat_size_bottom] = jQuery(currElement).data('sku') ? matData[3] : '';
                    returnData[currPZCartPropertiesData.default_top_mat_color] = (jQuery(currElement).data('sku') && jQuery(currElement).data('color-frame')) ? jQuery(currElement).data('color-frame') : '';
                }
                if (currProp == 'bottomMat') {
                    var matData = getMatPaddingData("bottomMat",  pzDefaultConf);
                    returnData[currPZCartPropertiesData.bottom_mat_default_sku] = jQuery(currElement).data('sku') ? jQuery(currElement).data('sku') : '';
                    returnData[currPZCartPropertiesData.bottom_mat_size_left] = jQuery(currElement).data('sku') ? matData[0] : '';
                    returnData[currPZCartPropertiesData.bottom_mat_size_right] = jQuery(currElement).data('sku') ? matData[1] : '';
                    returnData[currPZCartPropertiesData.bottom_mat_size_top] = jQuery(currElement).data('sku') ? matData[2] : '';
                    returnData[currPZCartPropertiesData.bottom_mat_size_bottom] = jQuery(currElement).data('sku') ? matData[3] : '';
                    returnData[currPZCartPropertiesData.default_bottom_mat_color] = (jQuery(currElement).data('sku') && jQuery(currElement).data('color-frame')) ? jQuery(currElement).data('color-frame') : '';
                }
                if (currProp == 'liner') {
                    returnData[currPZCartPropertiesData.liner_sku] = jQuery(currElement).data('sku') ? jQuery(currElement).data('sku') : '';
                    returnData[currPZCartPropertiesData.liner_width] = (jQuery(currElement).data('sku') && jQuery(currElement).data('width')) ? jQuery(currElement).data('width') : '';
                    returnData[currPZCartPropertiesData.default_liner_depth] = (jQuery(currElement).data('sku') && jQuery(currElement).data('depth')) ? jQuery(currElement).data('depth') : '';
                    returnData[currPZCartPropertiesData.default_liner_color] = (jQuery(currElement).data('sku') && jQuery(currElement).data('color-frame')) ? jQuery(currElement).data('color-frame') : '';
                }
            }
        }
        returnData[currPZCartPropertiesData.art_work_color] = (jQuery('#pz-text')) ? jQuery('#pz-text').val() : '';
        returnData[currPZCartPropertiesData.side_mark] = (jQuery('.pz-textarea')) ? jQuery('.pz-textarea').val() : '';

        artworkData['outerWidth'] = returnData[currPZCartPropertiesData.glass_width];
        artworkData['outerHeight'] = returnData[currPZCartPropertiesData.glass_height];
        artworkData['linerWidth'] = returnData[currPZCartPropertiesData.liner_width];

        var outerDimentionForItem = getOuterDimension(artworkData, 4);

        if (outerDimentionForItem && Object.keys(outerDimentionForItem).length > 0) {
            if (currPZCartPropertiesData.item_width) {
                returnData[currPZCartPropertiesData.item_width] = (outerDimentionForItem[0]) ? outerDimentionForItem[0] : 0;
            }
            if (currPZCartPropertiesData.item_height) {
                returnData[currPZCartPropertiesData.item_height] = (outerDimentionForItem[1]) ? outerDimentionForItem[1] : 0;
            }
        }
        if (returnData[currPZCartPropertiesData.glass_width] && returnData[currPZCartPropertiesData.glass_height]) {
            let params = {
                'glassWidth' : parseFloat(returnData[currPZCartPropertiesData.glass_width]),
                'glassHeight' : parseFloat(returnData[currPZCartPropertiesData.glass_height])
            };
            if (returnData[currPZCartPropertiesData.bottom_mat_default_sku] && returnData[currPZCartPropertiesData.bottom_mat_size_left] > 0) {
                params['matLeft'] = parseFloat(returnData[currPZCartPropertiesData.bottom_mat_size_left]);
                params['matRight'] = parseFloat(returnData[currPZCartPropertiesData.bottom_mat_size_right]);
                params['matTop'] = parseFloat(returnData[currPZCartPropertiesData.bottom_mat_size_top]);
                params['matBottom'] = parseFloat(returnData[currPZCartPropertiesData.bottom_mat_size_bottom]);

            }
            else if (returnData[currPZCartPropertiesData.top_mat_default_sku] && returnData[currPZCartPropertiesData.top_mat_size_left] > 0) {
                params['matLeft'] =  parseFloat(returnData[currPZCartPropertiesData.top_mat_size_left]);
                params['matRight'] = parseFloat(returnData[currPZCartPropertiesData.top_mat_size_right]);
                params['matTop'] = parseFloat(returnData[currPZCartPropertiesData.top_mat_size_top]);
                params['matBottom'] = parseFloat(returnData[currPZCartPropertiesData.top_mat_size_bottom]);
            }
            if (returnData[currPZCartPropertiesData.bottom_mat_default_sku] || returnData[currPZCartPropertiesData.top_mat_default_sku]) {
                const imageSize = getImageSize(params, 4);
                returnData[currPZCartPropertiesData.image_width] = (imageSize['width']) ? imageSize['width'] : returnData[currPZCartPropertiesData.image_width];
                returnData[currPZCartPropertiesData.image_height] = (imageSize['height']) ? imageSize['height'] : returnData[currPZCartPropertiesData.image_height];
            }
        }
        if (customCartImage) {
            returnData['CustomImage'] = customCartImage;
        }
        return returnData;
    }
}

function getOuterDimension(artworkData, decimalFraction) {
    var glassWidth = null;
    if (artworkData.outerWidth) {
        glassWidth = parseFloat(artworkData.outerWidth)
    }
    if (artworkData.frameWidth) {
        glassWidth += parseFloat(artworkData.frameWidth) * 2;
    }
    if (artworkData.linerWidth) {
        glassWidth += parseFloat(artworkData.linerWidth) * 2;
    }
    if (artworkData.frameType.toLowerCase() == "standard") {
        glassWidth -= parseFloat(0.5);
    } else if (artworkData.frameType.toLowerCase() == "floater") {
        glassWidth += parseFloat(0.25);
    }
    if (artworkData.linerWidth) {
        glassWidth -= 0.5;
    }
    var glassHeight = null;
    if (artworkData.outerHeight) {
        glassHeight = parseFloat(artworkData.outerHeight);
    }
    if (artworkData.frameWidth) {
        glassHeight += parseFloat(artworkData.frameWidth) * 2;
    }
    if (artworkData.linerWidth) {
        glassHeight += parseFloat(artworkData.linerWidth) * 2;
    }
    if (artworkData.frameType.toLowerCase() == "standard") {
        glassHeight -= parseFloat(0.5);
    } else if (artworkData.frameType.toLowerCase() == "floater") {
        glassHeight += parseFloat(0.25);
    }
    if (artworkData.linerWidth) {
        glassHeight -= parseFloat(0.5);
    }
    return [parseFloat(glassWidth).toFixed(decimalFraction), parseFloat(glassHeight).toFixed(decimalFraction)];
}
function getImageSize(params, decimalFraction = 4) {
    let imageSize = {};
    let imgWidth = 0, imgHeight = 0;
    if (params && params.matLeft && params.matRight) {
        imgWidth = params.glassWidth - (params.matLeft + params.matRight) + 0.5;
    }
    if (params && params.matTop && params.matBottom) {
        imgHeight = params.glassHeight - (params.matTop + params.matBottom) + 0.5;
    }
    imageSize = {
        'width': imgWidth.toFixed(decimalFraction),
        'height': imgHeight.toFixed(decimalFraction)
    }
    return imageSize;
}