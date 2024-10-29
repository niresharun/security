define(
    [
        'jquery',
        'mage/url',
        'mage/translate',
        'Magento_Ui/js/modal/confirm',
        'Magento_Ui/js/modal/modal',
        'jquery/ui',
        'domReady!',
        'Perficient_MyCatalog/js/jquery.ui.touch-punch.min',
        'Perficient_MyCatalog/js/jquery.jcarousel.min',
        'Perficient_MyCatalog/js/jquery.jcarousel-pagination'
    ],
    function (
        $,
        urlBuilder,
        $t,
        confirm,
    ) {
        $('.breadcrumbs').hide();

        let main = {
            addItem: function (carousel) {
                $('#catalog_addpage_button').on('click', function () {
                    $('.jcarousel-skin-tango .jcarousel-item-backpage').remove();

                    let items = carousel.find('li');
                    let next = items.length + 1;
                    let newItem = `<li class="sort-item jcarousel-item jcarousel-item-horizontal jcarousel-item-${next - 1} jcarousel-item-${next - 1}+-horizontal" id="th_${next - 1}" jcarouselindex="${next - 1}" style="float: left; list-style: none;" aria-current="false">
                        <a href="javascript:void(0);" class="template-page-type" data-id="${next - 1}">
                            <img tabindex="0" role="button" src="${imagePathUrl}/page1.png" alt="Cover Page ${next - 1}" class="wendover_page_thumb">
                            <div aria-hidden="true">${next - 1}</div>
                        </a>
                    </li>`;
                    let backCoverItem = `<li class="cover backpage jcarousel-item-backpage">
                        <a href="javascript:void(0);" class="template-page-type" data-id="back">
                            <img src="${imagePathUrl}/back-page.png" class="wendover_page_thumb">
                            <div>&nbsp;</div>
                        </a>
                    </li>`;
                    carousel.append(newItem);
                    carousel.append(backCoverItem);
                    $('.wendover_carousel').jcarousel('reload');
                    $('.wendover_carousel').jcarousel('scroll', next);
                    main.createPage(next - 1);
                    return false;
                });
            },

            showOverlay: function () {
                // Show the overlay.
            },

            hideOverlay: function () {
                // Hide the overlay.
            },

            savePageCheck: function (check) {
                let page_id = $('#wendover_page_id').val();
                if (page_id == 'front' || page_id == 'back') return;

                main.savePage(pageConfig);
            },

            loadTemplate: function (id, noReset) {
                main.showOverlay();

                let template = parseInt(id);
                let page = $('#wendover_page_id').val();
                let catalogId = $('#wendover_catalog_id').val();

                let pageData = {
                    template: template,
                    page: page,
                    catalog_id: catalogId,
                };

                pageConfig = {};
                $('.dropspot_desc').html('');
                $.ajax({
                    url: getTemplateUrl,
                    type: "POST",
                    dataType: "JSON",
                    showLoader: true,
                    data: pageData,
                    success: function (data) {
                        $('#wendover_template_content').html(data.html);
                        pageConfig = data.pageConfig;
                        if (!pageConfig || pageConfig.length == 0) {
                            pageConfig = {};
                        }

                        main.initDraggable();
                        main.initDroppable();

                        $('#wendover_template_id').val(id);
                        $('.wendover_template_canvas .draggable').hide();
                        $('.wendover_template_canvas .draggable').each(function () {
                            $(this).show();

                            for (let item in pageConfig) {
                                if (pageConfig[item].item_id == $(this).attr('data-alt')) {
                                    main.fitImage(pageConfig[item].item_id, item, $('#' + item + ' img').attr('id'));
                                }
                            }
                        });

                        main.fitAllImages();
                        if ($('.pricebox-container').first().css('display') == 'none') {
                            $('.price-multiplier-edit').fadeOut();
                            $('#catalog_pricing_button').html($t('Show Prices'));
                            $('#price_on').val(0);
                            $('.pricebox-container').fadeOut();
                        }
                        main.hideOverlay();

                        $('.page_number').html('Page ' + page);
                    }
                });
            },

            loadPage: function (page, noSave) {
                if (!noSave) {
                    main.savePageCheck();
                }

                main.showOverlay();
                main.resetPage();

                let pageData = {
                    pages: {
                        page_id: 0,
                        page_position: page,
                        page_template_id: $('#wendover_template_id').val()
                    },
                    catalog_id: $('#wendover_catalog_id').val(),
                };

                $.ajax({
                    url: loadPageUrl,
                    type: "POST",
                    dataType: "JSON",
                    showLoader: true,
                    data: pageData,
                    success: function (jdata) {
                        $('#wendover_page_id').val(page);
                        $('.wendover_page_thumbnails li').removeClass('active').attr('aria-current','false');
                        $('#th_' + page).addClass('active').attr('aria-current','true');
                        let templateId = jdata.page_template_id;

                        if (!templateId) templateId = 1;

                        let selectIndex = (parseInt(templateId) - 1) + '';
                        main.loadTemplate(templateId);
                    }
                });

                // Disable the previous page link, if the user is already on the same page.
                if (page <= 1) {
                    $('#icon-nav-left').addClass('disabled');
                } else {
                    $('#icon-nav-left').removeClass('disabled');
                }

                $('.jcarousel-next, .jcarousel-next').bind('click', function () {
                    $('#carousel-button').val(0);
                });
            },

            savePage: function (params) {
                let page_id = $('#wendover_page_id').val();
                if (page_id == 'front' || page_id == 'back') return;

                let pageData = {
                    pages: {
                        page_id: 0,
                        page_position: $('#wendover_page_id').val(),
                        page_template_id: $('#wendover_template_id').val(),
                        dropspot_config: params
                    },
                    catalog_id: $('#wendover_catalog_id').val(),
                    is_ajax: true
                };

                $.ajax({
                    url: savePageUrl,
                    type: "POST",
                    dataType: "JSON",
                    showLoader: true,
                    data: pageData,
                    beforeSend: function() {
                        $('body').trigger('processStart');
                    },
                    success: function (data) {
                        $('#th_' + pageData.pages.page_position)
                            .find('img')
                            .attr('src', imagePathUrl + '/page' + pageData.pages.page_template_id + '.png');
                    }
                });
                $('body').trigger('processStop');
            },

            savePdf: function (check) {
                main.showOverlay();
                let request = {};

                let pageData = {
                    pages: {
                        page_id: 0,
                        page_position: $('#wendover_page_id').val(),
                        page_template_id: $('#wendover_template_id').val(),
                        dropspot_config: pageConfig
                    },
                    catalog_id: $('#wendover_catalog_id').val(),
                    is_ajax: true
                };

                $.ajax({
                    url: savePageUrl,
                    type: "POST",
                    dataType: "JSON",
                    showLoader: true,
                    data: pageData,
                    success: function (data) {
                        if(check){
                            $('#th_' + pageData.pages.page_position)
                                .find('img')
                                .attr('src', imagePathUrl + '/page' + pageData.pages.page_template_id + '.png');
                            top.location = generatePdfUrl + 'catalog_id/' + pageData.catalog_id+'/download/' + check;
                        }
                    },
                    complete: function () {
                        main.hideOverlay();
                    }
                });
            },

            printCatalog: function () {
                main.showOverlay();
                let request = {};

                let pageData = {
                    pages: {
                        page_id: 0,
                        page_position: $('#wendover_page_id').val(),
                        page_template_id: $('#wendover_template_id').val(),
                        dropspot_config: pageConfig
                    },
                    catalog_id: $('#wendover_catalog_id').val(),
                    is_ajax: true
                };

                $.ajax({
                    url: savePageUrl,
                    type: "POST",
                    dataType: "JSON",
                    showLoader: true,
                    data: pageData,
                    success: function (data) {
                        $('#th_' + pageData.pages.page_position)
                            .find('img')
                            .attr('src', imagePathUrl + '/page' + pageData.pages.page_template_id + '.png');
                        top.location = printCatalogUrl + 'catalog_id/' + pageData.catalog_id;
                    },
                    complete: function () {
                        main.hideOverlay();
                    }
                });
            },

            createPage: function (total) {
                main.showOverlay();
                main.savePage(pageConfig);

                let pageData = {
                    pages: {
                        page_id: 0,
                        page_position: total,
                        page_template_id: 1,
                        dropspot_config: {}
                    },
                    catalog_id: $('#wendover_catalog_id').val()
                };

                $.ajax({
                    url: savePageUrl,
                    type: "POST",
                    dataType: "JSON",
                    showLoader: true,
                    data: pageData,
                    success: function (data) {
                        $("#wendover_total").val(total);
                        $('.wendover_carousel').jcarousel({initCallback: main.addItem, itemFallbackDimension: 100});
                        main.loadPage(data, 1);
                    }
                });
            },

            prevPage: function () {
                let pageId = parseInt($('#wendover_page_id').val());
                if (pageId >= 2) {
                    main.loadPage(pageId - 1);
                    $('.wendover_page_thumbnails').jcarousel('scroll', pageId - 1);
                }
            },

            nextPage: function () {
                let pageId = parseInt($('#wendover_page_id').val());
                let total = parseInt($('#wendover_total').val());

                if (pageId < total) {
                    main.loadPage(pageId + 1);
                    $('.wendover_page_thumbnails').jcarousel('scroll', pageId + 1);
                } else {
                    confirm({
                        title: $t("Create New Page"),
                        content: $t("You've reached the end of the pages list. Do you want to create new page?"),
                        modalClass: "classModal",
                        actions: {
                            /** @inheritdoc */
                            confirm: function () {
                                $('#catalog_addpage_button').click();
                            }
                        }
                    });
                }
            },

            resetElement: function (element) {
                $(element).css('top', 0).css('left', 0).css('position', 'relative');
            },

            resetPage: function () {
                $('.draggable').css('top', 0).css('left', 0);
                $('.dropspot_desc').html('');
                $('.dropspot').html('');
                pageConfig = {};
            },

            fitImage: function (imageClass, target, imageId) {
                if (!imageId) {
                    let newId = Math.floor((Math.random() * 100000) + 1);
                    $('.' + imageClass + ':first').clone().attr('id', newId);
                    imageId = newId;
                }
                let tWidth = parseInt($('#' + target).width());
                let tHeight = parseInt($('#' + target).height());

                $('#' + imageId).css({
                    width: 'auto',
                    height: 'auto',
                    position: 'absolute',
                    left: 0
                }).css('max-width', '100%').css('max-height', '100%');
                let iWidth = parseInt($('#' + imageId).width());
                let iHeight = parseInt($('#' + imageId).height());

                offsetHeight = (tHeight - iHeight) / 2;
                offsetWidth = (tWidth - iWidth) / 2;
                //offsetWidth = offsetHeight = 0;
                $('#' + imageId).css('position', 'absolute').css('left', offsetWidth).css('top', offsetHeight);
                $('#' + target).css('background-color', '#fff');

                let pid = $('#' + target).attr('rel');
                pageConfig[target] = {item_id: imageClass};
                $('#' + imageId).attr('parent', target);
            },

            fitAllImages: function () {
                for (let item in pageConfig) {
                    main.fitImage(pageConfig[item].item_id, item, $('#' + item + ' img').attr('id'));
                }
            },

            getImagePosition: function (image) {
                let pid;
                $.each(pageConfig, function (key, value) {
                    if (value['item_id'] == image) {
                        pid = $('#' + key).attr('rel');
                    }
                });

                return pid;
            },

            scrollToPage: function (p) {
                $('#carousel-button').val(1);
                let total = parseInt($('#carousel-count').val());
                let itemsPerPage = Math.floor(total / 9);
                let item = (itemsPerPage * (p - 1));

                main.scrollToItem(item);

                $('#select-dots li').removeClass('active');
                $('#select-dots .bullet' + p).addClass('active');
            },

            scrollToItem: function (targetPage) {
                $('#carousel-item').val(targetPage);
                $('.placeholder-carousel').jcarousel('scroll', targetPage);
            },

            initDroppable: function () {
                $(".droppable").droppable({
                    drop: function (event, ui) {
                        dropped = true;

                        $(this).css('border', '4px solid #fff').css('background-color', '#fff').css('opacity', 1).removeAttr("aria-dropeffect");
                        $(this).find("img").attr("aria-grabbed","false").focus();
                        let target = $(this).attr('id');
                        let image = ui.draggable.attr("data-alt");
                        let imageId = ui.draggable.attr("id");
                        if (target == $('#' + imageId).attr('parent')) return;

                        if ($('#' + imageId).attr('parent')) {
                            let parent = $('#' + imageId).attr('parent');
                            delete pageConfig[parent];
                            $('.' + parent + '_desc').html('');
                            $('#' + parent).css('background-color', '');
                        }

                        let dropElement = main.getImagePosition(image);
                        if (dropElement) {
                            let id = $('#' + dropElement).attr('rel');
                            $('.dropspot_' + id + '_desc').html('');
                        }
                        $('.draggable').removeAttr('dragging');

                        let tid = target.slice(9);
                        $('.dropspot_' + tid + '_desc').html('(' + tid + ')<br/>' + $('#place_' + image).html());
                        $('#' + target).html('');

                        $('#' + imageId).appendTo($('#' + target));
                        main.fitImage(image, target, imageId);
                    },
                    over: function (event, ui) {
                        $(this).css('border', '4px dashed #aaa').css('background-color', '#ccc').css('opacity', .5).attr("aria-dropeffect","move");
                        $(this).find("img").attr("aria-grabbed","true").focus();
                    },
                    out: function (event, ui) {
                        let color;
                        if ($(this).children('img').length > 0) {
                            color = '#fff';
                        }
                        else color = '';

                        $(this).css('border', '4px solid #fff').css('background-color', color).css('opacity', 1).removeAttr("aria-dropeffect");
                        $(this).find("img").attr("aria-grabbed","false").focus();
                        let target = $(this).attr('id');
                        let image = ui.draggable.attr('id');

                        if (!$('#' + image).attr('dragging') == 1) {
                            let id = $('#' + target).attr('rel');
                            $('#' + image).attr('dragging', 1)
                        }
                    }
                });
            },

            initDraggable: function () {
                let docWidth, docHeight;

                $(".draggable").draggable({
                    start: function (event, ui) {
                        ui.helper.data('dropped', false);

                        $('body').css('cursor', 'hand !important');
                        docWidth = parseInt($('body').css('width')) - 225;
                        docHeight = parseInt($('body').css('height')) - 225;

                        ui.helper.width('217px');
                        if ($(this).attr('moved') != 'true') {
                            $(this).attr({dragging: 1, id: Math.floor((Math.random() * 100000) + 1)});
                            $(this).clone().appendTo($(this).parent()).attr("id", Math.floor((Math.random() * 100000) + 1));
                            $(this).attr('moved', 'true').attr("id", Math.floor((Math.random() * 100000) + 1)).attr('aria-grabbed', 'true');
                            main.initDraggable();
                        }
                    },
                    helper: 'clone',
                    appendTo: 'body',
                    snap: ".dropspot",
                    snapTolerance: '5',
                    snapMode: 'inner',
                    revert: false,
                    reverting: function () {
                    },
                    drag: function (event, ui) {
                        let offset = ui.helper.offset();
                        let top = offset.top - 225;
                        let left = offset.left - 225;

                        if (left > docWidth) {
                            ui.position.left = docWidth;
                        }
                        ui.position.top = event.pageY;

                        var headerHeight = $(".page-header").height() - 40;
                        var parentElement = $('#wendover_template');
                        $('html, body').animate({
                            scrollTop: $(parentElement).offset().top + headerHeight - 175,
                        }, 0);
                    },
                    stop: function (event, ui) {
                        $('body').unbind().css('cursor', 'normal');

                        if (!dropped) {
                            let imageId = $(this).attr("id");
                            let image = $(this).attr("data-alt");

                            let dropElement = $('#' + imageId).attr('parent');
                            $('#' + imageId).fadeOut(function () {
                                $(this).remove()
                            });
                            delete pageConfig[dropElement];
                            $('.' + dropElement + '_desc').html('');
                            $('#' + dropElement).css('background-color', '');
                        }
                        dropped = false;
                    }
                });
            },

            savePageOnUnload: function(params) {
                if (pageAction === 'delete') {
                    // prevents the delete action from recreating the page it just deleted
                    return true;
                }

                let page_id = $('#wendover_page_id').val();
                if (page_id == 'front' || page_id == 'back') return;

                let pageData = {
                    pages: {
                        page_id: 0,
                        page_position: $('#wendover_page_id').val(),
                        page_template_id: $('#wendover_template_id').val(),
                        dropspot_config: params
                    },
                    catalog_id: $('#wendover_catalog_id').val(),
                    is_ajax: true
                };

                $.ajax({
                    url: savePageUrl,
                    type: "POST",
                    dataType: "JSON",
                    showLoader: true,
                    data: pageData,
                    async: false,
                    beforeSend: function () {
                    },
                    complete: function () {
                    },
                    success: function (data) {
                    }
                });
            },

            openPrice: function () {
                if ($('.price-dialog').css('display') != 'block') {
                    $('.price-arrow').addClass("active");
                    $('.price-dialog').fadeIn();

                } else {
                    main.closePrice();
                }
            },

            closePrice: function () {
                $('.price-arrow').removeClass("active");
                $('.price-dialog').fadeOut();
            },

            triggerPrices: function() {
                let value = $('#price_on').val();
                if (value != 1) {
                    $('.price-multiplier-edit').fadeIn();
                    $('#catalog_pricing_button').html($t('Hide Prices'));
                    $('#price_on').val(1);
                    $('.pricebox-container').fadeIn();
                    $('#price_multiplier_option').focus();
                } else {
                    $('.price-multiplier-edit').fadeOut();
                    $('#catalog_pricing_button').html($t('Show Prices'));
                    $('#price_on').val(0);
                    $('.pricebox-container').fadeOut();
                    $('.price-multiplier-edit').fadeOut();
                }

                let pageData = {
                    catalog_id: $('#wendover_catalog_id').val(),
                    price_on: $('#price_on').val()
                };
                $.ajax({
                    url: setMultiplierUrl,
                    type: "POST",
                    dataType: "JSON",
                    showLoader: true,
                    data: pageData,
                    success: function (data) {
                    }
                });
            },

            applyPrice: function () {
                let catalogId = $('#wendover_catalog_id').val();
                let value = priceMultiplier;
                if (!value || value === false) {
                    return;
                }

                var price = parseFloat(value);
                var priceOn = 1;

                $('.price-multiplier span.price-multiplier-change').html(price);

                let pageData = {
                    catalog_id: catalogId,
                    price: price.toFixed(2),
                    price_on: priceOn
                };

                $.ajax({
                    url: setMultiplierUrl,
                    type: "POST",
                    dataType: "JSON",
                    showLoader: true,
                    data: pageData,
                    success: function (data) {
                    }
                });

                main.closePrice();
            }
        };

        $(function () {
            /* Carousel initialization */
            $('.wendover_carousel')
                .jcarousel({
                    // Options go here
                });

            main.addItem($('.wendover_page_thumbnails'));

            /* Prev control initialization */
            $('.jcarousel-prev')
                .on('jcarouselcontrol:active', function () {
                    $(this).removeClass('inactive');
                })
                .on('jcarouselcontrol:inactive', function () {
                    $(this).addClass('inactive');
                })
                .jcarouselControl({
                    // Options go here
                    target: '-=3'
                });

            /* Next control initialization */
            $('.jcarousel-next')
                .on('jcarouselcontrol:active', function () {
                    $(this).removeClass('inactive');
                })
                .on('jcarouselcontrol:inactive', function () {
                    $(this).addClass('inactive');
                })
                .jcarouselControl({
                    // Options go here
                    target: '+=3'
                });

            $('.wendover_carousel')
                .on('jcarousel:reload jcarousel:create', function () {
                    var carousel = $('.jcarousel-list'),
                        liCount = carousel.find('li').length,
                        liWidth = 100,
                        baseWidth = 650,
                        totalWidth = baseWidth + ((liCount >= 13) ? (liCount - 13) : 0) * liWidth;
                    $('.jcarousel-list').css('width', Math.ceil(totalWidth) + 'px');
                })
                .jcarousel({
                    wrap: 'null'
                });

            


            // Initialize jCarousel for placeholder ul
            $('.placeholder ul').jcarousel({
                initCallback: function (carousel, state) {
                    carousel.list.css('margin', '0 auto');
                    let w = parseInt(carousel.list.css('width'));
                    carousel.list.css('width', (w - 100));
                },
                itemLoadCallback: function (carousel, state) {
                    if (parseInt($('#carousel-count').val()) < 10) {
                        dot = parseInt($('#carousel-item').val());
                    } else {
                        let item;
                        let total = carousel.length;
                        if ($('#carousel-button').val() == 1) {
                            item = parseInt($('#carousel-item').val());
                        }
                        else {
                            item = parseInt(carousel.first);
                        }

                        let itemsPerPage = Math.floor(total / 9);
                        dot = Math.floor(item / itemsPerPage) + 1;
                    }

                    $('#select-dots li').removeClass('active');
                    $('#select-dots .bullet' + dot).addClass('active');
                }
            });



            $('.jcarousels').jcarousel();

        $('.jcarousels')
            .on('jcarousel:reload jcarousel:create', function () {
                var carousel = $(this),
                    width = carousel.innerWidth();

                if (width >= 1300) {
                    width = width / 18;
                } else if (width >= 1200) {
                    width = width / 12;
                } else if (width >= 1024) {
                    width = width / 6;
                } else if (width >= 880) {
                    width = width / 4;
                }  else if (width >= 640) {
                    width = width / 3;
                } else if (width >= 320) {
                    width = width / 2;
                }

                carousel.jcarousel('items').css('width', Math.ceil(width) + 'px');
            })
            .jcarousel({
                wrap: null
            });

        $('.jcarousel-control-prev')
            .on('jcarouselcontrol:active', function() {
                $(this).removeClass('inactive');
            })
            .on('jcarouselcontrol:inactive', function() {
                $(this).addClass('inactive');
            })
            .jcarouselControl({
                target: '-=3'
            });

        $('.jcarousel-control-next')
            .on('jcarouselcontrol:active', function() {
                $(this).removeClass('inactive');
            })
            .on('jcarouselcontrol:inactive', function() {
                $(this).addClass('inactive');
            })
            .jcarouselControl({
                target: '+=3'
            });

       

            /* Pagination initialization */
            $('.dots-bottom')
                .on('jcarouselpagination:active', 'a', function () {
                    $(this).addClass('active');
                })
                .on('jcarouselpagination:inactive', 'a', function () {
                    $(this).removeClass('active');
                })
                .jcarouselPagination({
                    perPage: 3
                });
            $('.dots-bottom a').text(''); // To remove the text content generated by Jcarousel

            $(document).ajaxComplete(function (data, status, xhr) {
                if (status.responseText && status.responseText == 'myCatalogAuthError') {
                    top.location = '';
                }
            });

            main.loadPage(1, 1);

            main.initDraggable();

            $('#catalog_action_button').click(function (e) {
                let actionMenu = $('#top-submenu');
                if (actionMenu.attr('rel') == '') {
                    actionMenu.attr('rel', 'open');
                    actionMenu.fadeIn();
                    $(this).attr('aria-expanded','true');
                } else {
                    actionMenu.attr('rel', '');
                    actionMenu.fadeOut();
                    $(this).attr('aria-expanded','false');
                }
            });
            $(".droppable img").mouseover(function(){
                $(this).addClass("hovered");
            });
            $(".droppable img").mouseout(function(){
                $(this).removeClass("hovered");
            });
            $(".droppable img").focus(function(){
                $(this).addClass("focused");
            });
            $(".droppable img").blur(function(){
                $(this).removeClass("focused");
            });
            function touchHandler(event) {
                var touch = event.changedTouches[0];
                var simulatedEvent = document.createEvent("MouseEvent");
                simulatedEvent.initMouseEvent({
                        touchstart: "mousedown",
                        touchmove: "mousemove",
                        touchend: "mouseup"
                    }[event.type], true, true, window, 1,
                    touch.screenX, touch.screenY,
                    touch.clientX, touch.clientY, false,
                    false, false, false, 0, null);

                touch.target.dispatchEvent(simulatedEvent);
            }

            function init() {
                document.addEventListener("touchstart", touchHandler, true);
                document.addEventListener("touchmove", touchHandler, true);
                document.addEventListener("touchend", touchHandler, true);
                document.addEventListener("touchcancel", touchHandler, true);
            }
            init();
        });
        return main;
    }
);
