const canvas = document.getElementById('pz-canvas')
var artwork = null;
var pzOriginalArtworkData = null;
var pzWatermarkConf = null;

/* init fabric canvas */
var fabricCanvas = new fabric.Canvas(canvas, {
    enableRetinaScaling: false,
    preserveObjectStacking: true,
    selection: false
})

function getSelectedSize() {
    var range = document.getElementsByName("rangeVal")[0].value
    var rangeDimention = range.split(/[\s″×]+/); //range.split('×');
    if (rangeDimention && rangeDimention.length > 0) {
        return rangeDimention;
    }
    return [];
}

/** NOT Needed **/
const getGlassDimentionForOuterNew = (artworkData) => {
    /* Glass Width =
        Outer Width ? Frame Width*2 - Liner Width*2 + (0.5? if frame type = Standard) ? (0.25? if frame
        type = Floater)+ (0.5? if liner is selected)*/

    var glassWidth = artworkData.outerWidth - artworkData.frameWidth * 2 - artworkData.linerWidth * 2;

    if (artworkData.frameType.toLowerCase() == "standard") {
        glassWidth += 0.5;
    } else if (artworkData.frameType.toLowerCase() == "floater") {
        glassWidth -= 0.25;
    } else if (artworkData.linerWidth) {
        glassWidth += 0.5;
    }

    var glassHeight = artworkData.outerHeight - artworkData.frameWidth * 2 - artworkData.linerWidth * 2;

    if (artworkData.frameType.toLowerCase() == "standard") {
        glassHeight += 0.5;
    } else if (artworkData.frameType.toLowerCase() == "floater") {
        glassHeight -= 0.25;
    } else if (artworkData.linerWidth) {
        glassHeight += 0.5;
    }
    return [glassWidth, glassHeight];

}

function setPZSelectedOptions(data, canvasUpdate=true) {
    console.log("setPZSelectedOptions ", pzSelectedOptions)
    if (canvasUpdate != false && data.name && data.name == "size") {
        if (data.name == "size") {
            if ('frames' in pzSelectedOptions) {
                delete pzSelectedOptions['frames'];
            }
            if ('topMat' in pzSelectedOptions)
                delete pzSelectedOptions['topMat'];
            if ('bottomMat' in pzSelectedOptions)
                delete pzSelectedOptions['bottomMat'];
            if ('liner' in pzSelectedOptions)
                delete pzSelectedOptions['liner'];



            console.log("******************")
            console.log("pzSelectedOptions ", pzSelectedOptions)
            console.log("*****************", canvasUpdate)

            if (Object.keys(pzSelectedOptions).length > 0) {
                modifyCanvas();
            }

        }
    }
    else {
        pzSelectedOptions[data.name] = {
            'sku': (data.sku) ? data.sku : '',
            'displayName': (data.displayName) ? data.displayName : 'None',
            'lengthImage': (data.lengthImage) ? (data.lengthImage) : '',
            'cornerImage': (data.cornerImage) ? (data.cornerImage) : '',
        }
        if (data.width) {
            pzSelectedOptions[data.name].width = data.width;
        }
        if (data.color) {
            pzSelectedOptions[data.name].color = data.color;
        }
        console.log("******************")
        console.log("pzSelectedOptions ", pzSelectedOptions)
        console.log("*****************", canvasUpdate)

        if (canvasUpdate !=false) {
            modifyCanvas();
        }

    }
}

async function modifyCanvas() {
    console.log("MODIFY CANVAS")

    var range = document.getElementsByName("rangeVal")[0].value
    //var rangeDimention = range.split('×')
    var rangeDimention = range.split(/[\s″×]+/);

    let artworkData = {}

    var loopInc = 0;

    for (const property in pzSelectedOptions) {
        loopInc++;

        const data = pzSelectedOptions[property];

        if (property == 'frame' && data.sku && data.sku != "" && data.sku.trim().toLowerCase() != "no frame") {
            //var framePath = 'https://devcloud.productimize.com/productimizedemo/perficientJS/images/frames/renderer_';
            if (data.cornerImage && data.lengthImage) {
                /*artworkData['frame'] = {
                    sideImage: framePath + data.sku + '_length1.PNG',
                    cornerImage: framePath + data.sku + '_corner1.PNG',
                    width: parseFloat(data.width)
                }*/

                artworkData['frame'] = {
                    sideImage: data.lengthImage,
                    cornerImage: data.cornerImage,
                    width: parseFloat(data.width)
                }
            }
        } else if (property == 'liner' && data.sku && data.sku != "" && data.sku.trim().toLowerCase() != "no liner" ) {
            var linerPath = 'https://devcloud.productimize.com/productimizedemo/perficientJS/images/liner/renderer_';

            /*artworkData["liner"] = {
                sideImage: linerPath + data.sku + '_length1.png',
                cornerImage: linerPath + data.sku + '_corner1.png',
                width: 2
            }*/

            if (data.cornerImage && data.lengthImage) {

                artworkData['liner'] = {
                    sideImage: data.lengthImage,
                    cornerImage: data.cornerImage,
                    width: parseFloat(data.width)
                }

            }


        } else if ( (property == 'topMat' || property == 'bottomMat') && data.sku && data.sku != ""  &&  (!data.sku .trim().toLowerCase().includes('no ')) && data.lengthImage && data.lengthImage !="") {
            var topMatProperty = null;

            if (('topMat' in pzSelectedOptions) && pzSelectedOptions['topMat']  && pzSelectedOptions['topMat'].sku && pzSelectedOptions['topMat'].sku != "" &&  (!pzSelectedOptions['topMat'].sku .trim().toLowerCase().includes('no '))) {
                topMatProperty = pzSelectedOptions['topMat']
            }

            var padding = await getMatWidth(property, topMatProperty);
            var matPadding = JSON.parse(JSON.stringify(padding));

            if (property == 'topMat') {
                if (matPadding && Object.keys(matPadding).length > 0 && matPadding.left >0 && matPadding.top>0) {
                    console.log("topmat data ", matPadding);
                    pzSelectedOptions[property].width = matPadding;
                }
            }
            else {
                if (topMatProperty && Object.keys(topMatProperty).length > 0) {
                    pzSelectedOptions[property].width = {
                        'left':(parseFloat(pzSelectedOptions["topMat"].width.left) + parseFloat(padding.left)).toFixed(2),
                        'right':(parseFloat(pzSelectedOptions["topMat"].width.right) + parseFloat(padding.right)).toFixed(2),
                        'top':(parseFloat(pzSelectedOptions["topMat"].width.top) + parseFloat(padding.top)).toFixed(2),
                        'bottom':(parseFloat(pzSelectedOptions["topMat"].width.bottom) + parseFloat(padding.bottom)).toFixed(2)
                    }
                }
                else {

                    pzSelectedOptions[property].width = {
                        'left': parseFloat(padding.left).toFixed(2),
                        'right':parseFloat(padding.right).toFixed(2),
                        'top': parseFloat(padding.top).toFixed(2),
                        'bottom':parseFloat(padding.bottom).toFixed(2)
                    }

                }
            }

            console.log("padding ", padding)

            var matPath = 'https://devcloud.productimize.com/productimizedemo/perficientJS/images/mats/';
            if (padding && Object.keys(padding).length > 0 && padding.left >0 && padding.top>0) {
                //if (data.lengthImage) {
                artworkData[property] = {
                    sideImage: data.lengthImage,
                    width: {left: padding.left, right: padding.right, top: padding.top, bottom: padding.bottom},

                }
                //}
            }
        } else if (property == 'treatment' && data.lengthImage && data.lengthImage != "" ) {
            //var treatmentPath = 'https://devcloud.productimize.com/productimizedemo/perficientJS/images/treatment/'+ data['sku'].toLowerCase() +'.PNG';
            artworkData["treatment"] = {
                url: data.lengthImage,
                width: (parseFloat(data.width) > 0) ? parseFloat(data.width) : 0.1,
            }
        }
    }

    if (loopInc == Object.keys(pzSelectedOptions).length) {
        if (!'treatment' in artworkData) {
            artworkData['treatment'] = null;
        }
        if (!'frame' in artworkData) {
            artworkData['frame'] = null;
        }
        if (!'liner' in artworkData) {
            artworkData['liner'] = null;
        }
        if (!'topMat' in artworkData) {
            artworkData['topMat'] = null;
        }
        if (!'bottomMat' in artworkData) {
            artworkData['bottomMat'] = null;
        }

        artworkData["image"] = {
            url: productImg,
            dimension: {x: rangeDimention[0] ? parseFloat(rangeDimention[0]) : 15, y: rangeDimention[1] ? parseFloat(rangeDimention[1]) : 15},
        }
        if (pzWatermarkConf && Object.keys(pzWatermarkConf).length > 0) {
            artworkData["watermark"] = {
                url: pzWatermarkConf['url'],
                position : (pzWatermarkConf['position']) ? pzWatermarkConf['position'] : 'center',
                opacity : (pzWatermarkConf['opacity']) ? pzWatermarkConf['opacity'] : 100,
                dimension : {
                    x: (pzWatermarkConf['width']) ? pzWatermarkConf['width'] : 100,
                    y: (pzWatermarkConf['height']) ? pzWatermarkConf['height'] : 100,
                }
            };
        }

        artworkData["name"] = 'main-canvas-container';



        jQuery('body').trigger('processStart');
        //createArtwork(artworkData).then(() => {
        //artwork.create(artworkData).then(() => {
        //jQuery('body').trigger('processStop');
        //});


        console.log("Modify canvas is calling with artwokdata ", artworkData)
        artwork.create(artworkData).then(()=>{
            console.log("createArtwork ", artworkData)
            jQuery('body').trigger('processStop');
        }).catch(e =>  {
            jQuery('body').trigger('processStop');
            console.log("ERROR ==> ", e)
        })

        // document.getElementById('pz-loader1').removeClass('hide')

    }


}

function updatePZSelectedOptions(data, resetSelectedOptions) {
    console.log("updatePZSelectedOptions", resetSelectedOptions)
    if (data && data.name) {
        pzSelectedOptions[data.name] = {
            'sku': (data.sku) ? data.sku : '',
            'displayName': (data.displayName) ? data.displayName : 'None',
            'lengthImage': (data.lengthImage) ? (data.lengthImage) : '',
            'cornerImage': (data.cornerImage) ? (data.cornerImage) : '',

        }
        if (data.width) {
            pzSelectedOptions[data.name].width = data.width;
        }

        if (data.color) {
            pzSelectedOptions[data.name].color = data.color;
        }
    }
    // debugger;
    if (resetSelectedOptions.length > 0) {
        var itemRemoved = false;
        for (i = 0; i < resetSelectedOptions.length; i++) {
            if (resetSelectedOptions[i]) {

                var selectedOptionKey = resetSelectedOptions[i]

                if (selectedOptionKey == 'topmat')
                    selectedOptionKey = 'topMat';
                else if (selectedOptionKey == 'bottommat'){
                    selectedOptionKey = 'bottomMat';
                }
                if (selectedOptionKey in pzSelectedOptions) {
                    delete pzSelectedOptions[selectedOptionKey];
                    itemRemoved = true
                }
            }

            if ((i+1) == resetSelectedOptions.length && itemRemoved) {
                //console.log("pzSelectedOptions ", pzSelectedOptions)
                modifyCanvas();
            }

        }
    }
    else {
        modifyCanvas();
    }


}

function resetPZSelectedOptions(data) {
    var loopInc = 0;

    console.log("*******resetPZSelectedOptions ****** ", data)

    for (const property in data) {
        loopInc++;

        if (data[property] && data[property][0] == 0) {
            console.log("**********reset ", data[property]);
            if(property == 'topmat' && 'topMat' in pzSelectedOptions) {
                delete pzSelectedOptions['topMat']
            }
            else if(property == 'bottommat' && 'bottomMat' in pzSelectedOptions) {
                delete pzSelectedOptions['bottomMat']
            }
            else if(property == 'frame' && 'frame' in pzSelectedOptions) {
                delete pzSelectedOptions['frame']
            }
            else if(property == 'liner' && 'liner' in pzSelectedOptions) {
                delete pzSelectedOptions['liner']
            }
            else if(property == 'medtrt' && 'treatment' in pzSelectedOptions) {
                delete pzSelectedOptions['treatment']
            }
        }
    }


    console.log("******************")
    console.log("pzSelectedOptions ", pzSelectedOptions)
    console.log("*****************")

    if (loopInc == Object.keys(data).length) {
        modifyCanvas();
    }
}

//ITs not used i think
async function renderEditImage(customCartProperty) {

    console.log("renderEditImage", customCartProperty)

    var loopInc = 0;

    var artworkData = {};

    var range = document.getElementsByName("rangeVal")[0].value
    var rangeDimention = range.split(/[\s″×]+/); //range.split('×')


    var cartData =  (customCartProperty)
    for (const currProperty in cartData) {
        loopInc++;

        var property = currProperty.toLowerCase();

        console.log(" property ", property)

        const data = cartData[currProperty];
        console.log("data ", data)

        if (property == 'frame' && data && data != "" && data.trim().toLowerCase() != "no frame") {
            var framePath = 'https://devcloud.productimize.com/productimizedemo/perficientJS/images/frames/renderer_';
            artworkData['frame'] = {
                sideImage: framePath + data + '_length1.PNG',
                cornerImage: framePath + data + '_corner1.PNG',
                width:  2,
            }
            console.log("coming inside frame")
        } else if (property == 'liner' && data && data != "" && data.trim().toLowerCase() != "no liner" ) {
            var linerPath = 'https://devcloud.productimize.com/productimizedemo/perficientJS/images/liner/renderer_';

            artworkData["liner"] = {
                sideImage: linerPath + data + '_length1.png',
                cornerImage: linerPath + data + '_corner1.png',
                width: 2
            }
            console.log("coming inside liner")
        } else if ( (property == 'top mat' || property == 'bottom mat') && data && data != "" && (data.trim().toLowerCase() != "no top mat" && data.trim().toLowerCase() != "no bttom mat" ) ) {
            var matPath = 'https://devcloud.productimize.com/productimizedemo/perficientJS/images/mats/';
            var matType = (property == 'top mat') ? 'topMat' : 'bottomMat';
            //var topMatProperty = ('top mat' in cartData) ?  cartData['top mat'] : null;

            var topMatProperty = null;

            if (('topMat' in cartData) && cartData['top mat']  && cartData['topMat'].sku && cartData['top mat'].sku != "" &&  (!cartData['top mat'].sku .trim().toLowerCase().includes('no '))) {
                topMatProperty = cartData['top mat']
            }

            var padding = await getMatWidth(matType, topMatProperty);

            console.log("topMatProperty ", topMatProperty)

            if (pzSelectedOptions && matType in pzSelectedOptions) {
                //pzSelectedOptions[matType].width = JSON.parse(JSON.stringify(padding));

                if (matType == 'topMat') {
                    pzSelectedOptions[matType].width = JSON.parse(JSON.stringify(padding));
                }
                else {
                    if (topMatProperty && Object.keys(topMatProperty).length > 0) {
                        pzSelectedOptions[matType].width = {
                            'left':(parseFloat(pzSelectedOptions["topMat"].width.left) + parseFloat(padding.left)).toFixed(2),
                            'right':(parseFloat(pzSelectedOptions["topMat"].width.right) + parseFloat(padding.right)).toFixed(2),
                            'top':(parseFloat(pzSelectedOptions["topMat"].width.top) + parseFloat(padding.top)).toFixed(2),
                            'bottom':(parseFloat(pzSelectedOptions["topMat"].width.bottom) + parseFloat(padding.bottom)).toFixed(2)
                        }
                    }
                    else {

                        pzSelectedOptions[matType].width = {
                            'left': parseFloat(padding.left).toFixed(2),
                            'right':parseFloat(padding.right).toFixed(2),
                            'top': parseFloat(padding.top).toFixed(2),
                            'bottom':parseFloat(padding.bottom).toFixed(2)
                        }

                    }
                }
            }

            console.log("padding ", padding)

            var matPath = 'https://devcloud.productimize.com/productimizedemo/perficientJS/images/mats/';
            if (padding && Object.keys(padding).length > 0 && padding.left >0 && padding.top>0) {
                artworkData[property] = {
                    sideImage: matPath + data.sku + '_thumbnail.PNG',
                    //cornerImage: matPath + data.sku + '_corner1.PNG',
                    //width: 2

                    width: {left: padding.left, right: padding.right, top: padding.top, bottom: padding.bottom},

                }
            }
        } else if (property == 'treatment' && data.sku && data.sku != "" ) {
            var treatmentPath = 'https://devcloud.productimize.com/productimizedemo/perficientJS/images/treatment/GOLD LEAF DECKLED EDGE.PNG';
            artworkData["treatment"] = {
                url: treatmentPath,
                width: 0.2,
            }
        }
        /*} else if (property == 'treatment' && data.sku && data.sku != "" ) {
            var treatmentPath = 'https://devcloud.productimize.com/productimizedemo/perficientJS/images/treatment/GOLD LEAF DECKLED EDGE.PNG';
            artworkData["treatment"] = {
                url: treatmentPath,
                width: 0.2,
            }
        }*/ else {
        }


    }

    if (loopInc == Object.keys(cartData).length) {

        //var rangeDimention = cartData['Size'];
        artworkData["image"] = {
            url: productImg,
            dimension: {x: rangeDimention[0] ? rangeDimention[0] : 15, y: rangeDimention[1] ? rangeDimention[1] : 15},
        }


        console.log("Renderedit ", artworkData)
        //setTimeout(function(){ //alert("Hello");

        jQuery('body').trigger('processStart');
        //createArtwork(artworkData).then(() => {
        artwork.create(artworkData).then(() => {
            // document.getElementById('pz-loader1').removeClass('hide')
            console.log("RENDER EDIT IMAGE createArtwork ", artworkData)
            jQuery('body').trigger('processStop');
        });


        //}, 3000);


    }

}

const getMatWidth = async( matType, topMatSku = null) => {

    var pzDefaultConf = null;

    var selectedMedia = jQuery('.medium-select-elem').val();
    var selectedTreatment = jQuery('.treatment-select-elem').val();

    var matLeftPadding = 1, matRightPadding = 1, matTopPadding = 1, matBottomPadding = 1;

    if (pzArtworkData) {
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
                matTopPadding = (matTopPadding > 0) ? matTopPadding :0;
                matBottomPadding = (matBottomPadding > 0) ? matBottomPadding : 0;
            }
            else {
                matLeftPadding = pzDefaultConf['bottom_mat_size_left']
                matRightPadding = pzDefaultConf['bottom_mat_size_right']
                matTopPadding = pzDefaultConf['bottom_mat_size_top']
                matBottomPadding = pzDefaultConf['bottom_mat_size_bottom'];

                matLeftPadding = (matLeftPadding > 0) ? matLeftPadding : 0;
                matRightPadding = (matRightPadding > 0) ? matRightPadding : 0;
                matTopPadding = (matTopPadding > 0) ? matTopPadding : 0;
                matBottomPadding = (matBottomPadding > 0) ? matBottomPadding : 0;


                if (topMatSku) {
                    matLeftPadding = parseFloat(matLeftPadding) - parseFloat(pzDefaultConf['top_mat_size_left']);
                    matRightPadding = parseFloat(matRightPadding) - parseFloat(pzDefaultConf['top_mat_size_right']);
                    matTopPadding = parseFloat(matTopPadding) - parseFloat(pzDefaultConf['top_mat_size_top'])
                    matBottomPadding =parseFloat(matBottomPadding) - parseFloat(pzDefaultConf['top_mat_size_bottom'])
                }
            }

        }
    }

    console.log(matLeftPadding, " ", matRightPadding, " ", matTopPadding , " ", matBottomPadding, " ", selectedMedia, " ", selectedTreatment)

    if (pzDefaultConf && 'medium_default_sku' in pzDefaultConf && 'treatment_default_sku' in pzDefaultConf) {
        if (pzDefaultConf.media_default_sku != selectedMedia && pzDefaultConf.treatment_default_sku != selectedTreatment) {
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

                    if (topMatSku) {
                        matLeftPadding = parseFloat(matLeftPadding) - parseFloat(customArtworkData['new_top_mat_size_left']);
                        matRightPadding = parseFloat(matRightPadding) - parseFloat(customArtworkData['new_top_mat_size_right']);
                        matTopPadding = parseFloat(matTopPadding) - parseFloat(customArtworkData['new_top_mat_size_top'])
                        matBottomPadding =parseFloat(matBottomPadding) - parseFloat(customArtworkData['new_top_mat_size_bottom'])
                    }
                }
            }

        }
    }
    return {
        'left': parseFloat(matLeftPadding).toFixed(2),
        'right': parseFloat(matRightPadding).toFixed(2),
        'top': parseFloat(matTopPadding).toFixed(2),
        'bottom': parseFloat(matBottomPadding).toFixed(2)
    };
}
const resetArtwork = () => {
    pzSelectedOptions= {};
    var artworkData = {};
    var range = document.getElementsByName("rangeVal")[0].value
    var rangeDimention = range.split(/[\s″×]+/); //range.split('×')

    artworkData["image"] = {
        url: productImg,
        dimension: {x: rangeDimention[0] ? rangeDimention[0] : 15, y: rangeDimention[1] ? rangeDimention[1] : 15},
    }
    if (pzWatermarkConf && Object.keys(pzWatermarkConf).length > 0) {
        artworkData["watermark"] = {
            url: pzWatermarkConf['url'],
            position : (pzWatermarkConf['position']) ? pzWatermarkConf['position'] : 'center',
            opacity : (pzWatermarkConf['opacity']) ? pzWatermarkConf['opacity'] : 100,
            dimension : {
                x: (pzWatermarkConf['width']) ? pzWatermarkConf['width'] : 100,
                y: (pzWatermarkConf['height']) ? pzWatermarkConf['height'] : 100,
            }
        };
    }

    artwork.create(artworkData).then(() => {
        console.log("createArtwork ", artworkData)
        jQuery('body').trigger('processStop');
    });
}
async function setPZSelectedOptionsForEdit(artworkOption) {

    console.log("renderEditImage", artworkOption)

    var artworkOptions = JSON.parse(artworkOption);

    var loopInc = 0;

    //pzSelectedOptions = JSON.parse(JSON.stringify(artworkOptions));
    console.log("pzSelectedOptions", pzSelectedOptions)

    console.log("artworkOptions", artworkOptions)


    let artworkData = {}

    var loopInc = 0;

    for (const property in pzSelectedOptions) {
        loopInc++;

        const data = pzSelectedOptions[property];

        if (property == 'frame' && data.sku && data.sku != "" && data.sku.trim().toLowerCase() != "no frame") {
            // var framePath = 'https://devcloud.productimize.com/productimizedemo/perficientJS/images/frames/renderer_';

            if (data.cornerImage && data.lengthImage) {
                artworkData['frame'] = {
                    sideImage: data.lengthImage,
                    cornerImage: data.cornerImage,
                    width: parseFloat(data.width)
                }
            }

        } else if (property == 'liner' && data.sku && data.sku != "" && data.sku.trim().toLowerCase() != "no liner" ) {
            //var linerPath = 'https://devcloud.productimize.com/productimizedemo/perficientJS/images/liner/renderer_';
            if (data.cornerImage && data.lengthImage) {
                artworkData['liner'] = {
                    sideImage: data.lengthImage,
                    cornerImage: data.cornerImage,
                    width: parseFloat(data.width)
                }
            }
        } else if ( (property == 'topMat' || property == 'bottomMat') && data.sku && data.sku != ""  &&  (!data.sku .trim().toLowerCase().includes('no ')) && data.lengthImage && data.lengthImage !="") {

            var topMatProperty = null;

            if (('topMat' in pzSelectedOptions) && pzSelectedOptions['topMat']  && pzSelectedOptions['topMat'].sku && pzSelectedOptions['topMat'].sku != "" &&  (!pzSelectedOptions['topMat'].sku .trim().toLowerCase().includes('no '))) {
                topMatProperty = pzSelectedOptions['topMat']
            }


            var padding = await getMatWidth(property, topMatProperty);
            var matPadding = JSON.parse(JSON.stringify(padding));

            console.log("topmatproperty ",topMatProperty )

            if (property == 'topMat') {
                pzSelectedOptions[property].width = matPadding;
            }
            else {
                if (topMatProperty && Object.keys(topMatProperty).length > 0) {
                    pzSelectedOptions[property].width = {
                        'left':(parseFloat(pzSelectedOptions["topMat"].width.left) + parseFloat(padding.left)).toFixed(2),
                        'right':(parseFloat(pzSelectedOptions["topMat"].width.right) + parseFloat(padding.right)).toFixed(2),
                        'top':(parseFloat(pzSelectedOptions["topMat"].width.top) + parseFloat(padding.top)).toFixed(2),
                        'bottom':(parseFloat(pzSelectedOptions["topMat"].width.bottom) + parseFloat(padding.bottom)).toFixed(2)
                    }
                }
                else {
                    pzSelectedOptions[property].width = {
                        'left': parseFloat(padding.left).toFixed(2),
                        'right':parseFloat(padding.right).toFixed(2),
                        'top': parseFloat(padding.top).toFixed(2),
                        'bottom':parseFloat(padding.bottom).toFixed(2)
                    }
                }
            }

            console.log("padding ", padding)

            //var matPath = 'https://devcloud.productimize.com/productimizedemo/perficientJS/images/mats/';
            if (padding && Object.keys(padding).length > 0 && padding.left >0 && padding.top>0) {
                artworkData[property] = {
                    sideImage: data.lengthImage,
                    width: {left: parseFloat(padding.left), right: parseFloat(padding.right), top: padding.top, bottom: padding.bottom},

                }
                console.log("MAT ==> ", artworkData[property] )
            }
        } else if (property == 'treatment' && data.lengthImage && data.lengthImage != "" ) {
            //var treatmentPath = 'https://devcloud.productimize.com/productimizedemo/perficientJS/images/treatment/GOLD LEAF DECKLED EDGE.PNG';
            artworkData["treatment"] = {
                url: data.lengthImage,
                width: (data.width) ? parseFloat(data.width) : 0.1,
            }
        } else if (property == 'size') {
            range = data.sku
            rangeDimention = range.split(/[\s″×]+/); //range.split('×')
        }
    }
    artworkData["image"] = {
        url: productImg,
        dimension: {x: rangeDimention[0] ? rangeDimention[0] : 15, y: rangeDimention[1] ? rangeDimention[1] : 15},
    }
    if (pzWatermarkConf && Object.keys(pzWatermarkConf).length > 0) {
        artworkData["watermark"] = {
            url: pzWatermarkConf['url'],
            position : (pzWatermarkConf['position']) ? pzWatermarkConf['position'] : 'center',
            opacity : (pzWatermarkConf['opacity']) ? pzWatermarkConf['opacity'] : 100,
            dimension : {
                x: (pzWatermarkConf['width']) ? pzWatermarkConf['width'] : 100,
                y: (pzWatermarkConf['height']) ? pzWatermarkConf['height'] : 100,
            }
        };
    }

    if (loopInc == Object.keys(pzSelectedOptions).length) {
        jQuery('body').trigger('processStart');
        artwork.create(artworkData).then(() => {
            console.log("createArtwork ", artworkData)
            jQuery('body').trigger('processStop');
        }).catch(e => {
            jQuery('body').trigger('processStop');
            console.log("ERROR ==> ", e)
        })
    }
}

const getImageBySrc = url => new Promise((resolve, reject) => {
    let img = new Image()
    img.crossOrigin = 'anonymous'
    img.addEventListener('load', () => resolve(img))
    img.addEventListener('error', (e) => reject(`Error occured while dowloading image from url: ${url}`))
    img.src = url
})
const generateImage = (imgSrc, canvasSize) => new Promise((resolve, reject) => {
    var c = document.getElementById("pz-room-canvas");
    //  document.body.appendChild(c);
    var canvas = new fabric.Canvas(c);
    //canvas.setHeight(size[1]);
    //canvas.setWidth(size[0]);
    // canvasSize = canvasSize + (canvasSize * (canvasSize/1000));

    canvas.setWidth(canvasSize);
    canvas.setHeight(canvasSize);

    var immm = new fabric.Image(imgSrc, {
        left: 0,
        top: 0,
        scaleX: (canvasSize/1000), //+0.003,
        scaleY: (canvasSize/1000) // + 0.003
    })
    canvas.add(immm);
    resolve(canvas.toDataURL())
})

// View in room Image generation
const sendViewInRoomCanvasImage = (option, canvasSize)  => new Promise(async (resolve, reject) => {
    var currCanvasImage = artwork.render('png');
    let imgSrc = await getImageBySrc(currCanvasImage)
    let imgs = await generateImage(imgSrc, canvasSize)
    resolve(imgs)
})

// View in room Image generation
const sendViewInRoomCanvasImageOLD = (option, canvasSize)  => new Promise(async (resolve, reject) => {
    var viewInRoomImg = '';
    var viewRoomArtwork = new Artwork({
        name: 'room-canvas',
        width: canvasSize,
        height: canvasSize
    }, document.getElementById('pz-room-canvas'))

    //var range = document.getElementsByName("rangeVal")[0].value
    //var rangeDimention = range.split(/[\s″×]+/);

    var range = ''; //document.getElementsByName("rangeVal")[0].value;
    var rangeDimention = [];
    if (jQuery('.medium-select-elem').val() == "") {
        rangeDimention = [];
    } else {
        range = jQuery('.pz-item-title-out-dimensions-selected-text').text() || document.getElementsByName("rangeVal")[0].value;
        range = range.replace(/[ / w h]/g, '').trim();
        //range.replace(' / ', '');
        rangeDimention = range.split(/[\s″×]+/);
    }


    let artworkData = {}

    var loopInc = 0;

    for (const property in pzSelectedOptions) {
        loopInc++;

        const data = pzSelectedOptions[property];

        if (property == 'frame' && data.sku && data.sku != "" && data.sku.trim().toLowerCase() != "no frame") {
            if (data.cornerImage && data.lengthImage) {
                artworkData['frame'] = {
                    sideImage: data.lengthImage,
                    cornerImage: data.cornerImage,
                    width: parseFloat(data.width)
                }
            }
        } else if (property == 'liner' && data.sku && data.sku != "" && data.sku.trim().toLowerCase() != "no liner" ) {
            if (data.cornerImage && data.lengthImage) {
                artworkData['liner'] = {
                    sideImage: data.lengthImage,
                    cornerImage: data.cornerImage,
                    width: parseFloat(data.width)
                }
            }

        } else if ( (property == 'topMat' || property == 'bottomMat') && data.sku && data.sku != ""  &&  (!data.sku .trim().toLowerCase().includes('no ')) && data.lengthImage && data.lengthImage !="") {
            var topMatProperty = null;
            if (('topMat' in pzSelectedOptions) && pzSelectedOptions['topMat']  && pzSelectedOptions['topMat'].sku && pzSelectedOptions['topMat'].sku != "" &&  (!pzSelectedOptions['topMat'].sku .trim().toLowerCase().includes('no '))) {
                topMatProperty = pzSelectedOptions['topMat']
            }

            var padding = await getMatWidth(property, topMatProperty);
            var matPadding = JSON.parse(JSON.stringify(padding));

            if (property == 'topMat') {
                if (matPadding && Object.keys(matPadding).length > 0 && matPadding.left >0 && matPadding.top>0) {
                    pzSelectedOptions[property].width = matPadding;
                }
            }
            else {
                if (topMatProperty && Object.keys(topMatProperty).length > 0) {
                    pzSelectedOptions[property].width = {
                        'left':(parseFloat(pzSelectedOptions["topMat"].width.left) + parseFloat(padding.left)).toFixed(2),
                        'right':(parseFloat(pzSelectedOptions["topMat"].width.right) + parseFloat(padding.right)).toFixed(2),
                        'top':(parseFloat(pzSelectedOptions["topMat"].width.top) + parseFloat(padding.top)).toFixed(2),
                        'bottom':(parseFloat(pzSelectedOptions["topMat"].width.bottom) + parseFloat(padding.bottom)).toFixed(2)
                    }
                }
                else {
                    pzSelectedOptions[property].width = {
                        'left': parseFloat(padding.left).toFixed(2),
                        'right':parseFloat(padding.right).toFixed(2),
                        'top': parseFloat(padding.top).toFixed(2),
                        'bottom':parseFloat(padding.bottom).toFixed(2)
                    }
                }
            }
            if (padding && Object.keys(padding).length > 0 && padding.left >0 && padding.top>0) {
                artworkData[property] = {
                    sideImage: data.lengthImage,
                    width: {'left': parseFloat(padding.left), 'right': parseFloat(padding.right), 'top': parseFloat(padding.top), 'bottom': parseFloat(padding.bottom)},
                }
            }
        } else if (property == 'treatment' && data.lengthImage && data.lengthImage != "" ) {
            artworkData["treatment"] = {
                url: data.lengthImage,
                width: (parseFloat(data.width) > 0) ? parseFloat(data.width) : 0.1,
            }
        }
    }

    if (loopInc == Object.keys(pzSelectedOptions).length) {
        if (!'treatment' in artworkData) {
            artworkData['treatment'] = null;
        }
        if (!'frame' in artworkData) {
            artworkData['frame'] = null;
        }
        if (!'liner' in artworkData) {
            artworkData['liner'] = null;
        }
        if (!'topMat' in artworkData) {
            artworkData['topMat'] = null;
        }
        if (!'bottomMat' in artworkData) {
            artworkData['bottomMat'] = null;
        }

        artworkData["image"] = {
            url: productImg,
            dimension: {x: rangeDimention[0] ? parseFloat(rangeDimention[0]) : 15, y: rangeDimention[1] ? parseFloat(rangeDimention[1]) : 15},
        }
        if (pzWatermarkConf && Object.keys(pzWatermarkConf).length > 0) {
            artworkData["watermark"] = {
                url: pzWatermarkConf['url'],
                position : (pzWatermarkConf['position']) ? pzWatermarkConf['position'] : 'center',
                opacity : (pzWatermarkConf['opacity']) ? pzWatermarkConf['opacity'] : 100,
                dimension : {
                    x: 20,
                    y: 20,
                }
            };
        }
        artworkData["name"] = 'room-canvas-container';
        viewRoomArtwork.create(artworkData).then(()=> {
            var viewInRoomImg =  viewRoomArtwork.render('png');
            viewRoomArtwork.cleanup();
            resolve (viewInRoomImg);
        }).catch(e =>  {
            reject(e)
        })
    }
});

const downloadClick = () => {
    var canvasImg = artwork.render("jpeg");
    var link = document.getElementById('link');
    /*Start for ticket WENDOVER-514 to download image by sku name*/
    var skuVal = document.getElementById('download-sku').value;
    var sku = skuVal ? skuVal : 'download';
    /*End for ticket WENDOVER-514 to download image by sku name*/
    link.setAttribute('download', sku + '.jpeg');
    link.setAttribute('href', canvasImg.replace("image/jpeg", "image/octet-stream"));
    link.click();
    return false;
}

const downloadTearSheetClick = () => {
    let artworkData = {};
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
    var canvasImg = artwork.render("jpeg");
    var url = document.getElementById('url');
    var sku = jQuery('.product.sku .value').text();
    var framesku = jQuery('.frame-option li.selectedFrame .pz-design-item-name:first').text();

    jQuery.ajax({
         url: url.value,
         type: "POST",
         dataType: "JSON",
         data: {'imgData':canvasImg, 'sku':sku,artworkData: JSON.stringify(artworkData) },
         success: function (response) {
            if(response.response) {
                window.open(jQuery('.box-tocart .actions a#product-tearsheet-button').attr("href")+'?type=1&framesku='+framesku);
            } else {
                window.open(jQuery('.box-tocart .actions a').attr("href"));
            }
         },
         error: function (e) {
         console.log(e);
         }
     });
}

const getArtwork = () => {
    return true;
}

const getArtworkImage = (type) => {
    var currType = type ? type : 'jpeg';
    return artwork.render(currType);
}
