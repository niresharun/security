define([
    "jquery",
    'mage/url',
    'owlCarousel'
], function ($, url,owl) {
    "use strict";
    var defaultConfig = ($('#pz_magento_default_options').val()) ? JSON.parse($('#pz_magento_default_options').val()) : null;
    var mediaDefault = defaultConfig ? defaultConfig.medium_default_sku : null;
    var treatDefault = defaultConfig ? defaultConfig.treatment_default_sku : null;
    var frameDefault = defaultConfig ? defaultConfig.frame_default_sku : null;
    var topMatDefault = defaultConfig ? defaultConfig.top_mat_default_sku : null;
    var bottomMatDefault = defaultConfig ? defaultConfig.bottom_mat_default_sku : null;
    var linerDefault = defaultConfig ? defaultConfig.liner_default_sku : null;
    $.widget('mage.customisedOptions', {
        options: {
            sizeOptionDiv: '.pz-customizer-section .pz-custom-content-wrapper .size-option',
            linerOptionDiv: '.pz-customizer-section .pz-custom-content-wrapper .pz-liner',
            topMatOptionDiv: '.pz-customizer-section .pz-custom-content-wrapper .pz-top-mat',
            bottomMatOptionDiv: '.pz-customizer-section .pz-custom-content-wrapper .pz-bottom-mat',
            mediumOptionDiv: '.pz-customizer-section .pz-custom-content-wrapper .medium-option',
            frameOptionDiv: '.pz-customizer-section .pz-custom-content-wrapper .frame-option',
            mediumTabSelectDiv: '.pz-customizer-section .pz-custom-content-wrapper .medium-option .treatment-select-elem, .pz-customizer-section .pz-custom-content-wrapper .medium-option .medium-select-elem',
            sizeLabelDiv: '.pz-custom-content-wrapper .size-option .pz-item-title-text',
            preSizeLabelDiv: '.pz-custom-content-wrapper .size-option .pz-item-selected-size',
            sizeOuterLabelDiv: '.pz-custom-content-wrapper .size-option .pz-item-title-out-dimensions-text',
            preSizeOuterLabelDiv: '.pz-custom-content-wrapper .size-option .pz-item-title-out-dimensions-selected-text',
            sizeSlider: '.pz-customizer-section .pz-custom-content-wrapper output[name="rangeVal"]',
            apiReturnData: '#pz_platform_custom_returndata',
            mageFrameData: '#pz_magento_framedata',
            customiseUrl: 'productimize/index/option',
            defaultConfig: '#pz_magento_default_options',
            productLevel: '#pz_platform_product_level',
            isAjaxSuccess: false,
            defaultDatas : {
                'mediaDefault': mediaDefault,
                'treatDefault': treatDefault,
                'frameDefault': frameDefault,
                'topMatDefault': topMatDefault,
                'bottomMatDefault': bottomMatDefault,
                'linerDefault': linerDefault
            },
            edgeTreatmentjson:{}
        },
        vars: {
            customizerTabs: ['none', 'medtrt', 'size', 'frame', 'topmat', 'bottommat', 'liner', 'customcolor', 'sidemark'],
            customizerTabsObj: {
                'medtrt': [0, 1],
                'size': [0, 1],
                'frame': [0, 0],
                'topmat': [0, 0],
                'bottommat': [0, 0],
                'liner': [0, 0],
                'customcolor': [0, 0],
                'sidemark': [0, 0]
            },
            customCartProperty: {},
            tabLabels: {
                "frameli": "Frame",
                "topmatli": "Top Mat",
                "bottommatli": "Bottom Mat",
                "linerli": "Liner",
                "size": "Size",
                "media": "Media",
                "treatment": "Treatment"
            },
            cartProperties: {},
            pageEdit: 1,
            tabloops: 0,
            calculatedSize:'',
            accessRestriction:0,
            matsLiner : ['frame', 'topmat', 'bottommat', 'liner'],
            configlevel4 : ['frame', 'liner'],
            editchange : 0
        },
        _create: function () {
            $("#productimize_customize_button").on('click', function () {
                self.loadProductimizeData('');
            });
            if (window.location.href.indexOf("edit") > -1 || window.location.href.indexOf("type") > -1) {
                this.loadProductimizeData('edit');
            }
            $(document).on("click", ".showartworktext", function () {
                var ischecked = $(this).is(':checked');
                let chkstatus = 0;
                if (ischecked == false) {
                    chkstatus = 2;
                    $('#pz-text').val('');
                    $('.pz-text-control-wrapper').hide();
                } else {
                    chkstatus = 1;
                    $('.pz-text-control-wrapper').show();
                }
                self.artworkAjaxCall(chkstatus);
            });

            $(document).on("click", ".showsidemarktext", function () {
                var ischecked = $(this).is(':checked');
                if (ischecked == false) {
                    $('.pz-divtext').hide();
                    $('.pz-textarea').val('');
                } else {
                    $('.pz-divtext').show();
                }
            });

            $(document).on("input", ".pz-qty-field", function () {
                var str = $(".pz-qty-field").val();
                var dec = str.indexOf(".");
                var regex = new RegExp("^[0-9]+$");
                if (!regex.test(str)) {
                    $(".pz-qty-field").val('')
                }
                if(dec != -1)   {
                    $(".pz-qty-field").val('')
                }
            })

            var self = this;
            self.options.productLevel = $('#pz_platform_product_level').val() ? $('#pz_platform_product_level').val() : 1;
            var configlevel4 = ['frame', 'liner'];
            $('body').on('change', '.pz-customizer-section .pz-custom-content-wrapper .medium-option .treatment-select-elem', self.customiseSizeOption.bind(this));
            $(document).on('click', this.options.sizeOptionDiv, function () {
                if (!self.options.isAjaxSuccess) {
                    self.customiseSizeOption();
                }
                if (self.options.isAjaxSuccess) {
                    if ($(self.options.preSizeLabelDiv).html() == "") {
                        if ($(self.options.sizeSlider).text()) {
                            var str = $(self.options.sizeSlider).text();
                            self.sizeTitleAppend(str);
                            var nexttab = $(this).attr('data-nexttab');
                            self.vars.customizerTabsObj.size[0] = 1;
                            self.vars.customizerTabsObj[nexttab][1] = 1;
                        }
                    }
                }
            });
            $('body').on('click', '.pz-customizer-section .pz-custom-content-wrapper .medium-option .nextcontent', function (e) {
                let selectedMediumOption = $(self.options.mediumOptionDiv).find(".medium-select-elem option:selected").val();
                let selectedTreatmentOption = $(self.options.mediumOptionDiv).find(".treatment-select-elem option:selected").val();
                if (selectedMediumOption && selectedTreatmentOption) {
                    if ($(self.options.preSizeLabelDiv).html() == "") {
                        if ($(self.options.sizeSlider).text()) {
                            $(self.options.sizeOptionDiv).trigger('click');
                        }
                    }
                }
            });

            $('body').on("click", ".nextcontent", function (e) {
                e.stopImmediatePropagation();
                var tab = $(this).parents('.pz-custom-items').next().find('.pz-custom-item-header').attr('data-tab');
                self.tabGreenCheck($(this), 1, tab);
                return false;
            });
            $('body').on("click", ".pz-custom-item-header", function (e) {
                var tab = $(this).attr('data-tab');
                self.tabGreenCheck($(this), 2, tab);
            });
            $(document).on("click", ".pz-btn-share", function () {
                // Hide configured Price
                $('.pz-custom-itemspriceconf').hide();
                $('.locatesearch').val('');
                var mediaDefault = self.options.defaultDatas['mediaDefault'];
                if (self.options.productLevel != 1 && mediaDefault) {
                    $('.pz-medium select.medium-select-elem').val(mediaDefault).trigger('change');
                } else {
                    self.vars.customCartProperty = {};
                    self.cartEnableCheck();
                    $('.pz-custom-items').removeClass("open");
                    $('.pz-header-icon').find('.fa-chevron-up').removeClass('fa fa-chevron-up').addClass('fa fa-chevron-down')
                    $('.medium-option').addClass("open");
                    $('.medium-option .pz-header-icon').find('.fa-chevron-down').addClass('fa fa-chevron-up')
                    $('.pz-medium select.medium-select-elem').val('');
                    $('.pz-medium .medium-select-elem').selectric('refresh');
                    $(self.options.sizeLabelDiv).html(" ");
                    $(self.options.preSizeLabelDiv).html("");
                    $(self.options.sizeOuterLabelDiv).html("");
                    $(self.options.preSizeOuterLabelDiv).html("");
                    $(self.options.sizeLabelDiv).html(" Size ");
                    self.options.isAjaxSuccess= false;
                    $('.medium-treat .pz-item-selected-medtrt').html('');
                    var treathtml = '<option value="" class="option">Select Treatment</option>';
                    $('.pz_treatment select.treatment-select-elem').html(treathtml);
                    $('.pz_treatment select.treatment-select-elem').selectric('refresh');
                    $('.pz-custom-item-header .pz-item-header .pz-item-step-number').css('display', 'flex');
                    $('.pz-custom-item-header .pz-tick.pz-tick-success').css('display', 'none');
                    $('.showartworktext').prop('checked', false);
                    $('.pz-text-control-wrapper').css('display', 'none');
                    $('.pz-text-control-wrapper').find('textarea').text('');
                    $('.showsidemarktext').prop('checked', false);
                    $('.pz-divtext').css('display', 'none');
                    $('.pz-divtext').find('textarea').text('')
                    $(".pz-design-item").removeClass("selectedFrame")
                    $('[class^="pz-item-selected"]').text('');
                    resetArtwork()
                }
                return false;
            })
            $('body').on("change", ".pz-medium .medium-select-elem", function () {
                // Hide configured price
                $('.pz-custom-itemspriceconf').hide();
                $('.locatesearch').val('');
                self.vars.pageEdit = 1;
                self.vars.editchange = 0;
                $(self.options.sizeLabelDiv).html(" ");
                $(self.options.preSizeLabelDiv).html("");
                $(self.options.sizeOuterLabelDiv).html("");
                $(self.options.preSizeOuterLabelDiv).html("");
                $(self.options.sizeLabelDiv).html(" Size ");
                var selectedText = $(this).find(':selected').text();
                var selectedMedia = $(this).find(':selected').val();
                if (selectedMedia != '') {
                    self.vars.customCartProperty['media'] = selectedText;
                    delete self.vars.customCartProperty['treatment'];
                    self.headerTitle(1, selectedText)
                } else {
                    $('.medium-treat .pz-item-selected-medtrt').html('');
                    delete self.vars.customCartProperty['media'];
                    delete self.vars.customCartProperty['treatment'];
                }
                var treatArr = [];
                var treathtml = '<option value="" class="option">Select Treatment</option>';
                var returnedData = $(self.options.apiReturnData).val();
                var customizer_api_data = JSON.parse(returnedData);
                if (customizer_api_data && Object.keys(customizer_api_data).length > 0 && selectedMedia in customizer_api_data) {
                    var data = customizer_api_data[selectedMedia];
                    $.each(data['treatment'], function (trkey, trdata) {
                        if (trdata['display_to_customer']) {
                            treatArr.push(trkey);
                            treathtml += '<option data-sku="' + trkey + '" value="' + trkey + '" class="option">' + trdata['display_name'] + '</option>';
                        }
                    });
                }
                $('.pz_treatment select.treatment-select-elem').html(treathtml);
                let treatDefault = self.options.defaultDatas['treatDefault'];
                if (self.options.productLevel != 1) {
                    if ($.inArray(treatDefault, treatArr) !== -1) {
                        $(".treatment-select-elem option[value='" + treatDefault + "']").prop("selected", true)
                        $('.pz_treatment select.treatment-select-elem').selectric('refresh');
                        $('.pz_treatment select.treatment-select-elem').val(treatDefault).trigger('change');
                    }
                    $('.pz-medium select.medium-select-elem, .pz_treatment select.treatment-select-elem').prop('disabled', true);
                    $('.medium-select-elem, .treatment-select-elem').prop('disabled', true);
                    $('.selectric-treatment-select-elem').addClass('selectric-disabled');
                    $('.selectric-treatment-select-elem').css('pointer-events', 'none')
                }
                if (self.vars.pageEdit == 1) {
                    self.resetNextTabs('medtrt');
                }
                self.cartEnableCheck();
                $('.pz_treatment select.treatment-select-elem').selectric('refresh');
            });
            $('body').on("change", ".pz_treatment .treatment-select-elem", function () {
                // Hide configured price
                $('.pz-custom-itemspriceconf').hide();
                $('.locatesearch').val('');
                self.vars.pageEdit = 1;
                self.vars.editchange = 0;
                var selectedText = $(this).find(':selected').text();
                var selectedVal = $(this).find(':selected').val();
                var selectedmedia = $(".medium-select-elem").find(':selected').text();
                if (selectedVal != '') {
                    var finalText = selectedmedia + ' / ' + selectedText;
                    self.vars.customizerTabsObj.medtrt[0] = 1;
                    self.vars.customCartProperty['treatment'] = selectedText;
                } else {
                    var finalText = selectedmedia;
                    delete self.vars.customCartProperty['treatment'];
                }


                let treatVal = $(this).val();
                self.headerTitle(1, finalText)
                self.cartEnableCheck();
                if (self.vars.pageEdit == 1) {
                    self.resetNextTabs('medtrt');
                }
                if(Object.keys(self.options.edgeTreatmentjson).length > 0)  {
                    if (self.options.productLevel != 1) {
                        setTimeout(function()   {
                            self.passEdgeTreatment(treatVal);
                        },2000);
                    }   else {
                        self.passEdgeTreatment(treatVal);
                    }
                }   else    {
                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        var myObj = JSON.parse(this.responseText);
                        self.options.edgeTreatmentjson = myObj
                        if (self.options.productLevel != 1) {
                            setTimeout(function()   {
                                self.passEdgeTreatment(treatVal);
                            },2000);
                        }   else {
                            self.passEdgeTreatment(treatVal);
                        }
                    }
                    };
                    var d = new Date();
                    var milliSeconds = d.getMilliseconds();
                    xmlhttp.open("GET", BASE_URL+"pub/productimize_json/edge_treatment.json?" + milliSeconds, true);
                    xmlhttp.send();
                }
            });

            $(document).on("mouseover", ".frameli li", function (e) {
                let popleft = $(this).parent().position().left;
                let specDetail = $(this).attr('specDetail');
                if(specDetail)  {
                    $('.pz-hover-content1').html($('<img />').attr('src', specDetail).width('113px').height('auto'));
                    //$('.pz-hover-wrapper1,.pz-hover-content1').css('opacity',1);
                    console.log($('.pz-hover-wrapper1').width())
                    let popWidth = $('.pz-hover-wrapper1').width()/4;
                    console.log(popWidth)
                    console.log(popleft);
                    //popleft = parseFloat(popleft) - parseFloat(popWidth);

                    popleft = jQuery(this).parent()[0].getBoundingClientRect().left + jQuery('.frameli').find('.owl-item').position().left - jQuery('.frameli')[0].getBoundingClientRect().left;
                    console.log(popleft);
                    popleft = popleft - $('.pz-hover-wrapper1').width()/2;
                    console.log(popleft);
                    $('.pz-hover-wrapper1,.pz-hover-content1').css('left',popleft);
                    $('.pz-hover-wrapper1').css('top','-74px');
                    $('.pz-hover-wrapper1,.pz-hover-content1').css('display','flex');
                }
            });

            $(document).on("mouseout", ".frameli li", function (e) {
                $('.pz-hover-wrapper1').css('display','none');
            });

            $(document).on("click", ".frameli li,.topmatli li,.bottommatli li,.linerli li", function (e) {
                $('.pz-custom-itemspriceconf').hide();
                $(this).parents('.pz-design-item-list').find('.selectedFrame').removeClass('selectedFrame');
                $(this).addClass('selectedFrame');
                var selectedSku = $(this).attr('data-sku');
                var selectedColor = $(this).attr('data-color-frame');
                let tabidPass = $(this).parents('.pz-design-item-list').attr('class').includes('frame') ? 3: $(this).parents('.pz-design-item-list').attr('class').includes('topmat') ? 4 : $(this).parents('.pz-design-item-list').attr('class').includes('bottommat') ? 5 : 6;
                var selectedMatText = '';
                if (selectedSku != '' && selectedColor != '') {
                    selectedMatText = selectedSku + ' / ' + selectedColor;
                }
                self.headerTitle(tabidPass, selectedMatText)
                if(tabidPass == 6)  {
                    self.getCustomizedPrice();
                }
                if(tabidPass == 3)  {
                    self.checkLinerCondition();
                }
            });

            $('body').on("click", ".frameli li, .linerli li, .topmatli li, .bottommatli li", function (e) {
                const data = {
                    'sku': $(this).attr('data-sku'),
                    'width': $(this).attr('data-width')
                }
                let arrayy = {
                    "frameli": "frame",
                    "topmatli": "topMat",
                    "bottommatli": "bottomMat",
                    "linerli": "liner"
                };
                self.vars.editchange = 0;
                let parentClass = $(this).parents('.pz-design-item-list').attr('dataFrom');
                var nextTab = $(this).parents('.pz-custom-items').children('.pz-custom-item-header').attr('data-nexttab');
                var clickedTab = arrayy[parentClass].toLowerCase();
                self.vars.customCartProperty[self.vars.tabLabels[parentClass].toLowerCase()] = $(this).find('.pz-design-item-name:first').text();
                self.cartEnableCheck();
                // disable click event if product level is 4
                if (self.options.productLevel == 4 && $.inArray(clickedTab, configlevel4) !== -1 && e.originalEvent !== undefined) {
                    return false;
                }
                self.vars.customizerTabsObj[clickedTab][0] = 1;
                self.vars.customizerTabsObj[nextTab][1] = 1;
                if (nextTab == 'customcolor') {
                    self.vars.customizerTabsObj[nextTab][1] = 1;
                    self.vars.customizerTabsObj['sidemark'][1] = 1;
                }
                self.resetNextTabs(arrayy[parentClass], {
                    'name': arrayy[parentClass],
                    'sku': $(this).attr('data-sku'),
                    'displayName': $(this).find('.pz-design-item-name:first').text(),
                    'width': $(this).attr('data-width'),
                    'color': $(this).attr('data-color-frame'),
                    'cornerImage' : $(this).attr('dataCornerImg'),
                    'lengthImage' : $(this).attr('dataLengthImg')
                });
                setPZSelectedOptions({
                    'name': arrayy[parentClass],
                    'sku': $(this).attr('data-sku'),
                    'displayName': $(this).find('.pz-design-item-name:first').text(),
                    'width': $(this).attr('data-width'),
                    'color': $(this).attr('data-color-frame'),
                    'cornerImage' : $(this).attr('dataCornerImg'),
                    'lengthImage' : $(this).attr('dataLengthImg')
                }, true);
                //display outer dimensions for size
                if (arrayy[parentClass].toLowerCase() == 'frame' || arrayy[parentClass].toLowerCase() == 'topMat' || arrayy[parentClass].toLowerCase() == 'bottomMat') {
                    self.defaultTabCheck(arrayy[parentClass].toLowerCase())
                }
                if (arrayy[parentClass].toLowerCase() == 'frame' || arrayy[parentClass].toLowerCase() == 'liner') {
                    var artworkData = self.getArtworkData();
                    if (arrayy[parentClass].toLowerCase() == 'frame') {
                        artworkData.frameWidth = $(this).attr('data-width');
                        artworkData.frameType = $(this).attr('data-type')
                    } else {
                        artworkData.linerWidth = $(this).attr('data-width');
                    }
                    var outerDimensionValue = self.getOuterDimensionCalc(artworkData);
                    self.sizeOuterDimensionTitleAppend(outerDimensionValue);
                }
            });
        },
        artworkAjaxCall: function (chkstatus) {
            this.getCustomizedPrice();
        },
        defaultTabCheck: function(showTab) {
            var mediaDefault = this.options.defaultDatas['mediaDefault'];
            var treatDefault = this.options.defaultDatas['treatDefault'];
            var frameDefault = this.options.defaultDatas['frameDefault'];
            var topMatDefault = this.options.defaultDatas['topMatDefault'];
            var bottomMatDefault = this.options.defaultDatas['bottomMatDefault'];
            let selectedMedia = $(this.options.mediumOptionDiv).find(".medium-select-elem option:selected").val();
            let selectedTreatment = $(this.options.mediumOptionDiv).find(".treatment-select-elem option:selected").val();
            let selectedFrameOption = $.trim($(this.options.frameOptionDiv).find('.pz-design-item.selectedFrame').attr('data-sku'));
            let selectedTopMatSku = $(this.options.topMatOptionDiv).find(".pz-design-item.selectedFrame").attr('data-sku');
            let selectedBottomMatSku = $(this.options.bottomMatOptionDiv).find('.pz-design-item.selectedFrame').attr('data-sku');
            if(selectedMedia && selectedTreatment && selectedFrameOption && (selectedMedia == mediaDefault) && (treatDefault == selectedTreatment) && (frameDefault == selectedFrameOption)) {
                if(showTab == 'frame') {
                    $(this.options.topMatOptionDiv).find('.defaultframe').removeClass('hide');
                }   else if(showTab == 'topMat') {
                    if(topMatDefault == selectedTopMatSku) {
                        $(this.options.bottomMatOptionDiv).find('.defaultframe').removeClass('hide');
                    }   else {
                        $(this.options.bottomMatOptionDiv).find('.defaultframe').addClass('hide');
                        $(this.options.linerOptionDiv).find('.defaultframe').addClass('hide');
                    }
                } else if(showTab == 'bottomMat') {
                    if(bottomMatDefault == selectedBottomMatSku) {
                        $(this.options.linerOptionDiv).find('.defaultframe').removeClass('hide');
                    }   else {
                        $(this.options.linerOptionDiv).find('.defaultframe').addClass('hide');
                    }
                }
            }   else {
                $(this.options.topMatOptionDiv).find('.defaultframe').addClass('hide');
                $(this.options.bottomMatOptionDiv).find('.defaultframe').addClass('hide');
                $(this.options.linerOptionDiv).find('.defaultframe').addClass('hide');
            }
        },
        tabGreenCheck: function (presentThis, clickfrom, tab) {
            if(this.vars.editchange == 0)    {
                if ($.inArray(tab, this.vars.matsLiner) !== -1) {
                    if ($('.' + tab + 'li li').length == 1 && $('.' + tab + 'li li').hasClass('zeroth-value')) {
                        $('.' + tab + 'li li.zeroth-value').trigger('click');
                    }
                }
                if (this.options.productLevel == 4 && $.inArray(tab, this.vars.configlevel4) !== -1) {
                    $('.' + tab + 'li li.defaultOption').trigger('click');
                }
            }
            this.vars.pageEdit = 1;
            let customCartProperty = this.vars.customCartProperty;
            let tabLabels = this.vars.tabLabels;
            presentThis = presentThis;
            let parentItem = presentThis.parents('.pz-custom-items')
            let nextThis = parentItem.next();
            if (clickfrom == 2) {
                nextThis = parentItem;
                if (parentItem.prev().length > 0) {
                    parentItem = parentItem.prev();
                }
            }
            let tabopn = 1;
            var upcoming = '';
            let currentTab = parentItem.find('.pz-custom-item-header').attr('data-tab');
            let openTab = '';
            $.each(this.vars.customizerTabs, function (tab, value) {
                if (value == currentTab) {
                    upcoming = tab;
                }
            });
            $.each(this.vars.customizerTabs, function (tab, value) {
                if ($('[data-tab=' + value + ']').parent().length > 0 && upcoming != '' && tab <= upcoming) {
                    let datacheck = $('[data-tab=' + value + ']').parent().find('.nextcontent').attr('dataCheck').split(',');
                    $.each(datacheck, function (ind, value1) {
                        if (!customCartProperty[tabLabels[value1].toLowerCase()]) {
                            tabopn = 0;
                            openTab = value;
                            $('[data-tab=' + value + ']').parent().find('.pz-item-step-number').css('display', 'flex');
                            $('[data-tab=' + value + ']').parent().find('.pz-tick.pz-tick-success').css('display', 'none');
                            $('[data-tab=' + value + ']').parent().find('.pz-tick.pz-tick-success').css('display', 'none');
                            $('[data-tab=' + value + ']').parent().find('.pz-item-step-number-mobile').css('display', 'flex');
                            $('[data-tab=' + value + ']').parent().find('.pz-tick.pz-tick-success-mobile').css('display', 'none');
                        } else {
                            $('[data-tab=' + value + ']').parent().find('.pz-item-step-number').css('display', 'none');
                            $('[data-tab=' + value + ']').parent().find('.pz-item-step-number-mobile').css('display', 'none');
                            $('[data-tab=' + value + ']').parent().find('.pz-tick.pz-tick-success').css('display', 'flex');
                            $('[data-tab=' + value + ']').parent().find('.pz-tick.pz-tick-success-mobile').css('display', 'flex');
                        }
                    })
                }
                if (openTab != '') {
                    return false;
                }
            });
            let valueerror1 = $('.pz-custom-items.open');
            if (openTab != '') {
                valueerror1 = $('[data-tab=' + openTab + ']').parent();
            }
            let valueerror = valueerror1.find('.pz-item-title-text').text();
            if (valueerror.toLowerCase().includes('medium')) {
                valueerror = 'Media and Treatment'
            }
            if (tabopn == 0) {
                if ($('.customred').length == 0) {
                    valueerror1.find('.nextContentParent').before('<div class="customred">*Please select ' + valueerror + ' to continue</div>')
                    $('.customred').fadeOut(5000, function () {
                        $(this).remove();
                    });
                } else {
                    $('.customred').show().fadeOut(5000, function () {
                        $(this).remove();
                    });
                }
            } else {
                $('.pz-custom-items').removeClass("open");
                $('.pz-header-icon').find('.fa-chevron-up').removeClass('fa fa-chevron-up').addClass('fa fa-chevron-down')
                nextThis.addClass("open");
                nextThis.find('.pz-header-icon .fa-chevron-down').removeClass('fa fa-chevron-down').addClass('fa fa-chevron-up')
                parentItem.find('.pz-item-step-number').css('display', 'none');
                parentItem.find('.pz-tick.pz-tick-success').css('display', 'flex');
            }
        },
        editgreentick: function () {
            let cartProp = $('#pz_cart_properties').val();
            if (cartProp != '') {
                this.vars.cartProperties = JSON.parse(cartProp);
                if (this.vars.cartProperties['medium']) {
                    this.vars.customCartProperty['media'] = this.vars.cartProperties['medium'];
                }
                if (this.vars.cartProperties['treatment']) {
                    this.vars.customCartProperty['treatment'] = this.vars.cartProperties['treatment'];
                    $('.pz-custom-item-header[data-tab="medtrt"] .pz-item-header .pz-item-step-number').css('display', 'none');
                    $('.pz-custom-item-header[data-tab="medtrt"] .pz-tick.pz-tick-success').css('display', 'flex');
                }
                if (this.vars.cartProperties['artwork color'] && this.vars.cartProperties['artwork color'] != '') {
                    $('.showartworktext').prop('checked', true);
                    $('.pz-text-control-wrapper').css('display', 'block');
                    $('.pz-text-control-wrapper').find('textarea').text(this.vars.cartProperties['artwork color'])
                    this.vars.customCartProperty['artwork color'] = this.vars.cartProperties['artwork color'];
                    $('.pz-custom-item-header[data-tab="customcolor"] .pz-item-header .pz-item-step-number').css('display', 'none');
                    $('.pz-custom-item-header[data-tab="customcolor"] .pz-tick.pz-tick-success').css('display', 'flex');
                }
                if (this.vars.cartProperties['sidemark'] && this.vars.cartProperties['sidemark'] != '') {
                    $('.showsidemarktext').prop('checked', true);
                    $('.pz-divtext').css('display', 'block');
                    $('.pz-divtext').find('textarea').text(this.vars.cartProperties['sidemark'])
                    this.vars.customCartProperty['sidemark'] = this.vars.cartProperties['sidemark'];
                }
                let cartProperties = this.vars.cartProperties;
                let customCartProperty = this.vars.customCartProperty;
                $.each(this.vars.tabLabels, function (keytick, valtick) {
                    let valueindex = valtick.toLowerCase();
                    if (!cartProperties[valueindex]) {
                        if(valueindex == 'frame')  {
                            cartProperties[valueindex] = 'No Frame'
                        }
                        if(valueindex == 'top mat')  {
                            cartProperties[valueindex] = 'No Mat'
                        }
                        if(valueindex == 'bottom mat')  {
                            cartProperties['bottom mat'] = 'No Mat'
                        }
                        if(valueindex == 'liner')  {
                            cartProperties[valueindex] = 'No Liner'
                        }
                    }
                    if (cartProperties[valueindex]) {
                        customCartProperty[valueindex] = cartProperties[valueindex];
                        $('.pz-custom-item-header[data-tab="' + valueindex.replace(/\s/g, '').replace(new RegExp('li' + '$'), '') + '"] .pz-item-header .pz-item-step-number').css('display', 'none');
                        $('.pz-custom-item-header[data-tab="' + valueindex.replace(/\s/g, '').replace(new RegExp('li' + '$'), '') + '"] .pz-tick.pz-tick-success').css('display', 'flex');
                    }
                })
            }
            this.cartEnableCheck();
        },
        sizeTitleAppend: function (str) {
            let strSplit = str.split(/[\s″×]+/); //str.split('×');
            let strWidth = parseFloat(strSplit[0]).toFixed(2);
            let strHeight = parseFloat(strSplit[1]).toFixed(2);
            let res = strWidth + '″w × ' + strHeight + "″h";
            $(this.options.sizeLabelDiv).html("");
            $(this.options.sizeLabelDiv).html("Pre-Frame Size ");
            $(this.options.preSizeLabelDiv).html("");
            $(this.options.preSizeLabelDiv).html(res);
            this.sizeOuterDimentionDivEmpty();
            $('.pz-image-container-middle').html("Pre-Frame Size:" + res)
        },
        headerTitle: function (headercount, str) {
            let headerarray = {1:"pz-item-selected-medtrt",2:"pz-item-selected-size",3:"pz-item-selected-frame",4:"pz-item-selected-topmat",5:"pz-item-selected-bottommat",6:"pz-item-selected-liner"}
            $('.'+headerarray[headercount]).html('');
            if(str!='') {
                $('.'+headerarray[headercount]).html(' / ' + str);
            }
        },
        passEdgeTreatment:function (treatVal)   {
            let selectedMedia = $(this.options.mediumOptionDiv).find(".medium-select-elem option:selected").val();
            let selectedTreatment = treatVal;
            let returnedData = $(this.options.apiReturnData).val();
            returnedData = JSON.parse(returnedData);
            let edgeTreatVal = returnedData[selectedMedia]['treatment'][selectedTreatment]['image_edge_treatment'] ? returnedData[selectedMedia]['treatment'][selectedTreatment]['image_edge_treatment'] : 'none';
            let edgeTreatment = this.options.edgeTreatmentjson;
            let edgeSku = {};
            if (edgeTreatment[edgeTreatVal]) {
                edgeSku = edgeTreatment[edgeTreatVal];
            }
            setPZSelectedOptions({
                'name': 'treatment',
                'sku': selectedTreatment,
                'displayName': 'none',
                'width': (edgeSku && edgeSku.m_width) ? edgeSku.m_width : '',
                'lengthImage' : (edgeSku && edgeSku.renderLengthImage) ? edgeSku.renderLengthImage : ''
            }, true);





        },
        sizeOuterDimentionDivEmpty: function () {
            // Total outer dimention to be empty
            $(this.options.sizeOuterLabelDiv).html("");
            $(this.options.preSizeOuterLabelDiv).html("");
        },
        loadCustomizeButtonData: function() {
            var customizationButtonUrl = url.build("productimize/index/customizebuttonrestrict");
            var self = this;
            $.ajax({
                showLoader: false,
                url: customizationButtonUrl,
                data: {
                    product: $('#pz_product_id').val(),
                },
                type: "POST",
                success: function (data) {
                    if(data > 0 && data != 4)   {
                        $('#productimize_customize_button').css('display', 'block');
                    }
                }
            })

        },
        loadProductimizeData: function (pageLayoutType) {
            jQuery('body').trigger('processStart');
            this.detailPageDefaultData('hide');
            //$('#magento_customize_button').removeClass('active');
            $('.product-info-main').addClass('pz-info-customize');
            if ($('#product_addtocart_form').find('#pz_objects').length === 0) {
                $('#product_addtocart_form').append($('#pz_objects'));
            }
            var productimizeCustomizationUrl = url.build("productimize/index/index/");
            var self = this;
            $.ajax({
                showLoader: false,
                url: productimizeCustomizationUrl,
                data: {
                    product: $('#pz_product_id').val(),
                    isAjax: 1,
                    page: $('#pz_page_handle').val(),
                    id: $('#pz_param_id').val(),
                    type: $('#pz_type').val(),
                    qryStr: $('#pz_query_string').val(),
                    pageLayoutType: pageLayoutType
                },
                type: "POST",
                success: function (data) {
                    jQuery('body').trigger('processStop');
                    $('.box-tocart, .non-logged-download, .cust-prod-con').css('display', 'none');
                    $('#productimize_container').html(data.content);
                    $('#productimize_customize_button,.media').css('display', 'none');
                    $('.page-title-wrapper').prependTo(".productimize-grp");
                    $('.product-info-price').appendTo(".productimize-grp");
                    $(".pz-display-button-grp").appendTo(".productimize-grp");
                    $('.pz-social-icons').appendTo('.mp_social_share_inline_under_cart')
                    $('.pz-social-icons').show();
                    $('.a2a_kit,.mailto').hide();
                    // Move view in room popup div to pz-container div
                    if ($('.vir-popup').length > 0) {
                        $('.pz-main-widget > .pz-container').prepend($('.vir-popup'));
                    }
                    self.vars.accessRestriction = data.accessRestriction;
                    if (data.artworkData) {
                        pzArtworkData = data.artworkData;
                        var decodedArtworkData = JSON.parse(pzArtworkData);
                        if (decodedArtworkData) {
                            if (decodedArtworkData.configuration_level) {
                                $('#pz_platform_product_level').val(decodedArtworkData.configuration_level);
                                self.options.productLevel = decodedArtworkData.configuration_level;
                            }
                            if ('default_configuration' in decodedArtworkData) {
                                $('#pz_magento_default_options').val(decodedArtworkData.default_configuration);
                                let defConfiguration = JSON.parse(decodedArtworkData.default_configuration);
                                self.options.defaultDatas['mediaDefault'] = ((defConfiguration) && 'medium_default_sku' in defConfiguration) ? defConfiguration.medium_default_sku.trim() : null;
                                self.options.defaultDatas['treatDefault'] = ((defConfiguration) && 'treatment_default_sku' in defConfiguration) ? defConfiguration.treatment_default_sku.trim() : null;
                                self.options.defaultDatas['sizeDefault'] = ((defConfiguration) && 'size_default_sku' in defConfiguration) ? defConfiguration.size_default_sku.trim() : null;
                                self.options.defaultDatas['frameDefault'] = ((defConfiguration) && 'frame_default_sku' in defConfiguration) ? defConfiguration.frame_default_sku.trim() : null;
                                self.options.defaultDatas['topMatDefault'] = ((defConfiguration) && 'top_mat_default_sku' in defConfiguration) ? defConfiguration.top_mat_default_sku.trim() : null;
                                self.options.defaultDatas['bottomMatDefault'] = ((defConfiguration) && 'bottom_mat_default_sku' in defConfiguration) ? defConfiguration.bottom_mat_default_sku.trim() : null;
                                self.options.defaultDatas['linerDefault'] = ((defConfiguration) && 'liner_default_sku' in defConfiguration) ? defConfiguration.liner_default_sku.trim() : null;
                                var decReturnedData = data.returndata;
                                if(decReturnedData)    {
                                    if(self.options.defaultDatas['mediaDefault'] && decReturnedData[self.options.defaultDatas['mediaDefault']]) {
                                        $('.pz-selected-item-list > .pz-selected-container').eq(1).find('.pz-selected-opt-data').text(decReturnedData[self.options.defaultDatas['mediaDefault']].display_name)
                                    }
                                   if(self.options.defaultDatas['treatDefault'] && decReturnedData[self.options.defaultDatas['mediaDefault']]) {
                                        $('.pz-selected-item-list > .pz-selected-container').eq(2).find('.pz-selected-opt-data').text(decReturnedData[self.options.defaultDatas['mediaDefault']]['treatment'][self.options.defaultDatas['treatDefault']].display_name)
                                    }
                                }
                            }
                            if ('default_configuration_label' in decodedArtworkData) {
                                pzConfigurationLabel = decodedArtworkData.default_configuration_label;
                            }
                        }
                    }
                    if (data.pzCartPropertiesData) {
                        pzCartPropertiesData = data.pzCartPropertiesData;

                    }
                    var events = ["create", "start", "stop", "slide", "change"];
                    $.widget("app.slider", $.ui.slider, {
                        _getCreateEventData: function () {
                            return {value: this.value()};
                        },
                        _init: function () {
                            var steps = this.options.steps;
                            if ($.isArray(steps)) {
                                this.option("max", steps.length - 1);
                            }
                        },
                        _trigger: function (name, e, ui) {
                            var steps = this.options.steps;
                            if (!$.isArray(steps)) {
                                return this._superApply(arguments);
                            }
                            if ($.inArray(name, events) >= 0) {
                                return this._superApply([
                                    name,
                                    e,
                                    $.extend(ui, {
                                        stepValue: steps[ui.value]
                                    })
                                ]);
                            }
                            return this._superApply(arguments);
                        }
                    });
                    $('.productimize_container').next().css('display', 'none');
                    $('.description,.overview').css('display', 'none');
                    $(".product-detail-section").addClass("product-detail-customize-section");
                    $('#productimize_customize_cancel_button').css("display", "block");
                    if(data.accessRestriction == 3)  {
                        $('#pz-button-price').css("visibility", "hidden");
                        $('#pz-button-price').css("display", "block");
                    }   else {
                        $('#pz-button-price').css("display", "block");
                    }
                    
                    $('#sample_images_custom_product,#pz-display-price').css('display', 'none');
                    let prodPrice = 'Original Price '+data.productPrice;
                    $('#custom-price').html(prodPrice);
                    $('#pz_magento_framedata').val(JSON.stringify(data.FrameName));
                    $('#pz_platform_custom_returndata').val(JSON.stringify(data.returndata));
                    var returnedData = data.returndata;
                    var customizer_api_data = data.returndata;
                    var mediahtml = '<option value="" class="option">Select Media</option>';
                    var mediaArr = [];
                    $.each(returnedData, function (key, data) {
                        if (data['display_to_customer']) {
                            mediaArr.push(key);
                            mediahtml += '<option data-sku="' + key + '" value="' + key + '" class="option">' + data['display_name'] + '</option>';
                        }
                    });
                    $('.pz-medium select.medium-select-elem').append(mediahtml);
                    $('.pz-medium .medium-select-elem').selectric();
                    $('.pz_treatment .treatment-select-elem').selectric();
                    if (window.location.href.indexOf("edit") > -1 || window.location.href.indexOf("type") > -1) {
                        $('#pz_cart_properties').val(JSON.stringify(data.editData));
                        $("#customised-price").val(data['configuredPrice']['configureddisplayprice']);
                        $("#configured-price").html(data['configuredPrice']['configureddisplayprice']);
                        $("#selling_price").val(data['configuredPrice']['configuredsellingprice']);
                        let jsoncartProperty = data.editData;
                        $(".medium-select-elem option[value='" + jsoncartProperty['medium'] + "']").prop("selected", true)
                        $('.pz-medium .medium-select-elem').selectric('refresh');
                        $('.pz-medium select.medium-select-elem').val(jsoncartProperty['medium']);
                        $(".treatment-select-elem option[value='" + jsoncartProperty['treatment'] + "']").prop("selected", true)
                        var selectedMedia = jsoncartProperty['medium'];
                        var treatArr = [];
                        var treathtml = '<option value="" class="option">Select Treatment</option>';
                        var returnedData = $(self.options.apiReturnData).val();
                        var customizer_api_data = JSON.parse(returnedData);
                        if (customizer_api_data && Object.keys(customizer_api_data).length > 0 && selectedMedia in customizer_api_data) {
                            var data1 = customizer_api_data[selectedMedia];
                            $.each(data1['treatment'], function (trkey, trdata) {
                                if (trdata['display_to_customer']) {
                                    treatArr.push(trkey);
                                    treathtml += '<option data-sku="' + trkey + '" value="' + trkey + '" class="option">' + trdata['display_name'] + '</option>';
                                }
                            });
                        }
                        $('.pz_treatment select.treatment-select-elem').html(treathtml);
                        $(".treatment-select-elem option[value='" + jsoncartProperty['treatment'] + "']").prop("selected", true)
                        $('.pz_treatment select.treatment-select-elem').selectric('refresh');
                        $('.pz_treatment select.treatment-select-elem').val(jsoncartProperty['treatment']);
                        self.editgreentick();
                        var mediumText = $('.medium-select-elem').find(':selected').text() + ' / ' + $('.treatment-select-elem').find(':selected').text();
                        self.headerTitle(1, mediumText);
                        let sizeArray = data.sizeData;
                        self.sizeTrigger(sizeArray,jsoncartProperty['size']);
                        self.sizeTitleAppend(jsoncartProperty['size']);
                        if(data.FrameName[jsoncartProperty['frame']] && !jsoncartProperty['frame'].toLowerCase().includes('no '))   {
                            let frametit = jsoncartProperty['frame'];
                            frametit += ' / '+data.FrameName[jsoncartProperty['frame']].m_color_frame;
                            self.headerTitle(3, frametit);
                        }
                        if(jsoncartProperty['top mat'] && jsoncartProperty['top mat']!='null' && data.topMatData[jsoncartProperty['top mat']] && !jsoncartProperty['top mat'].toLowerCase().includes('no '))   {
                            let topmattit = jsoncartProperty['top mat'];
                            topmattit += ' / '+data.topMatData[jsoncartProperty['top mat']].m_color_mat;
                            self.headerTitle(4, topmattit);
                        }
                        if(jsoncartProperty['bottom mat'] && jsoncartProperty['bottom mat']!='null' && data.bottomMatData[jsoncartProperty['bottom mat']] && !jsoncartProperty['bottom mat'].toLowerCase().includes('no '))   {
                            let botmattit = jsoncartProperty['bottom mat'];
                            botmattit += ' / '+data.bottomMatData[jsoncartProperty['bottom mat']].m_color_mat;
                            self.headerTitle(5, botmattit);
                        }
                        if(jsoncartProperty['liner'] && !jsoncartProperty['liner'].toLowerCase().includes('no '))   {
                            let linertit = jsoncartProperty['liner'];
                            linertit += ' / '+data.linerData[jsoncartProperty['liner']].m_color_liner;
                            self.headerTitle(6, linertit);
                        }
                        self.vars.pageEdit = 0;
                        self.vars.editchange = 1;
                        self.callFrameRightContent(jsoncartProperty['size'])
                        self.callMatRightContent(data.topMatData, 'topmat');
                        self.callMatRightContent(data.bottomMatData, 'bottommat');
                        self.callLinerRightContent(data.linerData)
                        if (self.options.productLevel != 1) {
                            $('.selectric-medium-select-elem').addClass('selectric-disabled');
                            $('.selectric-medium-select-elem').css('pointer-events', 'none');
                            $('.pz_treatment select.treatment-select-elem').prop('disabled', true);
                            $('.selectric-treatment-select-elem').addClass('selectric-disabled');
                            $('.selectric-treatment-select-elem').css('pointer-events', 'none')
                        }
                    }   else {
                        if (self.options.productLevel != 1) {
                            if ($.inArray(self.options.defaultDatas['mediaDefault'], mediaArr) !== -1) {
                                $(".medium-select-elem option[value='" + self.options.defaultDatas['mediaDefault'] + "']").prop("selected", true)
                                $('.pz-medium .medium-select-elem').selectric('refresh');
                                $('.pz-medium select.medium-select-elem').val(self.options.defaultDatas['mediaDefault']).trigger('change');
                                $('.pz-medium select.medium-select-elem').prop('disabled', true);
                                $('.pz-medium .medium-select-elem').selectric('refresh');
                                $('.selectric-medium-select-elem').addClass('selectric-disabled');
                                $('.selectric-medium-select-elem').css('pointer-events', 'none');
                            }
                        }
                        $('.productimise-container').trigger('contentUpdated');
                        if ($('#product_addtocart_form').find('#pz_cart_properties').length === 0) {
                            $('#product_addtocart_form').append($('#pz_cart_properties'));
                        }
                    }
                    $('#product-updatecart-button').css('display', 'none');
                    setTimeout(() => {
                        self.cartEnableCheck();
                        if(data.accessRestriction == 2) { // customer's customer
                            $('.pz-add-to-cart-container,.pz-quantity-container').css('display','none');
                        } else if(data.accessRestriction == 3)  { // guest
                            $('.pz-add-to-cart-container,.pz-quantity-container').hide();
                            $('.pz-display-price').css('visibility','hidden');
                            $('.pz-custom-itemspriceconf,.product-original-price-container').css('display','none');
                        }
                        if(pageLayoutType == '')    {
                            $('.pz-custom-itemspriceconf').css('display','none');
                        }                        
                        $('#product-updatecart-button').css('display', 'none');
                    }, 1000);
                },
                error: function () {
                    jQuery('body').trigger('processStop');
                    alert('Error occurred');
                }
            });
        },
        cartEnableCheck: function() {
            var self = this;
            let tabLabels = this.vars.tabLabels;
            let enableCart = 1;
            $.each(tabLabels, function(tabLabelskey, tabLabelsvalue) {
                if(!self.vars.customCartProperty[tabLabelsvalue.toLowerCase()])    {
                    enableCart = 0;
                }
            })
            if(enableCart == 0) {
                $('.productimizecartbutton').prop("disabled", true);
                $('.cartbutton,.product-social-links').css({"pointer-events": "none","opacity": 0.5});
            }   else {
                $('.productimizecartbutton').prop("disabled", false);
                if(self.vars.accessRestriction != 3)  {
                    // $('.cartbutton,.product-social-links').css("pointer-events", "all");
                    // $('.cartbutton,.product-social-links').css("opacity", 1);
                    $('.cartbutton,.product-social-links').css({"pointer-events": "all","opacity": 1});
                }
                if(self.vars.accessRestriction == 3)  {
                    $('.product-social-links').css({"pointer-events": "all","opacity": 1});
                }
            }
        },
        detailPageDefaultData: function (action) {
            if (action == 'hide') {
                $('.sidemark-container,.art-configurator-desc,.Default-configuration-swatches,.Default-configuration-text,.update,.specialty').hide();
            } else {
                $('.sidemark-container,.art-configurator-desc,.Default-configuration-swatches,.Default-configuration-text,.specialty').show();
            }
        },
        sizeOuterDimensionTitleAppend: function (glassDimension) {
            var width, height, res;
            width = glassDimension[0];
            height = glassDimension[1];
            res = ' / ' + width + '″w × ' + height + "″h";
            $(this.options.sizeOuterLabelDiv).html("");
            $(this.options.sizeOuterLabelDiv).html("Total Outer Dimensions ");
            $(this.options.preSizeOuterLabelDiv).html("");
            $(this.options.preSizeOuterLabelDiv).html(res);
            $('.pz-image-container-middle').html("<b>Total Outer Dimensions:</b>" + (width + '″w × ' + height + "″h"))
        },
        hasChangedMediaTreatment: function () {
            var defaultMedium, defaultTreatment, selectedMediumOption, selectedTreatmentOption, hasChanged = 0;
            var defaultConfig = ($('#pz_magento_default_options').val()) ? JSON.parse($('#pz_magento_default_options').val()) : null;
            $.each(defaultConfig, function (key, data) {
                if (key == 'medium_default_sku') {
                    defaultMedium = data;
                    return false;
                }
            });
            $.each(defaultConfig, function (key, data) {
                if (key == 'treatment_default_sku') {
                    defaultTreatment = data;
                    return false;
                }
            });
            selectedMediumOption = $.trim($(this.options.mediumOptionDiv).find(".medium-select-elem option:selected").val());
            selectedTreatmentOption = $.trim($(this.options.mediumOptionDiv).find(".treatment-select-elem option:selected").val());
            if (selectedMediumOption && selectedTreatmentOption && defaultMedium != selectedMediumOption || defaultTreatment != selectedTreatmentOption) {
                hasChanged = 1;
            }
            return hasChanged;
        },
        hasChangedSizeFrame: function () {
            var defaultSize, dafaultFrame, selectedSizeOption, selectedFrameOption = '', hasChangedSF = 0;
            var decodedArtworkData = JSON.parse(pzArtworkData);
            defaultSize = "6×8";
            if (decodedArtworkData && 'image_width' in decodedArtworkData && 'image_height' in decodedArtworkData) {
                defaultSize = decodedArtworkData['image_width'] + '×' + decodedArtworkData['image_height'];
                if (decodedArtworkData.default_configuration) {
                    dafaultFrame = this.options.defaultDatas['frameDefault']
                }
            }
            selectedSizeOption = $.trim($(this.options.sizeSlider).text());
            selectedFrameOption = $.trim($(this.options.frameOptionDiv).find('.pz-design-item.selectedFrame').attr('data-sku'));
            if (selectedSizeOption && selectedFrameOption && defaultSize != selectedSizeOption || dafaultFrame != selectedFrameOption) {
                hasChangedSF = 1;
            }
            return hasChangedSF;
        },
        customiseSizeOption: function () {
            // Hide configured Price
            $('.pz-custom-itemspriceconf').hide();
            this.sizeOuterDimentionDivEmpty();
            var self = this, selectedMediumOption, selectedTreatmentOption;
            selectedMediumOption = $(self.options.mediumOptionDiv).find(".medium-select-elem option:selected").val();
            selectedTreatmentOption = $(self.options.mediumOptionDiv).find(".treatment-select-elem option:selected").val();
            let sizeArray = '';
            if (selectedMediumOption && selectedTreatmentOption) {
                $.ajax({
                    url: BASE_URL + self.options.customiseUrl,
                    type: "POST",
                    datatype: "json",
                    showLoader: true,
                    data: {
                        product: $('#pz_product_id').val(),
                        type: 'size',
                        selectedMedium: selectedMediumOption,
                        selectedTreatment: selectedTreatmentOption,
                        productId: document.getElementById('web_product_id') ? document.getElementById('web_product_id').value : null
                    },
                    success: function (response) {
                        sizeArray = response['content'];
                        self.sizeTrigger(sizeArray,null)
                    },
                    complete: function (xhr, status) {
                        if ($(".ui-slider-range").length > 0) {
                            $('.rangeleft').html('<span>' + sizeArray[0] + '</span>(Min)');
                            $('.rangeright').html('<span>' + sizeArray[sizeArray.length - 1] + '</span>(Max)');
                            var control = $('#slider');
                            var output = control.next('output');
                            if (self.vars.pageEdit == 0 && self.vars.cartProperties['size']) {
                                let sizeVal = self.vars.cartProperties['size']
                                let leftPosi = (100 / sizeArray.length) * sizeArray.indexOf(sizeVal);
                                self.vars.customCartProperty['size'] = sizeVal;
                                output.css('left', leftPosi + '%').css('position', 'absolute').text(sizeVal);
                                $('.ui-slider-handle').css('left', leftPosi + '%')
                                var str = $('output[name="rangeVal"]').text();
                            } else {
                                let size = (self.vars.calculatedSize) ? self.vars.calculatedSize : sizeArray[0];
                                let leftPosi = 0;
                                if(size) {
                                    leftPosi = (100 / sizeArray.length) * sizeArray.indexOf(size);
                                }
                                self.vars.customCartProperty['size'] = size;
                                output.css('left', leftPosi+'%').css('position', 'absolute').text(size);
                                $('.ui-slider-handle').css('left', leftPosi+'%')
                            }
                            self.cartEnableCheck();
                            if ($(self.options.sizeSlider).text()) {
                                var str = $(self.options.sizeSlider).text();
                                self.sizeTitleAppend(str);
                                var nexttab = $('.size-option').attr('data-nexttab');
                                self.vars.customizerTabsObj.size[0] = 1;
                                self.vars.customizerTabsObj[nexttab][1] = 1;
                                self.checkFrameCondition();
                                self.checkTopMatCondition();
                                self.checkBottomMatCondition();
                            }
                        }
                    },
                    error: function (error) {
                        self.options.isAjaxSuccess = false;
                    }
                });
            }
        },
        sizeTrigger: function(sizeArray,defaultsize) {
            // Hide configured Price
            //$('.pz-custom-itemspriceconf').hide();

            let defSize = (defaultsize) ? defaultsize : sizeArray[0];
            if (this.options.productLevel >= 3 && defaultsize == null) {
                defaultsize = this.options.defaultDatas['sizeDefault'];
            }
            let leftPosi = 0;
            var self = this;
            if(defaultsize) {
                let sizeDefault = defaultsize;
                let selectedSize = (sizeDefault.indexOf('x') != -1) ? sizeDefault.split('x') : sizeDefault.split('\u00d7');
                if(sizeArray.indexOf(sizeDefault) != -1)  {
                    defSize = sizeDefault;
                }   else {
                    let subArr = [];
                    var el = sizeArray.find(a =>{
                        let leftwidth = a.split(/[\s″×]+/); //a.split('×');
                        if(leftwidth[0] <= selectedSize[0]) {
                            if(leftwidth[1] <= selectedSize[1]) {
                                subArr.push(a)
                            }
                        }
                    });
                    if(subArr.length > 0)   {
                        defSize = subArr[subArr.length - 1]
                    }
                }
            }
            if(defSize) {
                leftPosi = (100 / sizeArray.length) * sizeArray.indexOf(defSize);
                leftPosi = self.getSliderPostionLeft(leftPosi, sizeArray);
            }
            self.vars.calculatedSize = defSize;
            $("#slider").slider({
                range: "min",
                steps: sizeArray,
                change: function (e, ui) {
                    self.vars.editchange = 0;
                    var control = $('#slider');
                    var output = control.next('output');
                    /*
                    var leftPercent = $('.ui-slider-handle')[0].style.left;
                    self.vars.customCartProperty['size'] = ui.stepValue;
                    self.cartEnableCheck();
                    leftPercent = leftPercent.replace('%', '');
                    leftPercent = parseInt(leftPercent);
                    var dis = leftPercent * 0.06;
                    leftPercent = leftPercent - dis;
                    output.css('left', leftPercent + '%').css('position', 'absolute').text(ui.stepValue);
                    */
                    var leftPercent = (100 / sizeArray.length) * sizeArray.indexOf(ui.stepValue);
                    leftPercent = self.getSliderPostionLeft(leftPercent, sizeArray);
                    output.css('left', leftPercent + '%').css('position', 'absolute').text(ui.stepValue);



                    $('.rangeleft').html('<span>' + sizeArray[0] + '</span>(Min)');
                    $('.rangeright').html('<span>' + sizeArray[sizeArray.length - 1] + '</span>(Max)');
                    // Hide configured Price
                    $('.pz-custom-itemspriceconf').hide();
                    self.sizeTitleAppend(ui.stepValue);
                    self.checkFrameCondition();
                    self.checkTopMatCondition();
                    self.checkBottomMatCondition();
                    self.resetNextTabs('size');
                },
                create: function (e, ui) {
                    var control = $('#slider');
                    var output = control.next('output');
                    leftPosi = (100 / sizeArray.length) * sizeArray.indexOf(defSize);
                    leftPosi = self.getSliderPostionLeft(leftPosi, sizeArray);
                    output.css('left', leftPosi+'%').css('position', 'absolute').text(defSize);
                    $('.rangeleft').html('<span>' + sizeArray[0] + '</span>(Min)');
                    $('.rangeright').html('<span>' + sizeArray[sizeArray.length - 1] + '</span>(Max)');
                },
                slide: function (e, ui) {
                    let control = $('#slider');
                    let output = control.next('output');

                    let currLeftPercent = (100 / sizeArray.length) * sizeArray.indexOf(ui.stepValue);
                    currLeftPercent = self.getSliderPostionLeft(currLeftPercent, sizeArray);
                    output.css('left', currLeftPercent + '%').css('position', 'absolute').text(ui.stepValue);
                    leftPosi = currLeftPercent;
                    //$('.ui-slider-handle').css('left', currLeftPercent + '%')
                }
            });
            $('.ui-slider-handle').css('left', leftPosi + '%')
            $('#slider').append('<div class="leftBubble"></div><div class="rightBubble"><div>')
            self.options.isAjaxSuccess = true;
            if (self.options.productLevel >= 3) {
                $("#slider").val(defSize);
                $('#slider').slider( 'disable');
            }
        },
        getSliderPostionLeft: function (currLeftPercent, sizeArray) {
            var multiplier =  0.06;
            var multiplierQutient = parseInt(currLeftPercent/20);
            if (multiplierQutient <= 1)
                multiplier =  0.06;
            else {
                multiplier =  0.04;
            }
            let dis = currLeftPercent * multiplier;
            currLeftPercent -= dis;
            return currLeftPercent;
        },
        checkFrameCondition: function () {
            var configLevel, selectedMediumOption, selectedTreatmentOption, selectedSizeOptions, defaultConfig,
                isDefaultFrame = 0, returnedData, minRabbetDepth;
            configLevel = this.options.productLevel;
            selectedMediumOption = $(this.options.mediumOptionDiv).find(".medium-select-elem option:selected").val();
            selectedTreatmentOption = $(this.options.mediumOptionDiv).find(".treatment-select-elem option:selected").val();
            selectedSizeOptions = $.trim($(this.options.sizeSlider).text());
            var currSelectedSizeOption = selectedSizeOptions.split(/[\s″×]+/);
            var selectedSizeOption = currSelectedSizeOption[0] + '×' + currSelectedSizeOption[1];
            defaultConfig = ($('#pz_magento_default_options').val()) ? JSON.parse($('#pz_magento_default_options').val()) : null;
            $.each(defaultConfig, function (key, data) {
                if (key == 'frame_default_sku' && data) {
                    isDefaultFrame = 1;
                    return false;
                }
            });
            returnedData = JSON.parse($(this.options.apiReturnData).val());
            minRabbetDepth = parseFloat(returnedData[selectedMediumOption]['treatment'][selectedTreatmentOption]['min_rabbet_depth']);
            var payload = {
                'config_level': configLevel,
                'selected_medium': selectedMediumOption,
                'selected_treatment': selectedTreatmentOption,
                'selected_size': selectedSizeOption,
                'has_changed_medium_treatment': this.hasChangedMediaTreatment(),
                'is_default_frame': isDefaultFrame,
                'min_rabbet_depth': minRabbetDepth
            };
            this.getAjaxDetails(payload, 'frame');
        },
        checkTopMatCondition: function () {
            var configLevel, isDefaultTopMat = 0,
                requireTopMatForTreatment, selectedMediumOption,
                selectedTreatmentOption, returnedData, defaultConfig, isDefaultMat, isDefaultBottomMat = 0,
                glassDimention, artworkData, width, height, isDefaultTopMatSku, isDefaultBottomMatSku;
            configLevel = this.options.productLevel;
            returnedData = JSON.parse($(this.options.apiReturnData).val());
            selectedMediumOption = $(this.options.mediumOptionDiv).find(".medium-select-elem option:selected").val();
            selectedTreatmentOption = $(this.options.mediumOptionDiv).find(".treatment-select-elem option:selected").val();
            requireTopMatForTreatment = parseFloat(returnedData[selectedMediumOption]['treatment'][selectedTreatmentOption]['requires_top_mat']);
            defaultConfig = ($('#pz_magento_default_options').val()) ? JSON.parse($('#pz_magento_default_options').val()) : null;
            artworkData = this.getArtworkData();
            glassDimention = getGlassDimention(artworkData);
            width = glassDimention[0];
            height = glassDimention[1];
            $.each(defaultConfig, function (key, data) {
                if (key == 'top_mat_default_sku' && data) {
                    isDefaultTopMat = 1;
                    isDefaultTopMatSku = data;
                    return false;
                }
            });
            $.each(defaultConfig, function (key, data) {
                if (key == 'bottom_mat_default_sku' && data) {
                    isDefaultBottomMat = 1;
                    isDefaultBottomMatSku = data;
                    return false;
                }
            });
            let selectedSizeOption = $.trim($(this.options.sizeSlider).text());
            var payload = {
                'config_level': configLevel,
                'selected_medium': selectedMediumOption,
                'selected_treatment': selectedTreatmentOption,
                'selected_size': selectedSizeOption,
                'require_topmat_for_treatment': requireTopMatForTreatment,
                'has_changed_medium_treatment': this.hasChangedMediaTreatment(),
                'has_changed_size_frame': this.hasChangedSizeFrame(),
                'is_default_topmat': isDefaultTopMat,
                'is_default_bottommat': isDefaultBottomMat,
                'is_default_bottommat_sku': isDefaultBottomMatSku,
                'is_default_topmat_sku': isDefaultTopMatSku,
                'width': width,
                'height': height
            };
            this.getAjaxDetails(payload, 'topmat');
        },
        checkBottomMatCondition: function () {
            var configLevel, isDefaultTopMat = 0,
                requireBottomMatForTreatment, selectedMediumOption,
                selectedTreatmentOption, returnedData, defaultConfig, isDefaultMat, isDefaultBottomMat = 0,
                glassDimention, artworkData, width, height, isDefaultTopMatSku, isDefaultBottomMatSku;
            configLevel = this.options.productLevel;
            returnedData = JSON.parse($(this.options.apiReturnData).val());
            selectedMediumOption = $(this.options.mediumOptionDiv).find(".medium-select-elem option:selected").val();
            selectedTreatmentOption = $(this.options.mediumOptionDiv).find(".treatment-select-elem option:selected").val();
            requireBottomMatForTreatment = parseFloat(returnedData[selectedMediumOption]['treatment'][selectedTreatmentOption]['requires_bottom_mat']);
            defaultConfig = ($('#pz_magento_default_options').val()) ? JSON.parse($('#pz_magento_default_options').val()) : null;
            artworkData = this.getArtworkData();
            let selectedSizeOption = $.trim($(this.options.sizeSlider).text());
            glassDimention = getGlassDimention(artworkData);
            width = glassDimention[0];
            height = glassDimention[1];
            $.each(defaultConfig, function (key, data) {
                if (key == 'top_mat_default_sku' && data) {
                    isDefaultTopMat = 1;
                    isDefaultTopMatSku = data;
                    return false;
                }
            });
            $.each(defaultConfig, function (key, data) {
                if (key == 'bottom_mat_default_sku' && data) {
                    isDefaultBottomMat = 1;
                    isDefaultBottomMatSku = data;
                    return false;
                }
            });
            var payload = {
                'config_level': configLevel,
                'selected_medium': selectedMediumOption,
                'selected_treatment': selectedTreatmentOption,
                'selected_size': selectedSizeOption,
                'require_bottommat_for_treatment': requireBottomMatForTreatment,
                'has_changed_medium_treatment': this.hasChangedMediaTreatment(),
                'has_changed_size_frame': this.hasChangedSizeFrame(),
                'is_default_topmat': isDefaultTopMat,
                'is_default_bottommat': isDefaultBottomMat,
                'is_default_topmat_sku': isDefaultTopMatSku,
                'is_default_bottommat_sku': isDefaultBottomMatSku,
                'width': width,
                'height': height
            };
            this.getAjaxDetails(payload, 'bottommat');
        },
        checkLinerCondition: function () {
            var configLevel, isDefaultLiner = 0,
                requireLinerForTreatment, frameType, selectedMediumOption,
                selectedTreatmentOption, selectedFrameSku, returnedFrameData, returnedData, defaultConfig,
                frameRabbetDepth, minRabbetDepth, linerRabbetDepthCheck, defaultLinerSku = '';
            configLevel = this.options.productLevel;
            returnedFrameData = JSON.parse($(this.options.mageFrameData).val());
            selectedFrameSku = $(this.options.frameOptionDiv).find('.pz-design-item.selectedFrame').attr('data-sku');
            if (selectedFrameSku && returnedFrameData[selectedFrameSku]) {
                frameType = returnedFrameData[selectedFrameSku]['m_frame_type'];
                frameRabbetDepth = returnedFrameData[selectedFrameSku]['m_frame_rabbet_depth'];
                frameType = frameType.toLowerCase();
            }
            returnedData = JSON.parse($(this.options.apiReturnData).val());
            selectedMediumOption = $(this.options.mediumOptionDiv).find(".medium-select-elem option:selected").val();
            selectedTreatmentOption = $(this.options.mediumOptionDiv).find(".treatment-select-elem option:selected").val();
            let selectedSizeOptions = $.trim($(this.options.sizeSlider).text());
            let currSelectedSizeOption = selectedSizeOptions.split(/[\s″×]+/);
            let selectedSizeOption = currSelectedSizeOption[0] + '×' + currSelectedSizeOption[1];

            requireLinerForTreatment = parseFloat(returnedData[selectedMediumOption]['treatment'][selectedTreatmentOption]['requires_liner']);
            minRabbetDepth = parseFloat(returnedData[selectedMediumOption]['treatment'][selectedTreatmentOption]['min_rabbet_depth']);
            linerRabbetDepthCheck = parseFloat(returnedData[selectedMediumOption]['treatment'][selectedTreatmentOption]['liner_depth_check']);
            defaultConfig = ($('#pz_magento_default_options').val()) ? JSON.parse($('#pz_magento_default_options').val()) : null;
            $.each(defaultConfig, function (key, data) {
                if (key == 'liner_default_sku' && data) {
                    isDefaultLiner = 1;
                    defaultLinerSku = data;
                    return false;
                }
            });
            if (frameType && frameRabbetDepth) {
                var payload = {
                    'config_level': configLevel,
                    'selected_medium': selectedMediumOption,
                    'selected_treatment': selectedTreatmentOption,
                    'selected_size': selectedSizeOption,
                    'frame_type': frameType,
                    'selected_frame_sku': selectedFrameSku,
                    'require_liner_for_treatment': requireLinerForTreatment,
                    'has_changed_medium_treatment': this.hasChangedMediaTreatment(),
                    'has_changed_size_frame': this.hasChangedSizeFrame(),
                    'is_default_liner': isDefaultLiner,
                    'frame_rabbet_depth': frameRabbetDepth,
                    'min_rabbet_depth': minRabbetDepth,
                    'liner_depth_check': linerRabbetDepthCheck,
                    'default_liner_sku': defaultLinerSku
                };
                this.getAjaxDetails(payload, 'liner');
            } else {
                var mediaframehtml = '<li class="pz-design-item no-liner zeroth-value selectedFrame" data-color="" data-sku="" data-width="" data-color-frame="" data-type="">' +
                    '<div class="pz-design-item-content">' +
                    '<div class="pz-design-item-img" style="background: url(&quot;https://devcloud.productimize.com/v3/promizenode/./assets/images/61/OptionImages/StandardImage/IMAGE-1608031183157.PNG&quot;); width: 50px; height: 50px;"></div>' +
                    '<div class="pz-design-item-name">No Liner</div>' +
                    '</div>' +
                    '</li>';
                $('.linerli').html(mediaframehtml);
            }
        },
        getAjaxDetails: function (payload, type) {
            payload.product = $('#pz_product_id').val();
            var self = this;
            var responseArray = [];
            $.ajax({
                url: BASE_URL + this.options.customiseUrl,
                type: "POST",
                datatype: "json",
                showLoader: true,
                data: {
                    product: $('#pz_product_id').val(),
                    payload,
                    type: type
                },
                success: function (response) {
                    if (type == 'frame') {
                        $('#pz_magento_framedata').val(JSON.stringify(response['content']));
                        self.callFrameRightContent($.trim($(self.options.sizeSlider).text()));
                    } else if (type == 'liner') {
                        self.callLinerRightContent(response['content']);
                    } else if (type == 'topmat' || type == 'bottommat') {
                        self.callMatRightContent(response['content'], type);
                    } else if (type == "price"){
                        if(self.vars.accessRestriction != 3)  {
                            $('.pz-custom-itemspriceconf').show();
                        }
                        $("#configured-price").html('');
                        $("#customised-price").val('');
                        $("#customised-price").val(response['content']['configureddisplayprice']);
                        $("#configured-price").html(response['content']['configureddisplayprice']);
                        $("#selling_price").val('');
                        $("#selling_price").val(response['content']['configuredsellingprice']);
                        $("#configurator_price").val(response['content']['configuredsellingprice']);
                        // $("#params_addtocart").val('');
                        //$("#params_addtocart").val(JSON.stringify(payload));
                    }
                },
                error: function (err) {
                }
            });
        },
        getArtworkData: function () {
            var selectedSizeOption = $.trim($(this.options.sizeSlider).text()),
                artworkData = {},
                selectedSize,
                selectedFrameSku, returnedFrameData, frameWidth,
                frameType = 'no-type';
            if (selectedSizeOption.indexOf('x') != -1) {
                selectedSize = selectedSizeOption.split('x');
            } else {
                selectedSize = selectedSizeOption.split('\u00d7');
            }
            selectedFrameSku = $(this.options.frameOptionDiv).find('.pz-design-item.selectedFrame').attr('data-sku');
            returnedFrameData = JSON.parse($(this.options.mageFrameData).val());
            if (selectedFrameSku && returnedFrameData[selectedFrameSku]) {
                frameWidth = returnedFrameData[selectedFrameSku]['m_frame_width'];
                frameType = returnedFrameData[selectedFrameSku]['m_frame_type'];
            }
            artworkData.outerWidth = selectedSize[0];
            artworkData.outerHeight = selectedSize[1];
            artworkData.frameWidth = frameWidth;
            artworkData.linerWidth = 0;
            artworkData.frameType = frameType;
            return artworkData;
        },
        getOuterDimensionCalc: function (artworkData) {
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
            return [parseFloat(glassWidth).toFixed(2), parseFloat(glassHeight).toFixed(2)];
        },
        getCustomizedPrice: function (){
            var selectedMediumOption, selectedTreatmentOption, selectedFrameSku, selectedTopMatSku, selectedBottomMatSku,
                selectedLinerSku, selectedSizeOption, selectedSize, glassWidth = 0, glassHeight = 0, outerDimensionValue, artworkData, imageWidth = 0, imageHeight = 0;
            selectedSizeOption = $.trim($(this.options.sizeSlider).text());
            if (selectedSizeOption.indexOf('×') != -1) {
                selectedSize = selectedSizeOption.split(/[\s″×]+/); //selectedSizeOption.split('×');
                glassWidth = selectedSize[0];
                glassHeight = selectedSize[1];
            }
            artworkData = this.getArtworkData();
            artworkData.frameWidth = $(this.options.frameOptionDiv).find(".pz-design-item.selectedFrame").attr('data-width');
            artworkData.frameType = $(this.options.frameOptionDiv).find(".pz-design-item.selectedFrame").attr('data-type');
            artworkData.linerWidth = $(this.options.linerOptionDiv).find('.pz-design-item.selectedFrame').attr('data-width');
            outerDimensionValue = this.getOuterDimensionCalc(artworkData);
            var itemWidth = outerDimensionValue[0];
            var itemHeight = outerDimensionValue[1];
            var imageDimention = getDefaultImageDimention();
            imageWidth = imageDimention[0];
            imageHeight = imageDimention[1];
            selectedMediumOption = $(this.options.mediumOptionDiv).find(".medium-select-elem option:selected").val();
            selectedTreatmentOption = $(this.options.mediumOptionDiv).find(".treatment-select-elem option:selected").val();
            selectedFrameSku = $(this.options.frameOptionDiv).find(".pz-design-item.selectedFrame").attr('data-sku');
            selectedTopMatSku = $(this.options.topMatOptionDiv).find(".pz-design-item.selectedFrame").attr('data-sku');
            selectedBottomMatSku = $(this.options.bottomMatOptionDiv).find('.pz-design-item.selectedFrame').attr('data-sku');
            selectedLinerSku = $(this.options.linerOptionDiv).find('.pz-design-item.selectedFrame').attr('data-sku');
            var color = '';
            var ischecked = $(".showartworktext").is(':checked');
            if (ischecked == false) {
                color = '';
            }   else {
                color = 1;
            }
            var payload = {
                'product_id': document.getElementById('web_product_id') ? document.getElementById('web_product_id').value : null,
                'medium': selectedMediumOption,
                'treatment': selectedTreatmentOption,
                'frame_sku': selectedFrameSku,
                'top_mat_sku': selectedTopMatSku,
                'bottom_mat_sku': selectedBottomMatSku,
                'liner_sku': selectedLinerSku,
                'image_width': imageWidth,
                'image_height': imageHeight,
                'glass_width': glassWidth,
                'glass_height': glassHeight,
                'item_width': itemWidth,
                'item_height': itemHeight,
                'custom_color': color
            };
            this.getAjaxDetails(payload, 'price');
        },
        callFrameRightContent: function (size) {
            var self = this,
                selectedMedia, selectedTreatment, returnedData, returnedFrameData;
            var framedetail = '',
                locSearchVal = '',
                colorSearchVal = [],
                widthSearchVal = '',
                frameColorFilter = {},
                colorSubVal = [],
                typeSearchVal = '',
                framesizefilter = {},
                frametypefilter = {};
            selectedMedia = $(self.options.mediumOptionDiv).find(".medium-select-elem option:selected").val();
            selectedTreatment = $(self.options.mediumOptionDiv).find(".treatment-select-elem option:selected").val();
            var defaultTreatment = self.options.defaultDatas['treatDefault'];
            var frameDefault = self.options.defaultDatas['frameDefault'];
            returnedData = $(self.options.apiReturnData).val();
            returnedFrameData = $(self.options.mageFrameData).val();
            var artworkData = {};
            if (size.indexOf('x') != -1) {
                var selectedSize = size.split('x');
            } else {
                var selectedSize = size.split('\u00d7');
            }
            returnedData = JSON.parse(returnedData);
            returnedFrameData = JSON.parse(returnedFrameData);
            framedetail = returnedFrameData;
            var frameTypesAllowed = [];
            let noframeShow = 0;
            $.each(returnedData[selectedMedia]['treatment'][selectedTreatment]['frames'], function (framekey, framedata) {
                frameTypesAllowed.push(framedata);
                if (framedata.toLowerCase().includes('unframed')) {
                    noframeShow = 1;
                }
            });
            var requiresLiner = returnedData[selectedMedia]['treatment'][selectedTreatment]['requires_liner'];
            var selectedFrameText = '';
            var selected = '';
            if (self.vars.pageEdit == 0 && self.vars.cartProperties['frame']) {
                if (self.vars.cartProperties['frame'] == 'No Frame') {
                    selected = 'selectedFrame';
                }
            }
            var mediaframehtml = '';
            if (noframeShow == 1 || Object.keys(returnedFrameData).length == 0) {
                mediaframehtml = '<li class="pz-design-item zeroth-value ' + selected + '" data-tab="" data-color="" data-sku="" data-width="" data-color-frame="" data-type="">' +
                    '<div class="pz-design-item-content">' +
                    '<div class="pz-design-item-img" style="background: url(&quot;https://devcloud.productimize.com/v3/promizenode/./assets/images/61/OptionImages/StandardImage/IMAGE-1608031183157.PNG&quot;); width: 50px; height: 50px;"></div>' +
                    '<div class="pz-design-item-name"> No Frame </div>' +
                    '</div>' +
                    '</li>';
            }
            let widthLi = '';
            let typeLi = '';
            let colorlist = '';
            let widthdata = [];
            widthLi = '<li class="pz-design-item widthli" id="widthli0" role="button" tab-index="0" tabindex="0" data-index="0">Select Width</li>';
            typeLi = '<li class="pz-design-item typeli typeli0" role="button" tab-index="0" tabindex="0" data-index="0">Select Type</li>';
            colorlist = '<div class="clearcolor">CLEAR ALL</div><div class="maincolor"><input type="checkbox" id="All Color" class="allcolorinput" name="All Color" value="All Color" /><label for="All Color"> All Color</label></div>';
            let typearray = [];
            let widthRelatedArr = {};
            $.each(returnedFrameData, function (framekey, framedata) {
                if(framedata['m_frame_type'] && framedata['m_frame_type'].toLowerCase().includes('edge'))    {
                }   else {
                    if ($.inArray(framedata['m_frame_type'], frameTypesAllowed) !== -1) {
                        if((parseInt(requiresLiner) && parseInt(requiresLiner) == 1 && framedata['m_show_with_liners'] == 'Yes') || (parseInt(requiresLiner) == 0)) {
                            var minRabbetDepth = parseFloat(returnedData[selectedMedia]['treatment'][selectedTreatment]['min_rabbet_depth']);
                            artworkData.outerWidth = selectedSize[0];
                            artworkData.outerHeight = selectedSize[1];
                            artworkData.frameWidth = framedata['m_frame_width'];
                            artworkData.linerWidth = 0;
                            artworkData.frameType = framedata['m_frame_type'];
                            var glassDimention = getGlassDimention(artworkData);
                            var glassSize = glassDimention[0] * glassDimention[1];
                            if (framedata['m_frame_width_range'] && $.inArray(framedata['m_frame_width_range'], widthdata) == -1) {
                                widthdata.push(framedata['m_frame_width_range']);
                                let dataKey = framedata['m_frame_width'].replace(/\./g, "") + ',' + framedata['m_color_family'] + ',' + framedata['m_frame_type'];
                                let widthLikey = framedata['m_frame_width_range'].replace(/\./g, "").replace(/['"]+/g, '').replace(/ /g, "")
                                //widthLi += '<li class="pz-design-item widthli" dataKey="' + dataKey + '" id="widthli' + widthLikey + '"  role="button" tab-index="0" tabindex="0" data-index="0">' + framedata['m_frame_width_range'] + '</li>';
                                widthRelatedArr[framedata['m_frame_width_range']] = {
                                    'dataKey' : dataKey,
                                    'widthLikey' : widthLikey
                                }
                            }
                            if ($.inArray(framedata['m_frame_type'].trim(), typearray) == -1) {
                                typearray.push(framedata['m_frame_type']);
                            }
                            if (framedata['m_color_family']) {
                                if (!frameColorFilter[framedata['m_color_family']]) {
                                    frameColorFilter[framedata['m_color_family']] = [];
                                    framesizefilter[framedata['m_color_family']] = [];
                                    frametypefilter[framedata['m_color_family']] = [];
                                }
                                if(framedata['m_color_frame'])  {
                                    if ($.inArray(framedata['m_color_frame'].trim(), frameColorFilter[framedata['m_color_family']]) == -1) {
                                        frameColorFilter[framedata['m_color_family']].push(framedata['m_color_frame'].trim());
                                        framesizefilter[framedata['m_color_family']].push(framedata['m_frame_width'].replace(/\./g, ""));
                                        frametypefilter[framedata['m_color_family']].push(framedata['m_frame_type'].replace(/\./g, ""));
                                    }
                                }
                            }
                            var selected = '';
                            if (self.options.productLevel == 4) {
                                if (frameDefault == framedata['m_sku']) {
                                    selected = 'defaultOption selectedFrame';
                                }
                            }
                            if (self.vars.pageEdit == 0 && self.vars.cartProperties['frame']) {
                                if (self.vars.cartProperties['frame'] == framedata['m_sku']) {
                                    selected = 'selectedFrame';
                                    var artworkData1 = self.getArtworkData();
                                    artworkData1.frameWidth = framedata['m_frame_width'];
                                    artworkData1.frameType = framedata['m_frame_type'];
                                    var outerDimensionValue = self.getOuterDimensionCalc(artworkData1);
                                    self.sizeOuterDimensionTitleAppend(outerDimensionValue);
                                }
                            }
                            var framePath = 'https://devcloud.productimize.com/productimizedemo/perficientJS/images/frames/renderer_';
                            var cornerImage = framedata['thumbnail'] ? framedata['thumbnail'] : '';
                            cornerImage = (cornerImage != "") ? cornerImage : placeHolderSwatchImageUrl;
                            let specImg = framedata.specificationImage ? framedata.specificationImage : '';
                            let widthrangekey = framedata['m_frame_width_range'] ? framedata['m_frame_width_range'].replace(/\./g, "").replace(/['"]+/g, '').replace(/ /g, "") : '';
                            let cornerShareImage = framedata.renderCornerImage ? framedata.renderCornerImage : '';
                            let lengthImage = framedata.renderLengthImage ? framedata.renderLengthImage : '';
                            mediaframehtml += '<li class="pz-design-item ' + selected + '" data-color="' + framedata['m_color_family'] + '" data-sku="' + framedata['m_sku'] + '" data-width-range="' + widthrangekey + '" data-width="' + framedata['m_frame_width'] + '" data-depth="' + framedata['m_frame_depth']  + '" data-color-frame="' + framedata['m_color_frame'] + '" data-type="' + framedata['m_frame_type'] + '" dataCornerImg="'+cornerShareImage+'" specDetail="'+specImg+'"  dataLengthImg="'+lengthImage+'">' +
                                '<div class="pz-design-item-content">' +
                                '<img class="pz-design-item-img owl-lazy" alt="'+framedata['m_sku']+'"  data-src="' + cornerImage + '" width="50px" height="50px" onerror="loadPlaceholderImage(this);return false" />' +
                                '<div class="pz-design-item-name">' + framedata['m_sku'] + ' </div>' +
                                '<div class="pz-design-item-name">' + framedata['m_frame_width'] + '"</div>' +
                                '</div>' +
                                '</li>';
                        }
                    }
                }
            });
            let frameColorFilterkeys = Object.keys(frameColorFilter), framei, len = frameColorFilterkeys.length;
            frameColorFilterkeys.sort();
            for (framei = 0; framei < len; framei++) {
                let framekey = frameColorFilterkeys[framei];
                var joinkeys = framesizefilter[framekey].concat(frametypefilter[framekey]).concat(framekey.replace(/\s/g, ''));
                colorlist += '<div class="maincolor"><div dataKey="' + joinkeys + '" class="checkmainarea" id="maincolor' + framekey.replace(/\s/g, '').toLowerCase() + '"><input type="checkbox" class="maincolorinput" id="' + framekey + '" name="' + framekey + '" value="' + framekey + '"><label for="' + framekey + '"> ' + framekey + '</label></div><div class="subcolor" style="display:none">';
                let frameColorFilterdata = frameColorFilter[framekey]
                frameColorFilterdata.sort();
                $.each(frameColorFilterdata, function (key, val) {
                    colorlist += '<div class="checkarea" id="framesubcolor' + framekey.replace(/\s/g, '').toLowerCase()+'_'+val.replace(/\s/g, '').toLowerCase() + '"><input type="checkbox" class="subcolorinput" id="' + framekey.replace(/\s/g, '').toLowerCase()+'_'+val + '" name="' + val + '" value="' + val + '"><label for="' + framekey.replace(/\s/g, '').toLowerCase()+'_'+val + '"> ' + val + '</label></div>';
                })
                colorlist += '</div></div>';
            }
            $.each(typearray, function (typekey, typeval) {
                typeLi += '<li class="pz-design-item typeli typeli' + typeval + '" role="button" tab-index="0" tabindex="0" data-index="0">' + typeval + '</li>';
            });
            widthdata.sort();
            $.each(widthdata, function (widthDataKey, widthDataValue) {
                var currWidthData = widthRelatedArr[widthDataValue];
                widthLi += '<li class="pz-design-item widthli" dataKey="' + currWidthData['dataKey'] + '" id="widthli' + currWidthData['widthLikey'] + '"  role="button" tab-index="0" tabindex="0" data-index="0">' + widthDataValue + '</li>';
            });

            $('.frameli').hide();
            $('.frameli').html(mediaframehtml)
                .ready(function () {
                    setTimeout(() => {
                        if (mediaframehtml && Object.keys(returnedFrameData).length > 0 && $('.frameli li').length > 0 ) {
                            $('.frameli').owlCarousel('destroy');
                            $('.frameli').owlCarousel({
                                items: 3,
                                lazyLoad: true,
                                loop: false,
                                margin: 10,
                                stagePadding: 50,
                                nav: true,
                                navText: [
                                    '<i class="fa fa-chevron-left" aria-hidden="true"></i>',
                                    '<i class="fa fa-chevron-right" aria-hidden="true"></i>'
                                ],
                                responsive: {
                                    0: {
                                        items: 3
                                    },
                                    600: {
                                        items: 4
                                    },
                                    1000: {
                                        items: 5
                                    }
                                }
                            });
                            $('.frameli').show();
                        }   else {
                            $('.frameli').show();
                        }
                        if ($('.frameli li').length > 5) {
                            $('.frameli').find('.owl-next,.owl-prev').show();
                        }   else {
                            $('.frameli').find('.owl-next,.owl-prev').hide();
                        }
                    }, 1000);
                });
            $('.pz-frame .colorlist').html(colorlist);
            $('.pz-optionwidthsearch ul').html(widthLi);
            $('.pz-frame').find('.pz-optiontypesearch ul').html(typeLi);
        },
        callLinerRightContent: function (linerArray) {
            var self = this,
                selectedMedia, selectedTreatment, returnedData, returnedFrameData;
            var framedetail = '',
                locSearchVal = '',
                colorSearchVal = [],
                widthSearchVal = '',
                frameColorFilter = {},
                colorSubVal = [],
                typeSearchVal = '',
                framesizefilter = {},
                frametypefilter = {};
            selectedMedia = $(self.options.mediumOptionDiv).find(".medium-select-elem option:selected").val();
            selectedTreatment = $(self.options.mediumOptionDiv).find(".treatment-select-elem option:selected").val();
            var linerDefault = this.options.defaultDatas['linerDefault'];
            returnedData = $(self.options.apiReturnData).val();
            returnedFrameData = $(self.options.mageFrameData).val();
            var artworkData = {};
            artworkData.outerWidth = "24";
            artworkData.outerHeight = "32";
            artworkData.frameWidth = "3.75";
            artworkData.linerWidth = 0;
            artworkData.frameType = "Standard";
            var glassDimention = getGlassDimention(artworkData)
            returnedData = JSON.parse(returnedData);
            returnedFrameData = linerArray;
            var frameTypesAllowed = [];
            $.each(returnedData[selectedMedia]['treatment'][selectedTreatment]['frames'], function (framekey, framedata) {
                frameTypesAllowed.push(framekey);
            });
            frameTypesAllowed = ["Liner"];
            var selectedFrameText = '';
            var selected = '';
            if (self.vars.pageEdit == 0 && self.vars.cartProperties['liner']) {
                if (self.vars.cartProperties['liner'] == 'No Liner') {
                    selected = 'selectedFrame';
                    self.vars.customCartProperty['liner'] = 'No Liner';
                    self.cartEnableCheck();
                }
            }
            var mediaframehtml = '<li class="pz-design-item no-liner zeroth-value ' + selected + '" data-color="" data-sku="" data-width="" data-color-frame="" data-type="">' +
                '<div class="pz-design-item-content">' +
                '<div class="pz-design-item-img" style="background: url(&quot;https://devcloud.productimize.com/v3/promizenode/./assets/images/61/OptionImages/StandardImage/IMAGE-1608031183157.PNG&quot;); width: 50px; height: 50px;"></div>' +
                '<div class="pz-design-item-name">No Liner</div>' +
                '</div>' +
                '</li>';
            let widthdata = [];
            let widthLi = '<li class="pz-design-item widthli" id="widthli0" role="button" tab-index="0" tabindex="0" data-index="0">Select Width</li>';
            let colorlist = '<div class="clearcolor">CLEAR ALL</div><div class="maincolor"><input type="checkbox" id="All Color" class="allcolorinput" name="All Color" value="All Color" /><label for="All Color"> All Color</label></div>';
            var i = 1;
            var requiresLiner = returnedData[selectedMedia]['treatment'][selectedTreatment]['requires_liner'];
            if (parseInt(requiresLiner)) {
                if(Object.keys(returnedFrameData).length > 0){
                    mediaframehtml = '';
                }
                $.each(returnedFrameData, function (framekey, framedata) {
                    if ($.inArray(framedata['m_liner_type'], frameTypesAllowed) !== -1) {
                        if (framedata['m_color_family']) {
                            if (!frameColorFilter[framedata['m_color_family']]) {
                                frameColorFilter[framedata['m_color_family']] = [];
                            }
                            if ($.inArray(framedata['m_color_liner'].trim(), frameColorFilter[framedata['m_color_family']]) == -1) {
                                frameColorFilter[framedata['m_color_family']].push(framedata['m_color_liner'].trim());
                            }
                        }
                        selected = '';
                        if (self.options.productLevel == 4) {
                            if (linerDefault == framedata['m_sku']) {
                                selected = 'defaultOption';
                            }
                        }
                        if (self.vars.pageEdit == 0 && self.vars.cartProperties['liner']) {
                            if (self.vars.cartProperties['liner'] == framedata['m_sku']) {
                                selected = 'selectedFrame';
                                var artworkData = self.getArtworkData();
                                artworkData.linerWidth = framedata['m_liner_width'];
                                var outerDimensionValue = self.getOuterDimensionCalc(artworkData);
                                self.sizeOuterDimensionTitleAppend(outerDimensionValue);
                            }
                        }
                        if(framedata['default_product'] && framedata['default_product'] == 1)    {
                            selected = ' defaultframe hide';
                        }
                        var linerThumbPath = framedata['thumbnail'] ? framedata['thumbnail'] : '';
                        linerThumbPath = (linerThumbPath != "" ) ? linerThumbPath : placeHolderSwatchImageUrl;
                        let cornerShareImage = framedata.renderCornerImage ? framedata.renderCornerImage : '';
                        let lengthImage = framedata.renderLengthImage ? framedata.renderLengthImage : '';
                        mediaframehtml += '<li class="pz-design-item ' + selected + '" data-color="' + framedata['m_color_family'] + '" data-sku="' + framedata['m_sku'] + '" data-width="' + framedata['m_liner_width'] + '" data-depth="' + framedata['m_liner_depth'] + '" data-color-frame="' + framedata['m_color_liner'] + '" data-type="' + framedata['m_frame_type'] + '" dataCornerImg="'+cornerShareImage+'" dataLengthImg="'+lengthImage+'">' +
                            '<div class="pz-design-item-content">' +
                            '<img class="pz-design-item-img owl-lazy" alt="'+framedata['m_sku']+'" data-src="' + linerThumbPath + '" width="50px" height="50px" onerror="loadPlaceholderImage(this);return false" />' +
                            '<div class="pz-design-item-name">' + framedata['m_sku'] + ' </div>' +
                            '<div class="pz-design-item-name">' + framedata['m_color_liner'] + '</div>' +
                            '</div>' +
                            '</li>';
                    }
                    i++;
                });
            }
            $.each(frameColorFilter, function (frameColorFilterkey, frameColorFilterdata) {
                colorlist += '<div class="maincolor"><div class="checkmainarea" id="maincolor' + frameColorFilterkey.replace(/\s/g, '').toLowerCase() + '"><input type="checkbox" class="maincolorinput" id="' + frameColorFilterkey + '" name="' + frameColorFilterkey + '" value="' + frameColorFilterkey + '"><label for="' + frameColorFilterkey + '"> ' + frameColorFilterkey + '</label></div><div class="subcolor">';
                $.each(frameColorFilterdata, function (key, val) {
                    colorlist += '<div class="checkarea" id="subcolor' + val.replace(/\s/g, '').toLowerCase() + '"><input type="checkbox" class="subcolorinput" id="' + val + '" name="' + val + '" value="' + val + '"><label for="' + val + '"> ' + val + '</label></div>';
                })
                colorlist += '</div></div>';
            });

            $('.pz-liner .colorlist').html(colorlist);
            $('.linerli').html(mediaframehtml)
                .ready(function () {
                    setTimeout(() => {
                        if (parseInt(requiresLiner) && mediaframehtml && Object.keys(returnedFrameData).length > 0 && $('.linerli li').length > 0) {
                            $('.linerli').owlCarousel('destroy');
                            $('.linerli').owlCarousel({
                                items: 4,
                                lazyLoad: true,
                                loop: false,
                                margin: 10,
                                stagePadding: 50,
                                nav: true,
                                navText: [
                                    '<i class="fa fa-chevron-left" aria-hidden="true"></i>',
                                    '<i class="fa fa-chevron-right" aria-hidden="true"></i>'
                                ],
                                responsive: {
                                    0: {
                                        items: 3
                                    },
                                    600: {
                                        items: 4
                                    },
                                    1000: {
                                        items: 5
                                    }
                                }
                            });
                        }
                        if ($('.linerli li').length > 5) {
                            $('.linerli').find('.owl-next,.owl-prev').show();
                        }   else {
                            $('.linerli').find('.owl-next,.owl-prev').hide();
                        }
                    }, 1000);
                });
            $('.pz-liner .pz-optionwidthsearch ul').append(widthLi);
        },
        callMatRightContent: function (matArray, matTypeOption) {
            var self = this,
                selectedMedia, selectedTreatment, returnedData, returnedFrameData;
            var framedetail = '',
                locSearchVal = '',
                colorSearchVal = [],
                widthSearchVal = '',
                frameColorFilter = {},
                colorSubVal = [],
                typeSearchVal = '',
                framesizefilter = {},
                frametypefilter = {};
            selectedMedia = $(self.options.mediumOptionDiv).find(".medium-select-elem option:selected").val();
            selectedTreatment = $(self.options.mediumOptionDiv).find(".treatment-select-elem option:selected").val();
            returnedData = $(self.options.apiReturnData).val();
            returnedFrameData = $(self.options.mageFrameData).val();
            var artworkData = {};
            artworkData.outerWidth = "24";
            artworkData.outerHeight = "32";
            artworkData.frameWidth = "3.75";
            artworkData.linerWidth = 0;
            artworkData.frameType = "Standard";
            var glassDimention = getGlassDimention(artworkData)
            returnedData = JSON.parse(returnedData);
            returnedFrameData = matArray;
            var frameTypesAllowed = [];
            $.each(returnedData[selectedMedia]['treatment'][selectedTreatment]['frames'], function (framekey, framedata) {
                frameTypesAllowed.push(framekey);
            });
            var frameType = "Standard";
            frameType = frameType.toLowerCase();
            frameTypesAllowed = [frameType];
            var requiresTopMat = returnedData[selectedMedia]['treatment'][selectedTreatment]['requires_top_mat'];
            var requiresBotMat = returnedData[selectedMedia]['treatment'][selectedTreatment]['requires_bottom_mat'];
            var selectedFrameText = '';
            var selected = '';
            if (self.vars.pageEdit == 0 && self.vars.cartProperties['top mat']) {
                if (self.vars.cartProperties['top mat'] == 'No Mat') {
                    selected = 'selectedFrame';
                    self.vars.customCartProperty['top mat'] = 'No Mat';
                    self.cartEnableCheck();
                }
            }
            var mediaframehtml = '<li class="pz-design-item no-mat zeroth-value ' + selected + '" data-color="" data-sku="" data-width="" data-color-frame="" data-type="">' +
                '<div class="pz-design-item-content">' +
                '<div class="pz-design-item-img" style="background: url(&quot;https://devcloud.productimize.com/v3/promizenode/./assets/images/61/OptionImages/StandardImage/IMAGE-1608031183157.PNG&quot;); width: 50px; height: 50px;"></div>' +
                '<div class="pz-design-item-name">No Mat</div>' +
                '</div>' +
                '</li>';
            selected = '';
            if (self.vars.pageEdit == 0 && self.vars.cartProperties['bottom mat']) {
                if (self.vars.cartProperties['bottom mat'] == 'No Mat') {
                    selected = 'selectedFrame';
                    self.vars.customCartProperty['bottom mat'] = 'No Mat';
                    self.cartEnableCheck();
                }
            }
            var mediaframebothtml = '<li class="pz-design-item no-mat zeroth-value ' + selected + '" data-color="" data-sku="" data-width="" data-color-frame="" data-type="">' +
                '<div class="pz-design-item-content">' +
                '<div class="pz-design-item-img" style="background: url(&quot;https://devcloud.productimize.com/v3/promizenode/./assets/images/61/OptionImages/StandardImage/IMAGE-1608031183157.PNG&quot;); width: 50px; height: 50px;"></div>' +
                '<div class="pz-design-item-name">No Mat</div>' +
                '</div>' +
                '</li>';
            var nomathtml = mediaframehtml;
            let widthdata = [];
            let widthLi = '<li class="pz-design-item widthli" id="widthli0" role="button" tab-index="0" tabindex="0" data-index="0">Select Width</li>';
            let typeLi = '<li class="pz-design-item typeli typeli0" role="button" tab-index="0" tabindex="0" data-index="0">Select Type</li>';
            let colorlist = '<div class="clearcolor">CLEAR ALL</div><div class="maincolor"><input type="checkbox" id="All Color Top" class="allcolorinput" name="All Color" value="All Color" /><label for="All Color Top"> All Color</label></div>';
            let colorlistbot = '<div class="clearcolor">CLEAR ALL</div><div class="maincolor"><input type="checkbox" id="All Color Bot" class="allcolorinput" name="All Color" value="All Color" /><label for="All Color Bot"> All Color</label></div>';
            var i = 1;
            let typearray = [];
            var requiresMat = (matTypeOption == 'topmat') ? requiresTopMat : requiresBotMat;
            if ((matTypeOption == "topmat" && parseInt(requiresTopMat) != 0) || (matTypeOption == "bottommat" && parseInt(requiresBotMat) != 0)) {
                mediaframehtml = '';
                mediaframebothtml = '';
                $.each(returnedFrameData, function (framekey, framedata) {
                    //if (framedata['m_mat_type'] && $.inArray(framedata['m_mat_type'].toLowerCase(), frameTypesAllowed) !== -1) {

                    if ($.inArray(framedata['m_mat_type'].trim(), typearray) == -1) {
                        typearray.push(framedata['m_mat_type']);
                    }
                    if (framedata['m_color_family']) {
                        if (!frameColorFilter[framedata['m_color_family']]) {
                            frameColorFilter[framedata['m_color_family']] = [];
                        }
                        if (framedata['m_color_mat'] && $.inArray(framedata['m_color_mat'].trim(), frameColorFilter[framedata['m_color_family']]) == -1) {
                            frameColorFilter[framedata['m_color_family']].push(framedata['m_color_mat'].trim());
                        }
                    }
                    selected = '';
                    if (i == 1) {
                        selectedFrameText = ' / ' + ' B97 ' + ' / ' + ' White';
                        const data = {
                            'sku': 'B97',
                            'width': 'White'
                        }
                    }
                    if (self.vars.pageEdit == 0 && self.vars.cartProperties['top mat']) {
                        if (self.vars.cartProperties['top mat'] == framedata['m_sku']) {
                            selected = 'selectedFrame';
                        }
                    }
                    if(framedata['default_product'] && framedata['default_product'] == 1)    {
                        selected = ' defaultframe hide';
                    }
                    var matPath = 'https://devcloud.productimize.com/productimizedemo/perficientJS/images/mats/';
                    //var matThumbImage = matPath + framedata['m_sku'] + '_thumbnail.PNG';
                    var matThumbImage = framedata.renderDisplayImage ? framedata.renderDisplayImage : '';
                    matThumbImage = (matThumbImage != "") ? matThumbImage : placeHolderSwatchImageUrl;
                    let cornerShareImage = framedata.renderCornerImage ? framedata.renderCornerImage : '';
                    let lengthImage = framedata.rendererImage ? framedata.rendererImage : '';
                    mediaframehtml += '<li class="pz-design-item ' + selected + '" data-color="' + framedata['m_color_family'] + '" data-sku="' + framedata['m_sku'] + '" data-width="' + framedata['m_mat_width'] + '" data-color-frame="' + framedata['m_color_mat'] + '" data-type="' + framedata['m_mat_type'] + '" dataCornerImg="'+cornerShareImage+'" dataLengthImg="'+lengthImage+'">' +
                        '<div class="pz-design-item-content">' +
                        '<img class="pz-design-item-img owl-lazy" alt="'+framedata['m_sku']+'"  data-src="' + matThumbImage + '" width="50px" height="50px" onerror="loadPlaceholderImage(this);return false" />' +
                        '<div class="pz-design-item-name">' + framedata['m_sku'] + ' </div>' +
                        '<div class="pz-design-item-name">' + framedata['m_color_mat'] + '</div>' +
                        '</div>' +
                        '</li>';
                    selected = '';
                    if (self.vars.pageEdit == 0 && self.vars.cartProperties['bottom mat']) {
                        if (self.vars.cartProperties['bottom mat'] == framedata['m_sku']) {
                            selected = 'selectedFrame';
                        }
                    }
                    if(framedata['default_product'] && framedata['default_product'] == 1)    {
                        selected = ' defaultframe hide';
                    }
                    mediaframebothtml += '<li class="pz-design-item ' + selected + '" data-color="' + framedata['m_color_family'] + '" data-sku="' + framedata['m_sku'] + '" data-width="' + framedata['m_mat_width'] + '" data-color-frame="' + framedata['m_color_mat'] + '" data-type="' + framedata['m_mat_type'] + '" dataCornerImg="'+cornerShareImage+'" dataLengthImg="'+lengthImage+'">' +
                        '<div class="pz-design-item-content">' +
                        '<img class="pz-design-item-img owl-lazy" alt="'+framedata['m_sku']+'"  data-src="' + matThumbImage + '" width="50px" height="50px" onerror="loadPlaceholderImage(this);return false" />' +
                        '<div class="pz-design-item-name">' + framedata['m_sku'] + ' </div>' +
                        '<div class="pz-design-item-name">' + framedata['m_color_mat'] + '</div>' +
                        '</div>' +
                        '</li>';
                    //}
                    i++;
                });
            }
            let frameColorFilterkeys = Object.keys(frameColorFilter), frmi, len = frameColorFilterkeys.length;
            frameColorFilterkeys.sort();
            for (frmi = 0; frmi < len; frmi++) {
                let framekey = frameColorFilterkeys[frmi];
                colorlist += '<div class="maincolor"><div class="checkmainarea" id="maincolor' + framekey.replace(/\s/g, '').toLowerCase() + '"><input type="checkbox" class="maincolorinput" id="Top ' + framekey + '" name="' + framekey + '" value="' + framekey + '"><label for="Top ' + framekey + '"> ' + framekey + '</label></div><div class="subcolor" style="display:none">';
                colorlistbot += '<div class="maincolor"><div class="checkmainarea" id="botmaincolor' + framekey.replace(/\s/g, '').toLowerCase() + '"><input type="checkbox" class="maincolorinput" id="bot' + framekey + '" name="' + framekey + '" value="' + framekey + '"><label for="bot' + framekey + '"> ' + framekey + '</label></div><div class="subcolor" style="display:none">';
                let frameColorFilterdata = frameColorFilter[framekey]
                frameColorFilterdata.sort();
                $.each(frameColorFilterdata, function (key, val) {
                    colorlist += '<div class="checkarea" id="subcolor' + framekey.replace(/\s/g, '').toLowerCase()+'_'+val.replace(/\s/g, '').toLowerCase() + '"><input type="checkbox" class="subcolorinput" id="' + val + '" name="' + val + '" value="' + val + '"><label for="' + val + '"> ' + val + '</label></div>';
                    colorlistbot += '<div class="checkarea" id="botsubcolor' +framekey.replace(/\s/g, '').toLowerCase()+'_'+ val.replace(/\s/g, '').toLowerCase() + '"><input type="checkbox" class="subcolorinput" id="bot' + val + '" name="' + val + '" value="' + val + '"><label for="bot' + val + '"> ' + val + '</label></div>';
                })
                colorlist += '</div></div>';
                colorlistbot += '</div></div>';
            }
            $.each(typearray, function (typekey, typeval) {
                typeLi += '<li class="pz-design-item typeli typeli' + typeval + '" role="button" tab-index="0" tabindex="0" data-index="0">' + typeval + '</li>';
            });
            if (matTypeOption == "topmat") {
                $('.topmatli').html("");
                if (parseInt(requiresTopMat) == 0 || Object.keys(returnedFrameData).length == 0) {
                    $('.topmatli').html(nomathtml);
                } else {
                    $('.topmatli').html(mediaframehtml)
                        .ready(function () {
                            setTimeout(() => {
                                if (mediaframehtml && Object.keys(returnedFrameData).length > 0 && $('.topmatli li').length > 0) {
                                    $('.topmatli').owlCarousel('destroy');
                                    $('.topmatli').owlCarousel({
                                        items: 4,
                                        lazyLoad: true,
                                        loop: false,
                                        margin: 10,
                                        stagePadding: 50,
                                        nav: true,
                                        navText: [
                                            '<i class="fa fa-chevron-left" aria-hidden="true"></i>',
                                            '<i class="fa fa-chevron-right" aria-hidden="true"></i>'
                                        ],
                                        responsive: {
                                            0: {
                                                items: 3
                                            },
                                            600: {
                                                items: 4
                                            },
                                            1000: {
                                                items: 5
                                            }
                                        }
                                    });
                                }
                                if ($('.topmatli li').length > 5) {
                                    $('.topmatli').find('.owl-next,.owl-prev').show();
                                }   else {
                                    $('.topmatli').find('.owl-next,.owl-prev').hide();
                                }
                            }, 1000);
                        });
                }
                $('.pz-top-mat .colorlist').html(colorlist);
                $('.pz-top-mat').find('.pz-optiontypesearch ul').html(typeLi);
            } else if (matTypeOption == "bottommat") {
                $('.bottommatli').html("");
                if (parseInt(requiresBotMat) == 0  || Object.keys(returnedFrameData).length == 0) {
                    $('.bottommatli').html(nomathtml);
                } else {
                    $('.bottommatli').html(mediaframebothtml)
                        .ready(function () {
                            setTimeout(() => {
                                if (mediaframebothtml && Object.keys(returnedFrameData).length > 0 && $('.bottommatli li').length > 0) {
                                    $('.bottommatli').owlCarousel('destroy');
                                    $('.bottommatli').owlCarousel({
                                        items: 4,
                                        lazyLoad: true,
                                        loop: false,
                                        margin: 10,
                                        stagePadding: 50,
                                        nav: true,
                                        navText: [
                                            '<i class="fa fa-chevron-left" aria-hidden="true"></i>',
                                            '<i class="fa fa-chevron-right" aria-hidden="true"></i>'
                                        ],
                                        responsive: {
                                            0: {
                                                items: 3
                                            },
                                            600: {
                                                items: 4
                                            },
                                            1000: {
                                                items: 5
                                            }
                                        }
                                    });
                                }
                                if ($('.bottommatli li').length > 5) {
                                    $('.bottommatli').find('.owl-next,.owl-prev').show();
                                }   else {
                                    $('.bottommatli').find('.owl-next,.owl-prev').hide();
                                }
                            },1000);
                        });
                }
                $('.pz-bottom-mat .colorlist').html(colorlistbot);
                $('.pz-bottom-mat').find('.pz-optiontypesearch ul').html(typeLi);
            }
            $('.pz-top-mat .pz-optionwidthsearch ul').append(widthLi);
            $('.pz-bottom-mat .pz-optionwidthsearch ul').append(widthLi);
        },
        resetNextTabs: function (currentVal, setSelectedOptions = null) {
            var localObj = this.vars.customizerTabsObj;
            var currentTab = currentVal.toLowerCase();
            var upcoming = '';
            var resetSelectedOptionInc = 0,
                resetSelectedOptions = [];
            var minsizetext = $('.pz-size .rangeleft span').length;
            if (currentTab == 'medtrt' && minsizetext) {
                var mintext = $('.pz-size .rangeleft span').html();
                $('.pz-size output').html(mintext);
                $('.pz-size output').css({'position': 'absolute', 'left': '0%'});
            }
            let customCartProperty = this.vars.customCartProperty;
            let tabLabels = this.vars.tabLabels;
            if (currentTab == 'medtrt') {
                resetSelectedOptions.push('treatment');
            }
            $.each(this.vars.customizerTabs, function (tab, value) {
                if (value == currentTab) {
                    upcoming = tab;
                }
                if (upcoming != '' && tab > upcoming) {
                    $('.pz-item-selected-' + value).html('');
                    $('.pz-design-item-list.' + value + 'li li').removeClass('selectedFrame');
                    localObj[value][0] = 0;
                    if (tabLabels[value] && customCartProperty[tabLabels[value].toLowerCase()]) {
                        delete customCartProperty[tabLabels[value].toLowerCase()];
                        resetSelectedOptionInc++;
                        resetSelectedOptions.push(value);
                    }
                    if (tabLabels[value + 'li'] && customCartProperty[tabLabels[value + 'li'].toLowerCase()]) {
                        delete customCartProperty[tabLabels[value + 'li'].toLowerCase()];
                        resetSelectedOptionInc++;
                        resetSelectedOptions.push(value);
                    }
                    $('.pz-custom-item-header[data-tab=' + value + '] .pz-item-header .pz-item-step-number').css('display', 'flex');
                    $('.pz-custom-item-header[data-tab=' + value + '] .pz-item-header .pz-tick.pz-tick-success').css('display', 'none');
                    $('[data-tab=' + value + ']').parent().find('.pz-custom-item-body textarea').val('');
                    $('[data-tab=' + value + ']').parent().find('.pz-custom-item-body input').prop('checked', false);
                }
            });
            this.cartEnableCheck();
            this.vars.customizerTabsObj = localObj;
            if (resetSelectedOptionInc > 0 && this.vars.pageEdit == 1) {
                updatePZSelectedOptions(setSelectedOptions, resetSelectedOptions);
            }
        }
    });
    return $.mage.customisedOptions;
});
