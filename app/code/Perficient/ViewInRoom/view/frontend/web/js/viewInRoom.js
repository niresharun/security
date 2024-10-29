define([
    "jquery",
    "mage/url"
], function($,url){
    "use strict";
    return function vir(config, element) {

        var options = {
            "pixToInchWidth" : 0,
            "pixToInchHeight" : 0,
            "windHeight" : $(window).height(),
            "windWidth" : $(window).width(),
            "winHeightMargin" : $(window).height()/10,
            "winWidthMargin" : $(window).width()/10
        };


        $('#magento_customize_button').on('click', function () {
            initOptions();
            var configData = getConfigData();
            setPixToInch(configData[0]);
        });

        $('#vir_button').on('click', function () {
            initOptions();
            var configData = getConfigData();
            setPixToInch(configData[0]);
            setTimeout(function () {
                $(".vir-img-container .button").trigger("focus");
            },1500);
        });

        $('.vir-popup').on('click', function () {
            closePopup();
        });

        $('.vir-img-container').on('click', function () {
            return false;
        });

        $('.vir-img-container span').on('click', function () {
            closePopup();
        });

        function closePopup(){
            $('body').removeClass('_has-modal');
            $('.vir-popup').hide();
            $('.vir-img-container .button').attr('tabindex','-1');
            $('.vir-button-container #vir_button').trigger("focus");
        }
        function getConfigData(){
            return window.vir_config;
        }

        function setPixToInch(option){

            var tmpImg = new Image();
            tmpImg.src = url.build(option.vir_background_img);
            $('.vir-img-container .button').attr({'tabindex':'0','role':'button'});
            $('.vir-img-container > .vir-bg').attr('src',url.build(option.vir_background_img));
            var pixWidth;
            var pixHeight;
            $(tmpImg).on('load',function(){
                pixWidth = tmpImg.width;
                pixHeight = tmpImg.height;
                var imageMaxWidth = getMaxWidth(pixWidth);
                var imageMaxHeight = getMaxHeight(pixHeight);
                var widthRatio = pixWidth / imageMaxWidth;
                var heightRatio = pixHeight / imageMaxHeight;

                if(widthRatio > heightRatio) {
                    imageMaxHeight = pixHeight / widthRatio;
                    options.pixToInch = imageMaxWidth / option.vir_wall_width;
                    $('.vir-img-container').css("width", imageMaxWidth);
                    $('.vir-img-container').css("height", imageMaxHeight);

                }
                else{
                    imageMaxWidth = pixWidth / heightRatio;
                    options.pixToInch = imageMaxHeight / option.vir_wall_height;
                    $('.vir-img-container').css("height", imageMaxHeight);
                    $('.vir-img-container').css("width", imageMaxWidth);
                }

                if(option.vir_wall_width > 0) {
                    options.pixToInchWidth = imageMaxWidth / option.vir_wall_width;
                }
                if(option.vir_wall_height > 0) {
                    options.pixToInchHeight = imageMaxHeight / option.vir_wall_height;
                }

                setArtPosition(option);

            });


        }

        function getMaxHeight(imgHeight){

            var imgDisplayHeight = imgHeight + (options.winHeightMargin);
            if(imgDisplayHeight > options.windHeight){
                imgHeight = options.windHeight - options.winHeightMargin;
            }
            return imgHeight

        }

        function getMaxWidth(imgWidth){

            var imgDisplayWidth = imgWidth + (options.winWidthMargin);
            if(imgDisplayWidth > options.windWidth){
                imgWidth = options.windWidth - options.winWidthMargin;
            }
            return imgWidth

        }

        function initOptions(){
            options = {
                "pixToInchWidth" : 0,
                "pixToInchHeight" : 0,
                "windHeight" : $(window).height(),
                "windWidth" : $(window).width(),
                "winHeightMargin" : $(window).height()/10,
                "winWidthMargin" : $(window).width()/10
            };
        }

        function setArtPosition(option) {
            if(option.item_width > 0 && option.item_height > 0){
                var artImageWidthInPix = option.item_width * options.pixToInch;
                var artImageHeightInPix = option.item_height * options.pixToInch;
            }
            if(option.vir_center_offset_width > 0 && option.vir_center_offset_height > 0){
                var centerWidthtPix = option.vir_center_offset_width * options.pixToInchWidth;
                var centerHeightPix = option.vir_center_offset_height * options.pixToInchHeight;
            }
            var finalLeft = Math.round(centerWidthtPix - (artImageWidthInPix/2));
            var finalTop = Math.round(centerHeightPix - (artImageHeightInPix/2));

            // Product customizer page - view in room image
            if (typeof getArtwork == 'function' && document.getElementsByName('edit_id')) {
                var canvasSize = 0;
                var range = '';
                var rangeDimention = [];

                if ($('.medium-select-elem').val() == "") {
                    rangeDimention = [];
                }
                else {
                    range = $('.pz-item-title-out-dimensions-selected-text').text() || document.getElementsByName("rangeVal")[0].value;
                    range = range.replace(/[ / w h]/g,'').trim();
                    rangeDimention = range.split(/[\s″×]+/);
                }
                var artImageWidthInPix = (rangeDimention.length > 1 && rangeDimention[0] ? parseFloat(rangeDimention[0]) : option.item_width) * options.pixToInch;
                var artImageHeightInPix = (rangeDimention.length > 1 && rangeDimention[1] ? parseFloat(rangeDimention[1]) : option.item_height) * options.pixToInch;

                var finalLeft = Math.round(centerWidthtPix - (artImageWidthInPix/2));
                var finalTop = Math.round(centerHeightPix - (artImageHeightInPix/2));

                if (artImageWidthInPix > artImageHeightInPix) {
                    canvasSize =  artImageWidthInPix;
                    /*if (rangeDimention.length > 1 && rangeDimention[0]) {
                        canvasSize = canvasSize + (canvasSize * (canvasSize / 1000));
                    }*/
                    finalTop = Math.round(centerHeightPix - (canvasSize / 2));
                }
                else {
                    canvasSize =  artImageHeightInPix;
                    /*if (rangeDimention.length > 1 && rangeDimention[1]) {
                        canvasSize = canvasSize + (canvasSize * (canvasSize / 1000));
                    }*/
                    finalLeft = Math.round(centerWidthtPix - (canvasSize / 2));
                }
                callViewInRoomImage(option, canvasSize);

                $('.vir_art').css('width', canvasSize);
                $('.vir_art').css('height', canvasSize);
                $('.vir_art').css('left', finalLeft);
                $('.vir_art').css('top', finalTop);
                $('body').addClass('_has-modal');
            }
            else { // Normal PDP page view in room image
                var artImage = option.item_image;
                $('.vir_art').attr('src', artImage);
                $('.vir_art').css('width', artImageWidthInPix);
                $('.vir_art').css('height', artImageHeightInPix);
                $('.vir_art').css('left', finalLeft);
                $('.vir_art').css('top', finalTop);
                $('body').addClass('_has-modal');
                $('.vir-popup').show();
            }
        }
        async function callViewInRoomImage (option, canvasSize) {
            var viewInRoomImg =  await sendViewInRoomCanvasImage(option, canvasSize);
            $('.vir_art').attr('src', viewInRoomImg);
            $('.vir-popup').show();
        }
    }
});
