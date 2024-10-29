require([
    'jquery',
    'mage/translate',
    'underscore',
    'jquery/ui',
    'slick',
    'domReady!',
    'accordion',
    'lazyload',
], function($,translate, _) {
    woObj = '';
    (function ($){
        /*Keyboard key codes*/
        var ESC_CODE = 27;
        var TAB_KEY = 9;
        var KEY_DOWN = 40;
        var KEY_ENTER = 13;
        var MOUSE_KEY_DOWN = 1;

        $.woport = {
            init : function() {
                this.browserAgent.init();
                this.artRangSlider();
                this.locationRangSlider();
                this.footerAccordion();
                this.searchInput();
                this.inspireOverlay();
                this.multislider();
                this.projectShowcase();
                this.projectShowcaseOverlay();
                this.crossSellsSlider();
                this.showHideDetail();
                this.moveSocialMediaSection();
                this.pdpTabShowMoreDescription();
                this.readMoreClick();
                this.priceMultiplier();
                this.catalogActionClick();
                this.addRemoveCMSContent();
                this.menuKeyBoardAccessible();
                this.selectCurrentAccountPage();
                this.redirectToProduct();
                this.mainMenuWcag();
                this.projectShowcaseCareer();
                this.projectShowcaseOverlayCareer();
                this.commonPopupCMSPage();
                this.readLessMoreListingPage();
                this.uiDatePickerChanges();
                this.weightedTooltip();
                this.specificationTooltip();
            },

            /* Browser Detect and add class for safari browser */
            browserAgent : {
                init : function() {
                    if($.woport.browserAgent.isSafari() == true && !(/iPad|iPhone|iPod/.test(navigator.userAgent))) {
                        $('html').addClass('safari');
                    }
                },
                isSafari : function() {
                    if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Chrome') == -1) {
                        return true;
                    }
                }
            },

            /*Source of Art Slick Slider with Range option*/
            artRangSlider :  function(){
                if($.woport.getViewPortSize().width > 767) {
                    var totalcount = $(".source-art-container .carousel .image-container").length;
                    var $carousel = $(".source-art-container .carousel");
                    var slider;
                    var slidecount;
                    var slideToShow = 4;
                    $carousel.not('.slick-initialized').slick({
                        speed: 300,
                        slidesToShow: slideToShow,
                        slidesToScroll: 1,
                        infinite: false,
                        arrows: true,
                        responsive: [
                            {
                                breakpoint: 768,
                                settings: 'unslick'
                            },
                        ]
                    });
                    /*Added text when slide change*/
                    $('.source-art-section').on('afterChange', function (event, slick, currentSlide, slideCount) {
                        var leftValue = slick.slideCount - 4;
                        var leftValueNew = 100 / leftValue;
                        $('.source-art-container .ui-slider-handle.ui-state-default.ui-corner-all').css('left', (leftValueNew * currentSlide) + '%');
                        $('.source-art-container .ui-slider-handle.ui-state-default.ui-corner-all').text('Slide ' + (currentSlide + 1) + ' of Source for Art Slider').attr('aria-live', 'polite');
                    });

                    setTimeout(function () {
                        var rangslidcount = totalcount - 4;
                        var totalslidscount = totalcount - 1;
                        slider = $(".source-art-container .slider").slider({
                            min: 0,
                            max: rangslidcount,
                            create: function (event, ui) {
                                var horizontalhandle = $('.art-inner-container .ui-slider-horizontal').innerWidth() / rangslidcount;
                                $(".art-inner-container .ui-slider-handle").width(horizontalhandle);
                            },
                            slide: function (event, ui) {
                                var slick = $carousel.slick("getSlick");
                                goTo = ui.value * (slick.slideCount - 1) / totalslidscount;
                                $carousel.slick("goTo", goTo);
                            }
                        });
                    }, 200);
                }
            },
            /*Location Slick Slider with Range Option*/
            locationRangSlider :  function(){
                var totalcount = $(".location-container .carousel .image-container").length;
                var $carousel = $(".location-container .carousel");
                var slider;
                var slidecount;
                var slideToShow = 3;
                $carousel.slick({
                    speed : 300,
                    slidesToShow: slideToShow,
                    slidesToScroll: 1,
                    infinite: false,
                    arrows: true,
                    responsive: [
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 1,
                                infinite: false,
                                arrows: false,
                            }
                        }
                        ]
                });
                /*Added text when slide change*/
                $('.locations-section').on('afterChange', function(event, slick, currentSlide, slideCount){
                    var leftValue = slick.slideCount - 3;
                    var leftValueNew = 100 / leftValue;
                    $('.location-container .ui-slider-handle.ui-state-default.ui-corner-all').css('left',(leftValueNew*currentSlide)+'%');
                    $('.location-container .ui-slider-handle.ui-state-default.ui-corner-all').text('Slide '+ (currentSlide + 1) + ' of location slider').attr('aria-live','polite');
                });

                setTimeout(function(){
                    var rangslidcount = totalcount - 3;
                    var totalslidscount = totalcount -1;
                    slider = $( ".location-container .slider" ).slider({
                        min : 0,
                        max : rangslidcount,
                        create: function( event, ui ) {
                            var horizontalhandle = $('.location-container .ui-slider-horizontal').innerWidth() / rangslidcount;
                            $(".location-container .ui-slider-handle").width(horizontalhandle);
                        },
                        slide: function(event, ui) {
                            var slick = $carousel.slick( "getSlick" );
                            goTo = ui.value * (slick.slideCount-1) / totalslidscount  ;
                            $carousel.slick( "goTo", goTo );
                        }
                    });
                }, 200);
            },
            /*Footer Accordion*/
            footerAccordion :  function(){
                var footerHeading = $('.footer-right-inner .footer-block-heading-container');
                footerHeading.bind("click",function(){
                    if( $.woport.getViewPortSize().width < 768){
                        $(this).attr({"tabindex":"0","role":"button","aria-expanded":"true"});
                        if($(this).next('.block-container').css('display')=='block'){
                            $(this).next('.block-container').slideUp();
                            $(this).removeClass('activeTab');
                            $(this).attr({"role":"button","aria-expanded":"false"});
                        }
                        else    {
                            $(this).addClass('activeTab');
                            footerHeading.not(this).removeClass('activeTab');
                            $('.footer-right-inner .block-container').slideUp();
                            $(this).next('.block-container').slideDown();
                            $(this).attr({"aria-expanded":"true","role":"button"});
                        }
                    }
                    else{
                        $(this).removeAttr("aria-expanded role");
                    }
                });
            },

            /* More accurate way of determining viewport/window size because it accounts for Windows scrollbars */
            getViewPortSize : function() {
                var e = window, a = 'inner';
                if (!('innerWidth' in window )) {
                    a = 'client';
                    e = document.documentElement || document.body;
                }
                return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
            },
            /*Header Search Input*/
            searchInput: function() {
                /*Open Search Popup on click on search icon*/
                $(document).on('click','.openBtn', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $("#myOverlay").css('display','block');
                    $('.block-search').addClass('active');
                    $('body').addClass('search-active');
                    $('body').addClass('-amsearch-overlay-opened');
                    $('header').addClass('-opened');
                    $('.amsearch-input').focus();
                });
                /*Closed Search Popup on click on close icon*/
                $(document).on('click','.closebtn',function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    $("#myOverlay").css('display','none');
                    $('.block-search').removeClass('active');
                    $('body').removeClass('search-active');
                    $('body').removeClass('-amsearch-overlay-opened');
                    $(".openBtn").focus();
                });
                /*Closed Search Popup on escape key*/
                $(document).on( 'keydown', function ( e ) {
                    var container = $(".block-search");
                    if ( e.keyCode === 27 ) {
                        $(".block-search").hide();
                        if(container.hasClass('active')){
                            $('.block-search').removeClass('active');
                            $('.openBtn').focus();
                            $('body').removeClass('search-active');
                        }
                        $('body').removeClass('search-active');
                    }
                });
                /*Focus Trap*/
                $('#search').on('keydown', function (e) {
                    if ((e.which === 9 && !e.shiftKey)) {
                        e.preventDefault();
                        $('#closebtn').focus();
                    }
                });
                $('#closebtn').on('keydown', function (e) {
                    if ((e.which === 9 && e.shiftKey)) {
                        e.preventDefault();
                        $('#search').focus();
                    }
                });
                /*Close Search Popup on outside click*/
                $(document).mouseup(function(e) {
                    var container = $(".block-search");
                    if (!container.is(e.target) && container.has(e.target).length === 0) {
                        container.hide();
                        if(container.hasClass('active')){
                            $('.block-search').removeClass('active');
                            $('body').removeClass('search-active');
                            $('.openBtn').focus();
                        }

                    }
                });
            },

            /*Inspire Overlay Homepage*/
            inspireOverlay : function(){
                var selector1 = $('.tiles-container:not(.cms-tiles-container-popup) li');
                selector1.find('.overlay-content').css('display','none');
                $('.tiles-container:not(.cms-tiles-container-popup) img,.tiles-container:not(.cms-tiles-container-popup) .corner-overlay-content').on('click', function (e) {
                    e.preventDefault();
                    $('.tiles-container:not(.cms-tiles-container-popup) .overlay-content').css('display','none');
                    selector1.removeClass('active-tiles');
                    $(this).parents('li').addClass('active-tiles');
                    $(this).parents('li').find('.overlay-content').css('display','block');
                    $(this).parents('li').find('.action-close').focus();
                    if($(this).parents('li').find('.content-item').hasClass('overlayactive')){
                        $(this).parents('li').find('.overlay-content').css('display','none');
                        $(this).parents('li').find('.content-item').removeClass('overlayactive');
                        $(this).parents('li').find('.content-item .overlay-content a').attr('tabindex','-1');
                        $(this).parents('li').find('.content-item .action-close').attr('tabindex','-1');
                        $('.tiles-container:not(.cms-tiles-container-popup)').removeClass('selected-section');
                        $('body').removeClass('modal-open-active');
                    }
                    else{
                        $('.content-item').removeClass('overlayactive');
                        $(this).parents('li').find('.content-item').addClass('overlayactive');
                        $(this).parents('li').find('.content-item .overlay-content a').attr('tabindex','0');
                        $(this).parents('li').find('.content-item .action-close').attr('tabindex','0');
                        $('.tiles-container:not(.cms-tiles-container-popup)').addClass('selected-section');
                        $('body').addClass('modal-open-active');
                    }

                    //Focus Trapping for Mobile view
                    if($.woport.getViewPortSize().width < 768) {
                        var focusableElement = $('.inspire-inner-section .content-item.overlayactive button,.inspire-inner-section .content-item.overlayactive a');
                        var firstFocusableElem = focusableElement[0];
                        var lastFocusableElem = focusableElement[focusableElement.length - 1];
                        $(document).on('keydown', function (e) {
                            var isTabPressed = (e.key === 'Tab' || e.keyCode === 9);
                            if (!isTabPressed) {
                                return;
                            }
                            if (e.shiftKey) {
                                if (document.activeElement === firstFocusableElem) {
                                    lastFocusableElem.focus();
                                    e.preventDefault();
                                }
                            } else {
                                if (document.activeElement === lastFocusableElem) {
                                    firstFocusableElem.focus();
                                    e.preventDefault();
                                }
                            }
                        });
                    }else{
                        $('.inspire-inner-section .content-item.overlayactive a').focus();
                        $('.inspire-inner-section .content-item.overlayactive .action-close').attr('tabindex','-1');
                    }
                });
                /*Close Overlay on close button*/
                $('.inspire-inner-section .action-close').on('click', function (e) {
                    $(this).parents('.content-item').removeClass('active-tiles');
                    $(this).parents('li').find('.overlay-content').css('display','none');
                    $(this).parents('li').find('.content-item').removeClass('overlayactive');
                    $(this).parents('li').find('.content-item .overlay-content a').attr('tabindex','-1');
                    $(this).parents('li').find('.content-item .action-close').attr('tabindex','-1');
                    $('.tiles-container:not(.cms-tiles-container-popup)').removeClass('selected-section');
                    $('body').removeClass('modal-open-active');
                    $(this).parents('.content-item').find('img').focus();
                });
                /*Close Overlay on Escape*/
                var focusableElement  = $('.inspire-inner-section .tiles-container .content-item');
                var parentElement  = $('.inspire-inner-section .tiles-container li');
                $(document).on( 'keydown', function ( e ) {
                    if ( e.keyCode === 27 && focusableElement.hasClass('overlayactive')) {
                        focusableElement.removeClass('overlayactive');
                        focusableElement.find('a').attr('tabindex','-1');
                        focusableElement.find('.action-close').attr('tabindex','-1');
                        $('body').removeClass('modal-open-active');
                    }
                });
            },
            /*Get to know us Page Full Popup*/
            commonPopupCMSPage : function(){
                var teamSelector = $('.cms-tiles-container-popup  img.image-main');
                teamSelector.parents('.content-item').find('.overlay-content').css('display','none');
                $('.cms-tiles-container-popup img.image-main').on('click', function (e) {
                    e.preventDefault();
                    $('.cms-tiles-container-popup .overlay-content').css('display','none');
                    teamSelector.parents('.content-item').removeClass('active-tiles');
                    $(this).parents('.content-item').addClass('active-tiles');
                    $(this).parents('.content-item').find('.overlay-content').css('display','block');
                    $(this).parents('.content-item').find('button.action-close').attr('tabindex','0').focus();
                    if($(this).parents('.content-item').hasClass('overlayactive')){
                        $(this).parents('.content-item').find('.overlay-content').css('display','none');
                        $(this).parents('.content-item').removeClass('overlayactive modal-popup _show');
                        $(this).parents('.content-item').find('.modals-overlay').remove();
                        $(this).parents('.content-item').find('.overlay-content a').attr('tabindex','-1');
                        $(this).parents('.content-item').find('button.action-close').attr('tabindex','-1');
                        $(this).parents('.content-item').find('.overlay-content img').attr('tabindex','-1');
                        $('.cms-tiles-container-popup').removeClass('selected-section');
                        $('body').removeClass('modal-open-active');
                    }
                    else{
                        $('.content-item').removeClass('overlayactive modal-popup _show');
                        $(this).parents('.content-item').addClass('overlayactive modal-popup _show').append('<div class="modals-overlay"></div>');
                        $(this).parents('.content-item').find('.overlay-content a').attr('tabindex','0');
                        $(this).parents('.content-item').find('button.action-close').attr('tabindex','0');
                        $(this).parents('.content-item').find('.overlay-content img').removeAttr('tabindex');
                        $('.cms-tiles-container-popup').addClass('selected-section');
                        $('body').addClass('modal-open-active');
                    }
                });
                /*Focus Trap*/
                $(document).on('keydown', '.cms-tiles-container-popup .overlayactive .action-close', function(e){
                    if (e.keyCode == 9 && e.shiftKey == true && $(this).hasClass('action-close') && !$(this).hasClass('view-more-link') && jQuery(this).is(':focus')){
                        $('.cms-tiles-container-popup .overlayactive a').focus();
                        e.preventDefault();
                    }
                    if (e.keyCode == 9 && e.shiftKey != true && $(this).hasClass('view-more-link') && $(this).is(':focus')) {
                        $('.cms-tiles-container-popup .overlayactive .action-close').focus();
                        e.preventDefault();
                    }
                });
                $(document).on('blur', '.cms-tiles-container-popup .overlayactive .action-close', function(e){
                    $('.cms-tiles-container-popup .overlayactive a').focus();
                    e.preventDefault();
                });
                $(document).on('blur', '.cms-tiles-container-popup .overlayactive a', function(e){
                    $('.cms-tiles-container-popup .overlayactive .action-close').focus();
                    e.preventDefault();
                });
                $('.cms-tiles-container-popup .action-close').on('click', function (e) {
                    $(this).parents('.content-item').find('.overlay-content').css('display','none');
                    $(this).parents('.content-item').removeClass('overlayactive modal-popup _show');
                    $(this).parents('.content-item').find('.modals-overlay').remove();
                    $(this).parents('.content-item').find('.overlay-content a').attr('tabindex','-1');
                    $(this).parents('.content-item').find('.overlay-content .action-close').attr('tabindex','-1');
                    $(this).parents('.content-item').find('.overlay-content img').attr('tabindex','-1');
                    $('.cms-tiles-container-popup').removeClass('selected-section');
                    $('body').removeClass('modal-open-active');
                    $(this).parents('.content-item').find('.image-main').focus();
                });
                /*Close Popup on Escape*/
                var focusableElement  = $('.cms-tiles-container-popup .content-item');
                var parentElement  = $('.cms-tiles-container-popup li');
                $(document).on( 'keydown', function ( e ) {
                    if ( e.keyCode === 27 && focusableElement.hasClass('overlayactive')) {
                        focusableElement.removeClass('overlayactive modal-popup _show');
                        focusableElement.find('.modals-overlay').remove();
                        focusableElement.find('.overlay-content img').attr('tabindex','-1');
                        focusableElement.find('a').attr('tabindex','-1');
                        focusableElement.find('.action-close').attr('tabindex','-1');
                        $('body').removeClass('modal-open-active');
                        $('.cms-tiles-container-popup').removeClass('selected-section');
                        $('.cms-tiles-container-popup .active-tiles .image-main').focus();
                        focusableElement.find('.overlay-content').css('display','none');
                    }
                });
                /*Close Popup on Outside click*/
                $(document).mouseup(function(e) {
                    var container = $(".cms-tiles-container-popup .content-item.overlayactive .overlay-content");
                    // if the target of the click isn't the container nor a descendant of the container
                    if (!container.is(e.target) && container.has(e.target).length === 0) {
                        $('.cms-tiles-container-popup').removeClass('selected-section');
                        $('.cms-tiles-container-popup .content-item').removeClass('overlayactive modal-popup _show');
                        $('.cms-tiles-container-popup .content-item .modals-overlay').remove();
                        $('.cms-tiles-container-popup .content-item .overlay-content').css('display','none');
                        $('.cms-tiles-container-popup .overlay-content img').attr('tabindex','-1');
                        $('.cms-tiles-container-popup .overlay-content a').attr('tabindex','-1');
                        $('.cms-tiles-container-popup .overlay-content .action-close').attr('tabindex','-1');
                        $('body').removeClass('modal-open-active');
                        $('.cms-tiles-container-popup .active-tiles .image-main').focus();
                    }
                });
            },

            /*Project Showcase Slider*/
            projectShowcase :  function(){
                var $carousel = $(".project-showcase-container:not(.common-showcase-container)");
                $carousel.slick({
                    speed : 300,
                    infinite: true,
                    arrows: true,
                    slidesToShow: 3,
                    slidesToScroll: 1,
                    dots: false,
                    responsive: [
                        {
                            breakpoint: 767,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1,
                                infinite: true,
                                arrows: true,
                                dots: false
                            }
                        }
                    ]
                });
            },
            /*WE LIVE OUR VALUES Slider - Career and Get to know us Page*/
            projectShowcaseCareer :  function(){
                var $carousel = $(".common-showcase-container");
                $carousel.not('.slick-initialized').slick({
                    speed : 300,
                    infinite: true,
                    arrows: true,
                    slidesToShow: 4,
                    slidesToScroll: 4,
                    dots: false,
                    responsive: [
                        {
                            breakpoint: 767,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1,
                                infinite: true,
                                arrows: true,
                                dots: false
                            }
                        }
                    ]
                });
                if($.woport.getViewPortSize().width < 768) {
                    $carousel.find('.slick-current').attr('aria-current', true);
                }
                else{
                    $carousel.find('.slick-current').attr('aria-current', false);
                }
            },
            /*Project Showcase Slide Overlay*/
            projectShowcaseOverlay : function(){
                var selector1 = $('.project-showcase-container:not(.common-showcase-container)');
                selector1.find('.image-container .overlay-content').css('display','none');
                $('.project-showcase-container:not(.common-showcase-container) .image-container').on('click', function (e) {
                    e.preventDefault();

                    $('.project-showcase-container:not(.common-showcase-container) .overlay-content').css('display','none');
                    $(this).find('.overlay-content').attr('aria-hidden','false');
                    $(this).find('.overlay-content button,.overlay-content a').attr("tabindex","0");
                    $(this).find('.overlay-content button span,.overlay-content a span').attr('aria-hidden','false');

                    $(this).find('.overlay-content').css('display','block');
                    $(this).find('.overlay-content .action.primary').focus();
                    selector1.find('.image-container').attr("tabindex","0");
                    selector1.find(".slick-slide[tabindex='-1'] .image-container").attr("tabindex","-1");
                    $(this).find('img').attr("tabindex","0");
                    selector1.find(".image-container").removeClass('active-tiles');
                    $(this).addClass('active-tiles');
                    if($(this).find('.content-item').hasClass('overlayactive')){
                        $(this).attr("tabindex","0");
                        $(this).find('img').attr("tabindex","0");
                        $(this).find('.overlay-content').css('display','none');
                        $(this).find('.content-item').removeClass('overlayactive');
                        $('.tiles-container').removeClass('selected-section');
                    }
                    else{
                        $(this).attr("tabindex","-1");
                        $(this).find('img').attr("tabindex","-1");
                        $('.content-item').removeClass('overlayactive');
                        $(this).find('.content-item').addClass('overlayactive');
                        $('.tiles-container').addClass('selected-section');
                    }
                });
                /*Redirect on that anchor present in overlay*/
                $('body').on('mousedown', '.project-showcase-container .slick-slide a', function(e) {
                    e.preventDefault();
                    var url = $(this).attr('href');
                    window.open(url, '_blank');
                });
                $('body').on('keypress keydown', '.project-showcase-container .slick-slide a', function(e) {
                    if (e.keyCode == 13 || e.keyCode == 32) {
                        e.preventDefault();
                        var url = $(this).attr('href');
                        window.open(url, '_blank');
                    }
                });
            },
            /*WE LIVE OUR VALUES Slider - Career and Get to know us Page*/
            projectShowcaseOverlayCareer : function(){
                var selector1 = $('.common-showcase-container .image-container');
                selector1.find('.overlay-content').css('display','none');
                $('.common-showcase-container .image-container').on('click', function (e) {
                    e.preventDefault();
                    $('.common-showcase-container .overlay-content').css('display','none').attr('aria-hidden',true);
                    $('.common-showcase-container .overlay-content .heading').attr('aria-hidden',true);
                    $(this).find('.overlay-content').attr('aria-hidden','false');
                    $(this).find('.overlay-content a').attr("tabindex","0");
                    $(this).find('.overlay-content a span').attr('aria-hidden','false');
                    $(this).find('.overlay-content').css('display','block').attr('aria-hidden',false);
                    $(this).find('.overlay-content .heading').attr('aria-hidden',false);
                    $(this).find('.overlay-content a').focus();
                    selector1.attr("tabindex","0");
                    $(this).find('img').attr("tabindex","0");
                    selector1.removeClass('active-tiles');
                    $(this).addClass('active-tiles');
                    if($(this).find('.content-item').hasClass('overlayactive')){
                        $(this).attr("tabindex","0");
                        $(this).find('img').attr("tabindex","0");
                        $(this).find('.overlay-content').css('display','none').attr('aria-hidden',true);
                        $(this).find('.overlay-content .heading').attr('aria-hidden',true);
                        $(this).find('.content-item').removeClass('overlayactive');
                        $('.tiles-container').removeClass('selected-section');
                    }
                    else{
                        $(this).attr("tabindex","-1");
                        $(this).find('img').attr("tabindex","-1");
                        $('.content-item').removeClass('overlayactive');
                        $(this).find('.content-item').addClass('overlayactive');
                        $('.tiles-container').addClass('selected-section');
                    }
                });
            },
            /*Slick slider for Related, Up Sell and Cross Sell Sections*/
            crossSellsSlider :  function(){
                var $carousel = $(".products-crosssell .product-items-container, .products-upsell .product-items-container, #products-related-section");
                $carousel.not('.slick-initialized').slick({
                    speed : 300,
                    slidesToShow: 5,
                    slidesToScroll: 1,
                    infinite: true,
                    arrows: true,
                    dots: false,
                    responsive: [
                        {
                            breakpoint: 1100,
                            settings: {
                                slidesToShow: 4,
                                infinite: true,
                            }
                        },
                        {
                            breakpoint: 1023,
                            settings: {
                                slidesToShow: 3,
                                slidesToScroll: 1
                            }
                        },
                        {
                            breakpoint: 767,
                            settings: 'unslick'
                        },
                    ]
                });
            },
            /*See Detail Changes Mini cart Section*/
            showHideDetail: function () {
                $(document).on('click','.show-detail',function(){
                    $(this).closest(".product-item-details").find(".see-detail-info").addClass('active').show();
                    $(this).removeClass("show-detail").addClass("hide-detail").attr("aria-expanded","true");
                });

                $(document).on('click','.hide-detail',function(){
                    $(this).closest(".product-item-details").find(".see-detail-info").removeClass('active').hide();
                    $(this).removeClass("hide-detail").addClass("show-detail").attr("aria-expanded","false");
                });
            },
            /*PDP Page Media Section*/
            moveSocialMediaSection: function () {
                if ($('body').hasClass("catalog-product-view")) {
                    var socialElement = $(".mp_social_share_inline_under_cart").detach();
                    $(".catalog-product-view .social-icons-container .other-social-icon").append(socialElement);
                }

                if ($('body').hasClass('catalog-product-view')) {
                    setTimeout(function () {
                        $('.product.data.items .title.active').attr('aria-selected','true');
                    }, 100);
                }
            },

            /*Slick Slider Multi Row Slider*/
            multislider :  function(){
                var $carousel = $(".multislider-container #multislider");
                $carousel.slick({
                    speed : 300,
                    rows: 2,
                    slidesToShow: 3,
                    slidesToScroll: 3,
                    infinite: true,
                    arrows: true,
                    dots: true,
                    focusOnSelect: true,
                    responsive: [
                        {
                            breakpoint: 767,
                            settings: {
                                slidesToShow: 2,
                                slidesToScroll: 2
                            }
                        }
                    ]
                });
            },
            /*PDP Page Short and Long Description Read Less More*/
            pdpTabShowMoreDescription: function() {
                if($.woport.getViewPortSize().width < 768){
                    $(' .catalog-product-view .product.info.detailed .item.title').attr('tabindex','-1');
                    $(' .catalog-product-view .product.info.detailed .data.item').removeAttr('role');
                    $(' .catalog-product-view .product.info.detailed .item.content').removeAttr('aria-hidden');
                    $(document).on('click','.catalog-product-view .data.item.title a',function () {
                        $(' .catalog-product-view .product.info.detailed .item.content').removeAttr('aria-hidden');
                    });
                }
                else{
                    $(' .catalog-product-view .product.info.detailed .item.title').attr('tabindex','0');
                    $(' .catalog-product-view .product.info.detailed .item.title').attr('role','tab');
                    $(' .catalog-product-view .product.info.detailed .item.content').attr('role','tabpanel');
                }
            },
            /*PDP Page Click Functionality*/
            readMoreClick: function () {
                $(document).on("click", ".read-link-container .showLessMore", function(e) {
                    var thisElement = $(this);
                    var readMoreLessElement = thisElement.parents('#long-description,#short-description').find(".truncate-text");
                    var textElement = ".truncate-text";

                    if (thisElement.hasClass("read-less")) {
                        readMoreLessElement.prev(textElement).show();
                        readMoreLessElement.hide();
                        $('.truncate-text:first-child').show();
                        thisElement.removeClass("read-less").addClass("read-more").text("Read More").attr({'aria-label':"Read More",'aria-expanded':'false'}).focus();
                    } else {
                        readMoreLessElement.hide();
                        readMoreLessElement.next(textElement).show();
                        thisElement.removeClass("read-more").addClass("read-less").text("Read Less").attr({'aria-label':"Read Less",'aria-expanded':'true'}).focus();
                    }
                    return false;
                    e.preventDefault();
                });
            },
           /* My Catalog Page WCAG Changes*/
            priceMultiplier: function () {
                /*Close On Escape*/
                $(document).on( 'keydown', function ( e ) {
                    if ( e.keyCode === 27 ) {
                        if( $(".mycatalog-index-pages .price-dialog").css('display') == 'block' ) {
                            $(".mycatalog-index-pages .price-dialog").hide();
                            $(".mycatalog-index-pages #price_multiplier_option").focus().attr('aria-expanded','false');
                        }
                    }
                });
                /*Focus Trap My Catalog Page*/
                $('.mycatalog-index-pages .cancel-price').on('keydown', function (e) {
                    let isTabPressed = (e.key === 'Tab' || e.keyCode === 9);
                    if (e.shiftKey) {
                        return;
                    } else{
                        $('.mycatalog-index-pages #price_multiplier').focus();
                        e.preventDefault();
                    }
                });
                $('.mycatalog-index-pages #price_multiplier').on('keydown', function (e) {
                    let isTabPressed = (e.key === 'Tab' || e.keyCode === 9);
                    if (e.shiftKey){
                        $('.mycatalog-index-pages .cancel-price').focus();
                        e.preventDefault();
                    } else{
                        return;
                    }
                });
            },
            /*Focus Trap My Catalog Page*/
            catalogActionClick: function () {
                $('#top-submenu .delete-catalog').on('keydown', function (e) {
                    var isTabPressed = (e.key === 'Tab' || e.keyCode === 9);
                    if (!isTabPressed) {
                        return;
                    }
                    if (!e.shiftKey && e.keyCode == 9) {
                        $('.email-catalog').focus();
                        e.preventDefault();
                    } else {
                        return;
                    }
                });

                $('#top-submenu .email-catalog').on('keydown', function (e) {
                    var isTabPressed = (e.key === 'Tab' || e.keyCode === 9);
                    if (!isTabPressed) {
                        return;
                    }
                    if (e.shiftKey && e.keyCode == 9) {
                        $('.delete-catalog').focus();
                        e.preventDefault();
                    } else {
                        return;
                    }
                });

                $(document).on( 'keydown', function ( e ) {
                    if ( e.keyCode === 27 ) {
                        if( $(".top-slider-container #top-submenu").css('display') == 'block' ) {
                            $(".top-slider-container #top-submenu").hide();
                            $("#catalog_action_button").focus();
                        }
                    }
                });
            },
            /*Category Page Banner Section*/
            addRemoveCMSContent: function () {
                if($(".catalog-category-view .category-view .page-main .category-image").length){
                    $(".catalog-category-view .category-view .page-title-wrapper").addClass("show-background");
                }
                else{
                    $(".catalog-category-view .category-view .page-title-wrapper").addClass("hide-background");
                }
                if($(".catalog-category-view .brand-schemes-container").length){
                    $(".catalog-category-view .category-view .page-title-wrapper").addClass("no-label");
                    $(".catalog-category-view .category-view .page-title-wrapper h1").addClass("no-label");

                    $(".catalog-category-view").on("DOMNodeInserted",".category-view",function(event){
                        setTimeout(function() {
                            $(".catalog-category-view .category-view .page-title-wrapper").addClass("no-label");
                            $(".catalog-category-view .category-view .page-title-wrapper h1").addClass("no-label");
                        },1000);
                    });
                }

                setTimeout(function(){
                    $(".ammenu-main-container .ammenu-submenu-container .tab-title.ui-tabs-anchor").removeAttr("role");
                }, 2500);
            },
            /*Menu, Dropdown related WCAG changes*/
            menuKeyBoardAccessible: function () {
                /*Dropdown changes for up and down key only*/
                $(document).on("keypress keydown", "[data-target='dropdown'] a, " +
                    "[data-target='dropdown'] span[role='button'], " +
                    "[data-target='dropdown'] .focus-button", function(e) {
                    // Listen for the up, down, left and right arrow keys, otherwise, end here
                    if ([37,38,39,40].indexOf(e.keyCode) == -1) {
                        return;
                    }

                    // Store the reference to our top level link
                    var links = $('.header.links').find('li');
                    var selected = $(".selected");

                    switch(e.keyCode) {
                        case 40: // down arrow
                            e.preventDefault();
                            e.stopPropagation();
                            links.removeClass("selected");
                            if (selected.next().length == 0) {
                                selected.siblings().first().addClass("selected").find("a, span[role='button'],.focus-button").filter(':visible').first().focus();
                            } else {
                                selected.next().addClass("selected").find("a, span[role='button'],.focus-button").filter(':visible').first().focus();
                            }
                            break;
                        case 38: /// up arrow
                            // Find the nested element that acts as the menu
                            e.preventDefault();
                            e.stopPropagation();
                            links.removeClass("selected");
                            if (selected.prev().length == 0) {
                                selected.siblings().last().addClass("selected").find("a, span[role='button'], .focus-button").filter(':visible').first().focus();
                            }else{
                                selected.prev().addClass("selected").find("a, span[role='button'],.focus-button").filter(':visible').first().focus();
                            }

                            break;
                    }
                });
                /*On reaching last li of dropdown move focus to first*/
                $(document).on("keydown", "[data-target='dropdown'] li:last-child a, " +
                    "[data-target='dropdown'] li:last-child span[role='button']", function(e) {
                    var code = (e.keyCode ? e.keyCode : e.which);
                    var esc = $.Event("keydown", { keyCode: 27 });
                    if(code == TAB_KEY){
                        $(this).parents("[data-target='dropdown']").siblings("[data-toggle='dropdown']").focus();
                    }
                });
                /*On closing dropdown move focus to clicked element*/
                $(document).on("keydown", "[data-target='dropdown']", function(e) {
                    var code = (e.keyCode ? e.keyCode : e.which);
                    if (code == ESC_CODE) {
                        $(this).siblings("[data-toggle='dropdown']").focus();
                    }
                });
                /*Mobile Toggle Hide content from reading when toggle is not active*/
                $(document).on('click','.ammenu-menu-toggle', function (e) {
                    $(this).attr('aria-expanded',"true");
                    $(".ammenu-tabs-container .ammenu-title.active .ammenu-link").focus();
                    if($.woport.getViewPortSize().width < 1025){
                        if ($('body').hasClass('-am-noscroll')) {
                            $('.sections.nav-sections').addClass('hide-content').attr('aria-hidden','true');
                            $('.page-main, .page-footer,.block.block-search,.openBtn,.logo, .header.content > [data-content-type="row"], .minicart-wrapper, .page-header > .panel.wrapper, .copyright, body > .modals-wrapper, #maincontent').addClass('hide-content').attr('aria-hidden','true');
                        }
                    }else{
                        $('.sections.nav-sections').removeClass('hide-content').removeAttr('aria-hidden');
                        $('.page-main, .page-footer,.block.block-search,.openBtn,.logo, .header.content > [data-content-type="row"], .minicart-wrapper, .page-header > .panel.wrapper, .copyright, body > .modals-wrapper, #maincontent').removeClass('hide-content').removeAttr('aria-hidden');
                    }
                });
                $(document).mouseup(function(e) {
                    var container = $(".ammenu-nav-sections.nav-sections");

                    // if the target of the click isn't the container nor a descendant of the container
                    if (!container.is(e.target) && container.has(e.target).length === 0 && $(this).hasClass("-opened"))
                    {
                        container.hide();
                        $(".ammenu-menu-toggle").attr('aria-expanded',"false");
                    }
                    $(".ammenu-menu-toggle").attr('aria-expanded',"false");
                    $('.sections.nav-sections').removeClass('hide-content').removeAttr('aria-hidden');
                    $('.page-main, .page-footer,.block.block-search,.logo,.openBtn, .header.content > [data-content-type="row"], .minicart-wrapper, .page-header > .panel.wrapper, .copyright, body > .modals-wrapper, #maincontent').removeClass('hide-content').removeAttr('aria-hidden');

                });
                /*Close Mobile Menu Toggle on Escape Key*/
                $(document).on('keydown', function (e) {
                    var container = $(".ammenu-nav-sections");
                    if ( e.keyCode == 27 && container.hasClass('-opened')) {
                        container.removeClass('-opened');
                        $(".ammenu-menu-overlay").hide();
                        $('.sections.nav-sections').removeClass('hide-content').removeAttr('aria-hidden');
                        $('.page-main, .page-footer,.block.block-search,.openBtn,.logo, .header.content > [data-content-type="row"], .minicart-wrapper, .page-header > .panel.wrapper, .copyright, body > .modals-wrapper, #maincontent').removeClass('hide-content').removeAttr('aria-hidden');
                        $(".ammenu-menu-toggle").removeClass('-active').attr('aria-expanded','false').focus();
                    }
                });

                /*Account Sidebar Dropdown Focus Trapping in mobile view*/
                $(document).on('keydown', '.block-collapsible-nav .items', function(e){
                    if(e.keyCode == 9  && e.shiftKey == true && $(this).children('.item:nth-child(2)').children().is(':focus') && $.woport.getViewPortSize().width < 768){
                        $(this).children('.item:last-child').children().focus();
                        e.preventDefault();
                    }
                    else if(e.keyCode == 9 && e.shiftKey != true && $(this).children('.item:last-child').children().is(':focus') && $.woport.getViewPortSize().width < 768 ){
                        $(this).children('.item:nth-child(2)').children().focus();
                        e.preventDefault();
                    }
                });
                /*Account Sidebar Dropdown Close on Escape in mobile view*/
                $(document).keyup(function(e) {
                    if ($(window).width() < 768 && e.keyCode == 27 && $('.block-collapsible-nav-title').hasClass('active')) {
                        $('.sidebar .block-collapsible-nav-content, .sidebar .title.block-collapsible-nav-title').removeClass('active');
                        $('.title.block-collapsible-nav-title').focus();
                        if ($('.sidebar .block-collapsible-nav-title').attr("aria-expanded", "true")) {
                            $('.sidebar .block-collapsible-nav-title').attr("aria-expanded", "false");
                        }
                    }
                });
            },
            /*Current Account Page Sidebar dropdown and WCAG changes*/
            selectCurrentAccountPage: function () {
                if($.woport.getViewPortSize().width < 768){
                    var currentPage = $(".block-collapsible-nav-content").find(".current").find("strong").html();
                    $(".block-collapsible-nav-title > strong").text(currentPage);
                    $('.sidebar .block-collapsible-nav-title').attr("role", "button");
                    $('.sidebar .block-collapsible-nav-title').attr({"aria-expanded":"false","tabindex":"0"});
                    $('.columns .sidebar.sidebar-main').insertBefore('.columns .column.main');
                    $('.sidebar .block-collapsible-nav-content .current strong').attr({"tabindex":"0","aria-current":"page"});
                }else {
                    $('.columns .sidebar.sidebar-main').insertAfter('.columns .column.main');
                    $('.sidebar .block-collapsible-nav-title').removeAttr("tabindex");
                    $('.sidebar .block-collapsible-nav-content .current strong').removeAttr("tabindex").attr('aria-current','page');
                }
                $(document).on("click", ".sidebar .block-collapsible-nav-title", function() {
                    $('.sidebar .block-collapsible-nav-title').attr("aria-expanded", "false");
                    $('.sidebar .block-collapsible-nav-title').focus();
                });
                $(document).on("click", ".sidebar .block-collapsible-nav-title.active", function() {
                    $('.sidebar .block-collapsible-nav-title').attr("aria-expanded", "true");
                    $('.sidebar .block-collapsible-nav-title').focus();
                });
            },
            /*Redirecting to PDP on non anchor image and having class product-item-link on adjacent available*/
            redirectToProduct: function () {
                $(document).on('click', '.product-item-photo', function () {
                    if ($(this).parents('.products-grid').hasClass('wishlist')) {
                        var nameElement = $(this).siblings('.product-item-name');
                        if (typeof nameElement !== 'undefined') {
                            var linkElement = nameElement.children('.product-item-link');
                            if (typeof linkElement !== 'undefined') {
                                window.location.href = linkElement.attr("href");
                            }
                        }
                    }
                });
            },
            /*Mega Menu Navigation WCAG changes for tab, up and down*/
            mainMenuWcag: function () {
                $(document).on('focus', '.-desktop .ammenu-main-container ul > li > a', function(e){
                    $(this).parent('li').siblings('li').children('.ammenu-submenu').removeClass('opened').hide();
                    $(this).parent('li').siblings('li').removeClass('active-submenu');
                    $(this).parent('li').siblings('li').children('a').removeClass('active');
                    $(this).parent('li').siblings('li').children('.ammenu-link').attr('aria-expanded','false');
                    $(this).parent('li').siblings('li').children('.ammenu-submenu').attr('aria-expanded','false');
                    e.preventDefault();
                });

                /*On Esc close menu*/
                $(document).keydown(function(e) {
                    var $parentMenu = $('.-desktop .ammenu-submenu.opened');
                    var mainMenu = $('.-desktop .ammenu-item');
                    if (e.keyCode == ESC_CODE && $parentMenu.filter(':visible')){
                        $parentMenu.parent("li").find('a.-parent').first().focus();
                        mainMenu.siblings('li').children('.ammenu-submenu').removeClass('opened').hide();
                        mainMenu.siblings('li').children('.ammenu-link').attr('aria-expanded','false');
                        mainMenu.siblings('li').children('.ammenu-submenu').attr('aria-expanded','false');
                        e.preventDefault();
                        e.stopPropagation();
                    }
                });

                /* Menu keydown code */
                $(document).on('keydown', '.-desktop .ammenu-main-container a', function(e){
                    var $parentMenu = $('.ammenu-submenu.opened');
                    //arrow left
                    if (e.keyCode == 37) {
                        //check if a li is focused, if not it focus the first one
                        if ($(this).is(':focus') && $(this).parent('li').prev('li').length > 0) {
                            $(this).parent('li').prev('li').children('a').focus();
                        } else {
                            $('.-desktop .ammenu-main-container > ul:first-child > li:first-child > a').focus();
                        }
                    }
                    //arrow right
                    else if (e.keyCode == 39) {
                        if ($(this).is(':focus') && $(this).parent('li').next('li').length > 0) {
                            $(this).parent('li').next('li').children('a').focus();
                        } else {
                            $('.-desktop .ammenu-main-container > ul:first-child > li:last-child > a').focus();
                        }
                    }
                    //arrow up
                    else if (e.keyCode == 38) {
                        if ($(this).is(':focus') && ($(this).parent('li').prev('li').length > 0)  && $.woport.getViewPortSize().width > 1024) {
                            $(this).parent('li').prev('li').children('a').focus();
                        } else {
                            $(this).parents('.ammenu-item').children('.ammenu-submenu').attr('aria-expanded', 'false');
                            $(this).parent('li').parent('.ammenu-submenu').parent('.ammenu-item').children('a').focus();
                            $(this).parents('.ammenu-item').children('.ammenu-link').attr('aria-expanded', 'false').focus();
                            $(this).parents('.ammenu-item').siblings('li').children('.ammenu-submenu').attr('aria-expanded', 'false');
                        }
                        e.preventDefault();
                    }
                    //arrow down
                    else if (e.keyCode == 40) {
                        if ($(this).is(':focus') && ($(this).parent('li').next('li').length > -1) && $(this).parent('li.ammenu-item').length < 0  && $.woport.getViewPortSize().width > 1024) {
                            $(this).parent('li').next('li').children('a').focus();
                        }else if ($(this).is(':focus') && ($(this).parent('li').next('li').length > -1) && $(this).parent('li').hasClass('ammenu-item') &&
                            $.woport.getViewPortSize().width > 1024) {
                            $(this).parents('.ammenu-item').children('.ammenu-submenu').addClass('opened').show();
                            $(this).parents('.ammenu-item').children('.ammenu-link').attr('aria-expanded', 'true');
                            $(this).parents('.ammenu-item').children('.ammenu-submenu').attr('aria-expanded', 'true');
                            $(this).parents('.ammenu-item').siblings('li').children('.ammenu-submenu').attr('aria-expanded', 'false');
                        }
                        else {
                            $(this).focus();
                        }
                        e.preventDefault();
                        e.stopPropagation();
                    }

                    $(document).on('keydown', '.-desktop .ammenu-submenu .tabs-content .custom-menu-heading, .-desktop .custom-menu-wrapper.custom-menu-cat-images li:first-child', function(e){
                        if (e.keyCode == 38){
                            $(this).parents('.ammenu-item').children('.ammenu-submenu').attr('aria-expanded','false');
                            $(this).parent('li').parent('.ammenu-submenu').parent('.ammenu-item').children('a').focus();
                            $(this).parents('.ammenu-item').children('.ammenu-link').attr('aria-expanded','false').focus();
                            $(this).parents('.ammenu-item').siblings('li').children('.ammenu-submenu').attr('aria-expanded','false');
                            $('.ammenu-submenu').hide().removeClass('opened');
                            $(this).focus();
                        }
                    });
                    $(document).on('blur', '.-desktop .ammenu-item:last-child .custom-menu-wrapper li:last-child a', function(e){
                        $(this).parents('.ammenu-item').children('.ammenu-submenu').attr('aria-expanded','false');
                        $(this).parent('li').parent('.ammenu-submenu').parent('.ammenu-item').children('a').focus();
                        $(this).parents('.ammenu-item').children('.ammenu-link').attr('aria-expanded','false').focus();
                        $(this).parents('.ammenu-item').siblings('li').children('.ammenu-submenu').attr('aria-expanded','false');
                        $('.ammenu-submenu').hide().removeClass('opened');
                        $(".action.showcart").focus();
                    });
                });
                $(document).on('click keydown','.-mobile .ammenu-toggle',function(e){
                    $(this).attr('aria-expanded',true);
                    $(this).addClass("active");
                });
                $(document).on('click keydown','.-mobile .ammenu-toggle.active',function(e){
                    $(this).attr('aria-expanded',false);
                    $(this).removeClass("active");
                });
                $(document).on('click','.-mobile .ammenu-arrow',function(e){
                    $(this).attr('aria-expanded',true);
                    $(this).addClass("active").parent('.category-item').find('.ammenu-item.-child:not(.-col-4)').show();
                });
                $(document).on('click','.-mobile .ammenu-arrow.active',function(e){
                    $(this).attr('aria-expanded',false);
                    $(this).removeClass("active").parent('.category-item').find('.ammenu-item.-child:not(.-col-4)').hide();
                });
                $(document).on('keydown','.-mobile .ammenu-arrow',function(e){
                    if(e.keyCode== 13){
                        $(this).attr('aria-expanded',true);
                        $(this).addClass("active").parent('.category-item').find('.ammenu-item.-child:not(.-col-4)').show();
                    }
                });
                $(document).on('keydown','.-mobile .ammenu-arrow.active',function(e){
                    if(e.keyCode== 13){
                        $(this).attr('aria-expanded',false);
                        $(this).removeClass("active").parent('.category-item').find('.ammenu-item.-child:not(.-col-4)').hide();
                    }
                });
            },
            /*Listing Page Banner Section Read Less More*/
            readLessMoreListingPage: function () {
                if ($(".category-view .category-description [data-element=inner]").html() == "") {
                    $(".category-view .category-description").remove();
                }
                $(document).on("click", ".show-lessmore-link", function(e) {
                    var thisElement = $(this);
                    var readMoreLessElement = thisElement.parents('.brand-inner-container').find(".truncate-text");
                    var textElement = ".truncate-text";

                    if (thisElement.hasClass("read_less")) {
                        readMoreLessElement.prev(textElement).show();
                        readMoreLessElement.hide();
                        $('.truncate-text:first-child').show();
                        thisElement.removeClass("read_less").addClass("read_more").text("Show More").attr({'aria-label':"Show More",'aria-expanded':'false'}).focus();
                    } else {
                        readMoreLessElement.hide();
                        readMoreLessElement.next(textElement).show();
                        thisElement.removeClass("read_more").addClass("read_less").text("Show Less").attr({'aria-label':"Show Less",'aria-expanded':'true'}).focus();
                    }
                    return false;

                    e.preventDefault();
                });
            },
            /*Date Picker Focus and label changes on Order and Shipped order page*/
            uiDatePickerChanges: function () {
                /*Label Changes*/
                $( "<label class='no-label' for='datepicker-month'>Select Month</label>" ).insertBefore( ".ui-datepicker-month" );
                $( "<label class='no-label' for='datepicker-year'>Select Year</label>" ).insertBefore( ".ui-datepicker-year" );
                $(".ui-datepicker-month").attr("id", "datepicker-month" );
                $(".ui-datepicker-year").attr("id", "datepicker-year" );
                $(".ui-datepicker-next, .ui-datepicker-prev").removeAttr("title");
                /*Open Datepicker and Set Focus*/
                $('.order-date-from .ui-datepicker-trigger').on('click',function(){
                    setTimeout(function(){
                        $('.ui-datepicker-prev').focus();
                        $('#ui-datepicker-div').addClass('date-range-order_date-from');
                    },200);
                });
                /*Close Datepicker on close icon*/
                $(document).on('click','.date-range-order_date-from .ui-datepicker-close',function(){
                    setTimeout(function(){
                        $('.order-date-from .ui-datepicker-trigger').focus();
                        $('#ui-datepicker-div').removeClass('date-range-order_date-from');
                    },500);
                });
                /*Open Datepicker and Set Focus*/
                $('.order-date-to .ui-datepicker-trigger').on('click',function(){
                    setTimeout(function(){
                        $('.ui-datepicker-prev').focus();
                        $('#ui-datepicker-div').addClass('date-range-order_date-to');
                    },200);
                });
                /*Close Datepicker on close icon*/
                $(document).on('click','.date-range-order_date-to .ui-datepicker-close',function(){
                    setTimeout(function(){
                        $('.order-date-to .ui-datepicker-trigger').focus();
                        $('#ui-datepicker-div').removeClass('date-range-order_date-to');
                    },500);
                });
                /*Close Datepicker on Escape key*/
                $(document).on('keydown',function (e) {
                   if(e.keyCode == 27 && $('.ui-datepicker.date-range-order_date-from').css('display') == 'block'){
                       $('.ui-datepicker.date-range-order_date-from').hide();
                       $('.order-date-from input').focus();
                      $('#ui-datepicker-div').removeClass('date-range-order_date-from');
                   }
                    if(e.keyCode == 27 && $('.ui-datepicker.date-range-order_date-to').css('display') == 'block'){
                        $('.ui-datepicker.date-range-order_date-to').hide();
                        $('.order-date-to input').focus();
                        $('#ui-datepicker-div').removeClass('date-range-order_date-to');
                    }
                });
            },
            /*Weighted tooltip WCAG changes*/
            weightedTooltip: function () {
               /* Open and Close tooltip on click*/
                $(document).on('click','.product-info-main:not(.pz-info-customize) .hint,body:not(.catalog-product-view) .hint,.minicart-wrapper .hint',function(){
                    $(this).parents('.tooltip-container').removeClass('remove-item');
                    $(this).parents('.tooltip-container').find('.close-icon').focus();
                    $(this).addClass('active');
                    $(this).parents('.tooltip-container').addClass('active');
                    $(this).attr('aria-expanded','true');
                });
                $(document).on('keydown','.product-info-main:not(.pz-info-customize) .hint,body:not(.catalog-product-view) .hint,.minicart-wrapper .hint',function(e){
                    if (e.keyCode == 13 || e.keyCode == 32){
                        $(this).parents('.tooltip-container').find('.close-icon').focus();
                    }
                });
                /*Close Tooltip when double click on weighted label*/
                $(document).on('click','.product-info-main:not(.pz-info-customize) .hint.active,body:not(.catalog-product-view) .hint.active,.minicart-wrapper .hint.active',function(){
                    $(this).removeClass('active');
                    $(this).parents('.tooltip-container').removeClass('active');
                    $(this).attr('aria-expanded','false');
                    $(this).focus();
                    $(this).parents('.tooltip-container').addClass('remove-item');
                });
                /*Close tooltip on outside click*/
                $(document).mouseup(function(e) {
                    var container = $(".tooltip-container");
                    var containerActive = $(".tooltip-container.active");
                    if ((!container.is(e.target) && container.has(e.target).length === 0) || (!containerActive.is(e.target) && containerActive.has(e.target).length === 0)) {
                        container.removeClass('active');
                        container.find('.hint').removeClass('active');
                    }
                });
                /*Close tooltip on escape*/
                $(document).on('keydown','.tooltip-container',function(e){
                    if (e.keyCode == 27 && $('.tooltip-container').hasClass('active')) {
                        $(this).removeClass('active');
                        $(this).find('.hint').removeClass('active');
                        $(this).find('.hint').attr('aria-expanded','false').focus();
                        $(this).addClass('remove-item');
                    }
                });
                /*Hide Class on mousenter*/
                $( ".tooltip-container").mouseenter(function() {
                    $(this).removeClass('remove-item');
                });
                /*close tooltip on click on close icons*/
                $(document).on('click','.product-info-main:not(.pz-info-customize) .pz-tooltip-content .close-icon,body:not(.catalog-product-view) .pz-tooltip-content .close-icon,.minicart-wrapper .pz-tooltip-content .close-icon',function(){
                    $(this).parents('.tooltip-container').removeClass('active');
                    $(this).parents('.tooltip-container').find('.hint').removeClass('active');
                    $(this).parents('.tooltip-container').find('.hint').attr('aria-expanded','false').focus();
                    $(this).parents('.tooltip-container').addClass('remove-item');
                });
                /*Trap Focus in Tooltip*/
                $(document).on('keydown', '.product-info-main:not(.pz-info-customize) .tooltip-container .pz-tooltip-content a,body:not(.catalog-product-view) .tooltip-container .pz-tooltip-content a,.minicart-wrapper .tooltip-container .pz-tooltip-content a', function(e){
                    if (e.keyCode == 9 && e.shiftKey == true && $(this).parents('.tooltip-container').hasClass('active') ){
                        if($(this).parent().parent().children('span:last-child').length && $(this).is(':focus') && !$(this).hasClass('close-icon')){
                            $(this).parent().parent().children('a:first-child').focus();
                        }
                        if($(this).parent().children('span:last-child').length && $(this).is(':focus') && $(this).hasClass('close-icon')){
                            $(this).parent().find('span > a').focus();
                        }
                        e.preventDefault();
                    }
                    if (e.keyCode == 9 && e.shiftKey != true && $(this).parents('.tooltip-container').hasClass('active') ){
                        if($(this).parent().parent().children('span:last-child').length && $(this).is(':focus') && !$(this).hasClass('close-icon')){
                            $(this).parent().parent().children('a:first-child').focus();
                        }
                        if($(this).parent().children('span:last-child').length && $(this).is(':focus') && $(this).hasClass('close-icon')){
                            $(this).parent().find('span > a').focus();
                        }
                        e.preventDefault();
                    }
                });
            },
            specificationTooltip: function () {
                $(document).on('click', '.frame-container .placeholder-img', function (e) {
                    $(this).parent(".frame-container").find(".frame-hover-container").addClass("active").removeClass('remove-item');
                    $(this).parent(".frame-container").addClass("active");
                    $(this).parents(".configuration-swatches-block").siblings().find(".frame-container").removeClass("active");
                    $(this).parents(".configuration-swatches-block").siblings().find(".frame-hover-container").removeClass("active");
                    $(this).parents(".configuration-swatches-block").siblings().find('.placeholder-img').removeClass("active");
                    $(this).addClass('active');
                    $(this).parent(".frame-container").find(".close-icon").focus();
                });
                $(document).on('click', '.frame-container .placeholder-img.active', function (e) {
                    $(this).parent(".frame-container").find(".frame-hover-container").removeClass("active").addClass('remove-item');
                    $(this).parent(".frame-container").removeClass("active");
                    $(this).removeClass('active');
                });
                $(document).on('click', '.frame-container .close-icon', function (e) {
                    $(this).parents(".frame-container").find(".frame-hover-container").removeClass("active").addClass('remove-item');
                    $(this).parents(".frame-container").find('.placeholder-img').removeClass('active');
                    $(this).parents(".frame-container").removeClass("active");
                });
                $(document).on('keydown', function (e) {
                    if(e.keyCode == 27 && $(".frame-container .frame-hover-container").hasClass('active')){
                        $(".frame-container .frame-hover-container").removeClass("active").addClass('remove-item');
                        $(".frame-container .placeholder-img").removeClass('active');
                        $(".frame-container").removeClass("active");
                    }
                });
                $(document).mouseup(function(e) {
                    var container = $(".frame-container .placeholder-img");
                    var containerActive = $(".frame-container .placeholder-img.active");
                    if ((!container.is(e.target) && container.has(e.target).length === 0) || (!containerActive.is(e.target) && containerActive.has(e.target).length === 0)) {
                        container.removeClass('active');
                        container.parent('.frame-container').removeClass('active');
                        container.parent('.frame-hover-container').removeClass('active');
                    }
                });
                $(".frame-container .placeholder-img").mouseenter(function() {
                    $(this).parent('.frame-container').find('.frame-hover-container').removeClass('remove-item');
                });
            }
        };

        /*Sticky Header*/
        function stickyHeader(){
                var stickyContainer = $('.page-header');
                var headerTopElement = $('.page-header');
                var referenceTop = $(headerTopElement).outerHeight(true);
                var footerTop = $('.page-footer').offset().top - 90;
                var pageHeader = $('.page-header').outerHeight(true);
                if($(window).scrollTop() > referenceTop){
                    $('.page-header').addClass('sticky-header');
                    $('#maincontent.page-main').css({'margin-top': pageHeader});
                    $('.page-header > div[data-content-type="row"]').addClass('hidden');
                    if($.woport.getViewPortSize().width < 767) {
                        $('.page-header.sticky-header').removeClass('actives').removeAttr('style');
                    }

                } else {
                    $('.page-header').removeClass('sticky-header');
                    $('.page-header > div[data-content-type="row"]').removeClass('hidden');
                    $('.page-main').removeAttr('style');
                }
            }
        /*Listing page Read Less More*/
        function readLessMorePLP(){
            var showChar = 255;
            $(".listing-description").each(function() {
                var content = $(this).html();
                if (content.length > showChar) {
                    var firstElement = content.substr(0, showChar);
                    var totalElement = content;
                    var readLessLink = "<div class='show-more-container read-link-containers'><a role='button' aria-label='Show More' aria-expanded='false' aria-atomic='true' tabindex='0' class='read_more show-lessmore-link'>Show More</a></div>";
                    var html = '<div class="truncate-text" style="display:block">' + firstElement +
                        '</div><div class="truncate-text" style="display:none">' +
                        totalElement+
                        '</div>'+readLessLink;
                    $(this).html(html);
                }
            });
        };
        /*PDP Page Tab Section Read Less More for Mobile View*/
        function readLessMorePDP() {
            if(woObj.getViewPortSize().width < 768) {
                var maxCharLimit = 140;
                var readMoreTxt = " Read More";
                var readLessTxt = " Read Less";
                var fetchElement = $(".catalog-product-view #long-description,.catalog-product-view #short-description");
                fetchElement.each(function () {
                    var fetchData = $(this).html();
                    var fetchDataText = $(this).text();
                    if (fetchData.length > maxCharLimit) {
                        var firstStr = fetchData.substring(0, maxCharLimit);
                        var totalElement = fetchData;
                        var ellipsisDot = '...';
                        var strToAdd = "<div class='truncate-text firstElem' style='display:block'>" + firstStr +ellipsisDot +
                            "</div><div class='truncate-text totalElem' style='display:none'> " + totalElement +
                            "</div><div class='read-link-container'><a role='button' aria-expanded='false' aria-label='Read More' aria-atomic='true' tabindex='0' class='showLessMore read-more'>Read More</a></div>";
                        $(this).html(strToAdd);
                    }
                });
            }
        };

        $(document).ready(function() {
            woObj = $.woport;
            woObj.init();
        });
        $(window).on("scroll", function () {
            stickyHeader();
        });
        /*Read Less More Changes on Listing page*/
        $('.catalog-category-view').on('ajaxComplete',function( event, request, settings ) {
            if ($(".category-view .category-description [data-element=inner]").html() == "") {
                $(".category-view .category-description").remove();
            }
            var fetchElement = $(".brand-inner-container .listing-description");
            fetchElement.each(function () {
                if($(this).parents('.brand-inner-container').find('.show-lessmore-link').length == 0){
                    readLessMorePLP();
                }
            });
        });
        /*Add View Details dropdown on Shopping Cart page for mobile view*/
        $('.checkout-cart-index').on('ajaxComplete',function( event, request, settings ) {
            var parentElement = $(".cart.table-wrapper .product-item-details .item-options");
            if($('.cart.table-wrapper .product-item-details .show-hide-detail-cart').length == 0){
                $("<a class='show-hide-detail-cart show-detail-cart' tabindex='0' role='button' aria-expanded='false'>View Details</a>").insertBefore(parentElement);
            }
            if($.woport.getViewPortSize().width < 768) {
                $(document).on('click', '.show-detail-cart', function () {
                    $(this).parent(".product-item-details").find(".item-options").addClass('active').show();
                    $(this).removeClass("show-detail-cart").addClass("hide-detail-cart").attr("aria-expanded", "true");
                });
                $(document).on('click', '.hide-detail-cart', function () {
                    $(this).parent(".product-item-details").find(".item-options").removeClass('active').hide();
                    $(this).removeClass("hide-detail-cart").addClass("show-detail-cart").attr("aria-expanded", "false");
                });
            }else {
                $(".show-detail-cart .item-options").show();
            }
            setTimeout(function(){
                $('.product-slider .slick-initialized .slick-slide.slick-cloned button').attr('tabindex','-1');
            }, 2500);
            setTimeout(function () {
                $('.checkout-cart-index .cart.items .hint').on('click',function(){
                    $(this).parent('.tooltip-container').find('.close-icon').addClass("close-icons").focus();
                    $(this).parent('.tooltip-container').removeClass("remove-item");
                });
            },3500);
        });
        /*WCAG Changes*/
        $('.catalog-product-view').on('ajaxComplete',function( event, request, settings ) {
            setTimeout(function(){
                $('.product-slider .slick-initialized .slick-slide.slick-cloned button').attr('tabindex','-1');
            }, 2000);

            var fetchElement = $(".catalog-product-view .data.item.content");
            fetchElement.each(function () {
                if($(this).find('.read-link-container').length == 0){
                    readLessMorePDP();
                }
            });
        });
        /*Role Remove when ajax complete*/
        $('.catalog-product-view,.checkout-cart-index,.catalogsearch-result-index,.cms-page-view,.cms-home,.catalog-category-view,.account').on('ajaxComplete',function( event, request, settings ) {
            setTimeout(function(){
                $('.ammenu-item').attr('role','none');
                $(".ammenu-main-container .ammenu-submenu-container .tab-title.ui-tabs-anchor").removeAttr("role");
            }, 2500);
        });
        /*Common Changes*/
        window.onload = new function() {
            setTimeout(function(){
                $('.ammenu-item').attr('role','none');
                $('.amcform-page-wrap.fields.ui-tabs-panel').removeAttr("aria-labelledby");
                $('.cms-page-view .landing-page-forms .control.amcform-gdpr').remove();
                $(".ammenu-nav-sections .ammenu-tabs-container>.ammenu-title").removeAttr("tabindex");
                $(".page-footer .form.subscribe input[name='am-gdpr-checkboxes-from'],.page-footer .form.subscribe input[name='am-ccpa-checkboxes-from']").remove();
                $('.location-container .ui-slider-handle.ui-state-default.ui-corner-all').text('Slide 1 of Location Slider').attr('aria-live','polite');
                $('.source-art-container .ui-slider-handle.ui-state-default.ui-corner-all').text('Slide 1 of Source for Art Slider').attr('aria-live','polite');
            }, 1200);
            $(".cms-page-view .amform-form .insert-container .form-control").attr("autocomplete","on");
            $('.ammenu-nav-sections .ammenu-categories .ammenu-wrapper .ammenu-item').css('display','none');
            //Remove focus parameter in url of layered navigation filers and clear links
            const filterAnchors = $("#layered-filter-block").find("a");
            if (filterAnchors.length) {
                filterAnchors.each(function(index, obj) {
                    let urlparts = $(obj).attr('href').split('?');
                    if (urlparts.length >= 2) {
                        let prefix = encodeURIComponent('focus') + '=';
                        let pars = urlparts[1].split(/[&;]/g);
                        for (let i = pars.length; i-- > 0;) {
                            if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                                pars.splice(i, 1);
                            }
                        }
                        let hrefWithoutFocus = urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : '');
                        $(this).attr('href', hrefWithoutFocus);
                    }
                });
            }

            //Start: have focus on element based on focus parameter in url
            if (!$('html').hasClass('ie11')){
                const queryString = window.location.search;
                const urlParams = new URLSearchParams(queryString);
                const pageFocus = urlParams.get('focus');
                if (pageFocus != null) {
                    $("#" + pageFocus).focus().select();
                }
            }
            if($('body').hasClass("checkout-index-index")){
                setTimeout(function(){
                    $(".checkout-index-index .nav-sections,.checkout-index-index .nav-toggle").remove();
                }, 500);
            }
            setTimeout(function () {
                $('.ammenu-item').attr('role','none');
                $(".ammenu-main-container .ammenu-submenu-container .tab-title.ui-tabs-anchor").removeAttr("role");
            },2500);

            //Focus on Close Button on Weighted tooltip
            $(document).on('click','.product-info-main:not(.pz-info-customize) .hint,body:not(.catalog-product-view) .hint,.minicart-wrapper .hint',function(){
                $(this).parents('.tooltip-container').find('.close-icon').focus();
            });
        };

        /*Multiple Wishlist Popup Focus Trap*/
        $(document).on('keydown', '#create-wishlist-block .action', function(e){
            if (e.keyCode == 9 && e.shiftKey == true && $(this).hasClass('close') && !$(this).hasClass('cancel') && $(this).is(':focus')){
                $('#create-wishlist-block .actions-toolbar .action.cancel').focus();
                e.preventDefault();
            }
            if (e.keyCode == 9 && e.shiftKey != true && $(this).hasClass('cancel') && $(this).is(':focus')) {
                $('#create-wishlist-block .popup-actions .action.close').focus();
                e.preventDefault();
            }
        });
        $(document).on('keydown', '.window.wishlist.popup', function(e){
            if (e.keyCode == 27){
                $(this).hide();
                $(".window.wishlist.overlay").hide();
            }
        });

        /*Tooltip code used on login, checkout pages*/
        $(document).on('keydown', '.field-tooltip .field-tooltip-content a', function(e){
            if (e.keyCode == 9 && e.shiftKey == true && $(this).parents('.field-tooltip').hasClass('_active') ){
                if($(this).parent().parent().children('span:last-child').length && $(this).is(':focus') && !$(this).hasClass('close-icon')){
                    $(this).parent().parent().children('a:first-child').focus();
                }
                if($(this).parent().children('span:last-child').length && $(this).is(':focus') && $(this).hasClass('close-icon')){
                    $(this).parent().find('span > a').focus();
                }
                e.preventDefault();
            }
            if (e.keyCode == 9 && e.shiftKey != true && $(this).parents('.field-tooltip').hasClass('_active') ){
                if($(this).parent().parent().children('span:last-child').length && $(this).is(':focus') && !$(this).hasClass('close-icon')){
                    $(this).parent().parent().children('a:first-child').focus();
                }
                if($(this).parent().children('span:last-child').length && $(this).is(':focus') && $(this).hasClass('close-icon')){
                    $(this).parent().find('span > a').focus();
                }
                e.preventDefault();
            }
        });
        $(document).on('click', '.field-tooltip .close-icon', function(e){
            if ($('.field-tooltip').hasClass('_active') ){
                $(this).parents('.field-tooltip').trigger('click');
                $(this).parents('.field-tooltip').find('.field-tooltip-action').focus();
                $('.field-tooltip-content').attr('aria-hidden','true');
                $('.field-tooltip-action').attr('aria-expanded','false');
                e.preventDefault();
            }
        });

        $(document).on('keydown', '.field-tooltip .close-icon', function(e){
            if (e.keyCode == 13 || e.keyCode == 27 && e.shiftKey != true && $(this).parents('.field-tooltip').hasClass('_active') ){
                $(this).parents('.field-tooltip').trigger('click');
                $(this).parents('.field-tooltip').find('.field-tooltip-action').focus();
                $('.field-tooltip-content').attr('aria-hidden','true');
                $('.field-tooltip-action').attr('aria-expanded','false');
                e.preventDefault();
            }
        });

        $(".what-this-tooltip").on("click",function() {
            setTimeout(function () {
                $('.what-this-tooltip-wrapper .field-tooltip-content .close-icon').focus();
            },100);
        });

        /*Keyboard Accessibilty for tooltip, Read Less-More ,Show View Detail, CMS Pages CLickable Elemnet */
        $(document).on('keypress keydown', '.showLessMore,.show-lessmore-link,.tooltip-container .hint,.show-hide-detail-cart', function (e) {
            if (e.keyCode == 13 || e.keyCode == 32) {
                $(this).trigger('click');
                e.preventDefault();
            }
        });
        $(document).on('keypress', '.project-showcase-container .image-container,.tiles-container img,.project-showcase-section .overlay-content .action.primary', function (e) {
            if (e.keyCode == 13 || e.keyCode == 32) {
                $(this).trigger('click');
                e.preventDefault();
            }
        });

        /*Checkout Page Focus on tooltip close*/
        $('.checkout-index-index').on('ajaxComplete',function( event, request, settings ) {
            $(".field-tooltip-action").on("click", function () {
                if ($('body').hasClass('checkout-index-index')) {
                    if ($(this).parent('.field-tooltip').find('span:first-child a').length) {
                        $(this).parent('.field-tooltip').find('span:first-child a').addClass("close-tooltip-icons").focus();
                    } else {
                        $(this).next().children().addClass("close-tooltip-icon").focus();
                    }
                }
            });
        });
        $(window).on('resize load', function() {
            if (_.isEmpty(woObj)) {
                console.log('woObj object is not initiated');
                return;
            }
            woObj.crossSellsSlider();
            woObj.pdpTabShowMoreDescription();
            woObj.projectShowcaseCareer();
            woObj.artRangSlider();

            if( $.woport.getViewPortSize().width < 768){
                $(".ammenu-content .greet.welcome span").attr("id","greeting");
                $(".ammenu-content .customer-welcome .customer-name").attr("aria-labelledby","greeting");
                $(".panel.header .greet.welcome span").removeAttr("id");
                $(".panel.header .customer-welcome .customer-name").removeAttr("aria-labelledby");
                $(".footer-right-inner .footer-columns .footer-block-heading-container").attr({'tabindex':'0','role':'button','aria-expanded':'false'});
                $(".source-art-container .slick-initialized .slick-slide").attr('aria-current','false');
            }
            else {
                $(".ammenu-content .greet.welcome span").removeAttr("id");
                $(".ammenu-content .customer-welcome .customer-name").removeAttr("aria-labelledby");
                $(".panel.header .greet.welcome span.not-logged-in").attr("id","greeting");
                $(".panel.header .customer-welcome .customer-name").attr("aria-labelledby","greeting");
                $(".footer-right-inner .footer-columns .footer-block-heading-container").removeAttr('tabindex role aria-expanded');
                $(".source-art-container .slick-initialized .slick-slide").first().attr('aria-current','true');
            }
            woObj.selectCurrentAccountPage();

            var fetchElement = $(".catalog-product-view .data.item.content");
            fetchElement.each(function () {
                if($(this).find('.read-link-container').length == 0){
                    readLessMorePDP();
                }
            });
        });
    })(jQuery);
});
