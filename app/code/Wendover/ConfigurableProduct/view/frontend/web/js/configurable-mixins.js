/**
Purpose : Not to update image on PDP, until all the options are selected.
          Also enable the download (image & tear-sheet) after all options are selected
 +
 */
define([
    'jquery',
    'underscore',
    'Perficient_Catalog/js/framestockstatus',
    'mage/translate',
    'Perficient_PriceMultiplier/js/utility'
], function ($, _, frameStockStatus, $t, utility) {
    'use strict';

    const widgetMixin = {
        options: {
            place_holder_image: '',
            allOptionsSelectedButtons: {
                '#vir_button': true,
                '#pdp-download-btn': true,
                '#product-addtocart-button': true,
                '#product-tearsheet-button': true,
                'a[data-action="add-to-wishlist"],button[data-action="add-to-wishlist"]': true,
                'div.split.button.wishlist > [data-toggle="dropdown"]': true,
            },
            all_product_name: {},
            all_view_in_room_config: {},
            frame_attribute_id: null,
            pzCartProperties: 'input#pz_cart_properties',
            frameSelectElement: "#attribute",
            frame_swatch_li: 'li.mirror-frame',
            imgDownloadBtn: '#pdp-download-btn',
            viewInRoomBtn: '#vir_button',
            tearSheetDownloadBtn: '#product-tearsheet-button',
            tearSheetDownloadUrl: '/tearsheet/download/pdf/product_id/{PRODUCTID}',
            addToWishlistBtn: '#mail-to-friend',
            wishListUrl: null,
            initialPriceText: $t('Starting at ')

        },
        _create: function() {
          this._super();
          $('span.trigger-product-price').on('simpleProductPrice-update', this._reloadPrice.bind(this));
          $('body.catalog-product-view').on('selectionState-update', () => {
              this.setOptionButtonsState(this.isSelectionComplete());
          });
          // indirectly triggering the add-to-wishlist event
          if (!_.isEmpty(this.options.settings)) {
              $(_.last(this.options.settings)).trigger('change');
          }
        },
        _fillSelect: function (element) {
            var attributeId = element.id.replace(/[a-z]*/, ''),
                options = this._getAttributeOptions(attributeId),
                prevConfig,
                index = 1,
                allowedProducts,
                allowedProductsByOption,
                allowedProductsAll,
                i,
                j,
                finalPrice = parseFloat(this.options.spConfig.prices.finalPrice.amount),
                optionFinalPrice,
                optionPriceDiff,
                optionPrices = this.options.spConfig.optionPrices,
                allowedOptions = [],
                indexKey,
                allowedProductMinPrice,
                allowedProductsAllMinPrice,
                canDisplayOutOfStockProducts = false,
                filteredSalableProducts;

            this._clearSelect(element);
            element.options[0] = new Option('', '');
            element.options[0].innerHTML = this.options.spConfig.chooseText;
            prevConfig = false;

            if (element.prevSetting) {
                prevConfig = element.prevSetting.options[element.prevSetting.selectedIndex];
            }

            if (options) {
                for (indexKey in this.options.spConfig.index) {
                    /* eslint-disable max-depth */
                    if (this.options.spConfig.index.hasOwnProperty(indexKey)) {
                        allowedOptions = allowedOptions.concat(_.values(this.options.spConfig.index[indexKey]));
                    }
                }

                if (prevConfig) {
                    allowedProductsByOption = {};
                    allowedProductsAll = [];

                    for (i = 0; i < options.length; i++) {
                        /* eslint-disable max-depth */
                        for (j = 0; j < options[i].products.length; j++) {
                            // prevConfig.config can be undefined
                            if (prevConfig.config &&
                                prevConfig.config.allowedProducts &&
                                prevConfig.config.allowedProducts.indexOf(options[i].products[j]) > -1) {
                                if (!allowedProductsByOption[i]) {
                                    allowedProductsByOption[i] = [];
                                }
                                allowedProductsByOption[i].push(options[i].products[j]);
                                allowedProductsAll.push(options[i].products[j]);
                            }
                        }
                    }

                    if (typeof allowedProductsAll[0] !== 'undefined' &&
                        typeof optionPrices[allowedProductsAll[0]] !== 'undefined') {
                        allowedProductsAllMinPrice = this._getAllowedProductWithMinPrice(allowedProductsAll);
                        finalPrice = parseFloat(optionPrices[allowedProductsAllMinPrice].finalPrice.amount);
                    }
                }

                for (i = 0; i < options.length; i++) {
                    if (prevConfig && typeof allowedProductsByOption[i] === 'undefined') {
                        continue; //jscs:ignore disallowKeywords
                    }

                    allowedProducts = prevConfig ? allowedProductsByOption[i] : options[i].products.slice(0);
                    optionPriceDiff = 0;

                    if (typeof allowedProducts[0] !== 'undefined' &&
                        typeof optionPrices[allowedProducts[0]] !== 'undefined') {
                        allowedProductMinPrice = this._getAllowedProductWithMinPrice(allowedProducts);
                        optionFinalPrice = parseFloat(optionPrices[allowedProductMinPrice].finalPrice.amount);
                        optionPriceDiff = optionFinalPrice - finalPrice;
                        options[i].label = options[i].initialLabel;
                    }

                    if (allowedProducts.length > 0 || _.include(allowedOptions, options[i].id)) {
                        options[i].allowedProducts = allowedProducts;
                        element.options[index] = new Option(this._getOptionLabel(options[i]), options[i].id);

                        if (this.options.spConfig.canDisplayShowOutOfStockStatus) {
                            filteredSalableProducts = $(this.options.spConfig.salable[attributeId][options[i].id]).
                            filter(options[i].allowedProducts);
                            canDisplayOutOfStockProducts = filteredSalableProducts.length === 0;
                        }

                        if (typeof options[i].price !== 'undefined') {
                            element.options[index].setAttribute('price', options[i].price);
                        }

                        if (allowedProducts.length === 0 || canDisplayOutOfStockProducts) {
                            element.options[index].disabled = true;
                        }

                        element.options[index].config = options[i];
                        index++;
                    }

                    /* eslint-enable max-depth */
                }
            }
        },
        _initializeOptions: function(){
            this._initializeOptionPrice();

            this._super();

            $('.config-text').text(this.options.initialPriceText);

            //if has frame swatches
            if (this.options.frame_attribute_id) {
                this._initializeFrameOptions();
            }
        },
        _initializeOptionPrice: function() {
            const {optionPrices} = this.options.spConfig;

            utility.getMultipliedPrice(_.keys(optionPrices));
        },
        _setupChangeEvents: function() {
            this._super();

            if (this.options.frame_attribute_id) {
                $(this.options.frame_swatch_li).on('click', (event) => {
                    const parent_stage = $(event.target).parents('.owl-stage');
                    const parent_item = $(event.target).parents('.owl-item');
                    // On frame re-selection, avoid clearing of value on size and glass-type dropdown.
                    if (parent_item && $(parent_item[0]).hasClass('selected')) {
                        return;
                    }
                    const liElements = $(parent_item).children('li');
                    if (liElements.length == 0) {
                        return;
                    }
                    const imageElements = liElements.children('img');
                    if (imageElements.length == 0) {
                        return;
                    }
                    const imageClicked = imageElements.attr('alt');
                    $(this.options.frameSelectElement+this.options.frame_attribute_id).val(imageClicked).change();
                    // remove .selected css class from previous item
                    if (parent_stage) {
                        $(parent_stage[0]).children('.owl-item.selected').removeClass('selected');
                    }
                    // add .selected css class from previous item
                    if (parent_item) {
                        $(parent_item[0]).addClass('selected');
                    }
                });
                // add frame change event
                $(this.options.frameSelectElement+this.options.frame_attribute_id)
                    .on('change', (event) => this._updateSelectedFrameLabel(event.target));
                // refresh frame component, to fix broken owl-carousel on accidental drag while clicking
                $("#mirror-frame-swatches-" + this.options.spConfig.productId)
                    .on('dragged.owl.carousel', (event) => $(event.target).trigger('refresh.owl.carousel'));
            }
        },
        _updateSelectedFrameLabel: function(element) {
            const optionLabel = $(element).find(":selected")[0]?.value;
            $("#selected-frame").text(optionLabel);
            const {frame_attribute_id, spConfig} = this.options;
            const frameAttribute = spConfig?.attributes[frame_attribute_id];
            const selectedFrame = _.find(frameAttribute?.options, (option) => { return (option.id === optionLabel); });
            $("#frameFinish").text('');
            if (!_.isEmpty(selectedFrame.frameFinish)) {
                $("#frameFinish").text(selectedFrame.frameFinish);
            }

            frameStockStatus({'defaultFrameSku':  $("#selected-frame").text()});
        },
        _initializeFrameOptions: function() {

            const frameOptions = this._getAttributeOptions(this.options.frame_attribute_id);
            const frameSwatchCarousel = $("#mirror-frame-swatches-"+this.options.spConfig.productId);
            _.each(frameOptions, $.proxy(function(element) {
                const liOption = `<li id='mirror-frame-${this.options.spConfig.productId}-${element.id}' class='mirror-frame'>
                    <span>Frame</span>
                    <img src="${element.frameImage}" alt="${element.id}" data-spec-image-path="${element.frameImageSpec}">
                    <span>${element.id}</span>
                    <p>${element.frameDimension}</p>
                </li>`;
                frameSwatchCarousel.trigger('add.owl.carousel', liOption);
            }, this));
            frameSwatchCarousel.trigger('refresh.owl.carousel');
        },
        _configureElement: function(element) {
            this._super(element);

            const {frame_attribute_id, values, spConfig} = this.options;
            if (frame_attribute_id && element.value && (Number.parseInt(element.attributeId) === frame_attribute_id)) {
                const parentItem = $(`#mirror-frame-${spConfig.original_product_id}-${element.value}`).parents('.owl-item');
                if (parentItem.length === 0) {
                    return;
                }
                parentItem.addClass('selected');
                this._updateSelectedFrameLabel(element);
            }
        },
        isSelectionComplete: function() {
            return _.reduce(this.options.settings, function(memo, element) {
                return (element.value !== '');
            });
        },
        _changeProductImage: function (noLabel) {
            // check all options are selected
            const isSelectionComplete = this.isSelectionComplete();

            const { viewInRoomBtn, allOptionsSelectedButtons, initialPriceText } = this.options;

            $('.config-text').text(isSelectionComplete ? '' : initialPriceText);

            // update download urls
            this.setDownloadTearSheet(isSelectionComplete ? this.simpleProduct : null);
            this.setDownloadImage(isSelectionComplete ? this.simpleProduct : null);
            this.setWishlistUrl(isSelectionComplete ? this.simpleProduct : null);

            // update viewInRoom
            const isViewInRoomDataSet = this.setViewInRoom(isSelectionComplete ? this.simpleProduct : 'default');
            allOptionsSelectedButtons[viewInRoomBtn] = isViewInRoomDataSet;

            // update specialty label
            this.setSpecialty(isSelectionComplete ? this.simpleProduct : 'default');
            // update weight label
            this.setWeight(isSelectionComplete ? this.simpleProduct : 'default');
            // update pzCartProperties textfield
            this.setPzCartProperties(isSelectionComplete ? this.simpleProduct : 'default');

            // update SKU and name on PDP
            const {sku} = this.options.spConfig;
            if (sku) {
                const newSku = isSelectionComplete ? sku[this.simpleProduct] :
                    $(this.options.imgDownloadBtn).data('configurable-sku')
                this.updatePDPSKU(newSku);
            }
            const {all_product_name} = this.options;
            if (all_product_name) {
                this.updatePDPName(isSelectionComplete ? all_product_name[this.simpleProduct] : all_product_name['default']);
            }

            this.setOptionButtonsState(isSelectionComplete);

            if (isSelectionComplete) {
                return this._super(noLabel);
            }

            // Selection not completed
            const gallery = $(this.options.mediaGallerySelector)?.data('gallery');
            if (gallery && this.options.mediaGalleryInitial) {
                const defaultImg = this.options.mediaGalleryInitial[0]?.img;
                const currImage = gallery?.returnCurrentImages()[0]?.img;
                if (currImage !== defaultImg) {
                    gallery.updateData(this.options.mediaGalleryInitial);
                }
            }
        },
        getSimpleProductImage: function(product_id) {
            const imageDownloadButton = $(this.options.imgDownloadBtn);
            if (!this.options.mediaGalleryInitial) {
                return {'href' : null, 'sku': null};
            }
            // default images
            let href = this.options.mediaGalleryInitial[0].full;
            let sku = $(imageDownloadButton).data('configurable-sku');
            if (product_id == null) {
                return {'href': href, 'sku': sku};
            }

            const spConfig = this.options.spConfig;
            if (spConfig && spConfig.images) {
                // simple product images
                let productImages = this.options.place_holder_image;
                if (!_.isEmpty(spConfig.images) && !_.isEmpty(spConfig.images[product_id])) {
                    [productImages] = spConfig.images[product_id];
                }
                if (productImages) {
                    const newHref = _.isString(productImages) ? productImages : productImages.full;
                    const newSku = spConfig.sku[product_id];
                    href = newHref ? newHref : href;
                    sku = newSku ? newSku : sku;
                }
            }
            return {'href': href, 'sku': sku};

        },
        setSpecialty: function(product_id) {
            const specialtyElement = $('#product-specialty-text');
            const {specialties} = this.options.spConfig;
            let value = '';
            if (!_.isEmpty(specialties) && !_.isEmpty(specialties[product_id])) {
                value = specialties[product_id];
            }
            specialtyElement.text(value);
        },
        setWeight: function(product_id) {
            const weightElement = $('#product-weight-text');
            const {weights} = this.options.spConfig;
            let value = '';
            if (!_.isEmpty(weights) && !_.isEmpty(weights[product_id])) {
                value = weights[product_id];
            }
            weightElement.text(value);
        },
        setPzCartProperties: function(product_id) {
            const {spConfig, pzCartProperties} = this.options
            const pzCartTextElement = $(pzCartProperties);
            const {'pz_cart_properties': pzCartPropertiesData } = spConfig;
            let jsonData = JSON.parse(pzCartTextElement.val());
            if (!_.isEmpty(pzCartPropertiesData) && !_.isEmpty(pzCartPropertiesData[product_id])) {
                _.extend(jsonData, pzCartPropertiesData[product_id]);
            }
            pzCartTextElement.val(JSON.stringify(jsonData));
            $(pzCartProperties).trigger('update_pz_cart_properties');
        },
        setViewInRoom: function(product_id) {
            const {all_view_in_room_config} = this.options;
            if (_.isEmpty(all_view_in_room_config) || _.isEmpty(all_view_in_room_config[product_id])) {
                if (product_id != 'default') {
                    console.info(`View-in-room configuration is invalid for the selected product-id: ${product_id}`);
                }
                return false;
            }
            window.vir_config = all_view_in_room_config[product_id];
            return true;
        },
        setDownloadTearSheet: function(product_id) {
            const tearSheetDownloadButton = $(this.options.tearSheetDownloadBtn);
            const href =
                product_id ? this.options.tearSheetDownloadUrl.replace('{PRODUCTID}', product_id) : '#';
            tearSheetDownloadButton.attr("href", href)
        },
        setWishlistUrl: function(product_id) {
            const {addToWishlistBtn, wishListUrl} = this.options;
            const defaultWishlistUrl = wishListUrl.replace(/\/$/, "");
            const addToWishlistHref = product_id ?
                `${defaultWishlistUrl}/child_id/${product_id}` : defaultWishlistUrl;

            $(addToWishlistBtn).attr('href', addToWishlistHref);
        },
        setDownloadImage: function(product_id) {
            const imageDownloadButton = $(this.options.imgDownloadBtn);
            const {href, sku} = this.getSimpleProductImage(product_id);
            imageDownloadButton.attr("href", href);
            imageDownloadButton.attr("download", sku);
        },
        setOptionButtonsState: function(isSelection) {
            _.each(this.options.allOptionsSelectedButtons, (elementState, elementSelector) => {
                if (elementState && isSelection) {
                    $(elementSelector).removeClass('disabled');
                } else {
                    $(elementSelector).addClass('disabled');
                }
            });
        },
        updatePDPSKU: function(sku) {
            const skuElement = $('div.product.attribute.sku').children('div.value');
            if (skuElement.length === 0) {
                return;
            }
            skuElement.text(sku)
        },
        updatePDPName: function(name) {
            const nameElement = $('h1.page-title').children('span[data-ui-id="page-title-wrapper"]');
            if (nameElement.length === 0) {
                return;
            }
            nameElement.text(name)

        }
    };

    return function (configurableWidget) {
        $.widget('mage.configurable', configurableWidget, widgetMixin);

        return $.mage.configurable;
    };
});


