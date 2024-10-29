require(['jquery', 'jquery/ui'], function ($) {
    'use strict';
    $(document).ready(function () {
        let bodyElement = $('body');

        window.dataLayer = window.dataLayer || [];
        if (bodyElement.hasClass('catalog-category-view')) {

            if ($('#layered-filter-block').length) {
                //category page load
                dataLayer.push({
                    'event': 'categoryView',
                    'source': $('#page-title-heading span').text()
                });
            }
            else {
                //category landing page
                dataLayer.push({
                    'event': 'categoryLandingView',
                    'source': $.trim($('h1').text())
                });

                //cateogry landing click
                bodyElement.on('click', '#subcat-grid a', function () {
                    dataLayer.push({
                        'event': 'categoryLandingClick',
                        'target': $.trim($(this).text())
                    });
                });
            }

            // applied sorting
            bodyElement.on('change', '#product_list_order', function () {
                dataLayer.push({
                    'event': 'appliedSorting',
                    'target': $(this).val()
                });
            });

            // Applied  Filter
            bodyElement.on('change', '.filter-content input[type=checkbox]', function () {
                if ($(this).is(':checked')) {
                    dataLayer.push({
                        'event': 'appliedFilter',
                        'target': $(this).parent().find('span.label').text()
                    });
                }
            });

            // Product Click
            bodyElement.on('click', '.products-grid a', function () {
                dataLayer.push({
                    'event': 'productClick',
                    'target': $(this).parentsUntil('.listing-product').find('.product-item-name a').text()
                });
            });
        }

        // product detail page
        if (bodyElement.hasClass('catalog-product-view')) {

            $('div#products-related-section a').addClass('series-product');
            $('div.block.upsell a').addClass('rec-product');

            //Series Product Click
            bodyElement.on('click', '.products-related a.series-product', function () {
                dataLayer.push({
                    'event': 'seriesProductClick'
                });
            });

            //Recommended Product Click
            bodyElement.on('click', '.products-upsell a.rec-product', function() {
                dataLayer.push({
                    'event': 'recommendedProductClick'
                });
            });

            //product detail page load
            dataLayer.push({
                'event': 'productView',
                'location': $('.page-title span').text()
            });

            // Add to favorites
            bodyElement.on('click', '.split.button.wishlist button', function () {
                dataLayer.push({
                    'event': 'addToFavorites',
                    'target': $('.catalog-product-view .page-title-wrapper span').text()
                });

            });

            // Add Collection to cart
            bodyElement.on('click', '.block.related .add-collection-link a', function () {
                var product = [];

                $('.products-related .slick-slide:not(.slick-cloned) a.series-product').each(function () {
                    var self,trimProductName;

                    self = $(this);
                    trimProductName = $.trim(self.find('.product-item-name').text());

                    product.push({
                        'name':trimProductName
                    });
                });
                dataLayer.push({
                    'event': 'addToCollection',
                    'target':  product
                });
            });

            // Add collection from favorites
            bodyElement.on('click','.product-set-wishlist',function () {
                dataLayer.push({
                    'event': 'addToCollection',
                    'target': $('.catalog-product-view .page-title-wrapper span').text()
                });
            });

            // Add to projects
            bodyElement.on('click', '.split.button .items li', function () {
                dataLayer.push({
                    'event': 'addToProjects',
                    'target': $('.catalog-product-view .page-title-wrapper span').text()
                });
            });

            //Download Click
            bodyElement.on('click', 'button.pz-btn-cart', function () {
                dataLayer.push({
                    'event': 'DownloadProductImage'
                });
            });
        }

        //Cart page
        if (bodyElement.hasClass('checkout-cart-index')) {
            $('div.block.crosssell  a').addClass('rec-product');
        }

        // Marketing opt-in
        bodyElement.on('click', 'footer .mgz-newsletter-btn', function () {
            if ($('.mgz-newsletter-form .email input').val()) {
                dataLayer.push({
                    'event': 'marketingOpt'
                });
            }
        });

        // Page Footer

        $('footer.page-footer a').addClass('footer-link');
        bodyElement.on('click', 'footer.page-footer a', function () {
            dataLayer.push({
                'event': 'footer-link'
            });
        });

        //Page Header Menu
        $('nav.navigation a').addClass('mgz-image-link');
        bodyElement.on('click', 'nav.navigation a', function () {
            dataLayer.push({
                'event': 'homepage_button_click'
            });
        });

        //Home Page
        if (bodyElement.hasClass('cms-index-index')) {
            $('.column.main a').addClass('mgz-image-link');
            bodyElement.on('click', '.column.main a', function () {
                dataLayer.push({
                    'event': 'homepage_button_click'
                });
            });
        }

        if (bodyElement.hasClass('checkout-index-index')) {
            window.dataLayer = window.dataLayer || [];
            dataLayer.push({
                'event': 'checkoutShipping'
            });
            bodyElement.on('click', '#shipping-method-buttons-container button', function () {
                dataLayer.push({
                    'event': 'checkoutBilling'
                });
            });
        }

        // Search Result Display
        if (bodyElement.hasClass('catalogsearch-result-index')) {
            window.dataLayer = window.dataLayer || [];
            dataLayer.push({
                'event': 'searchResults',
                'source': $('.page-title span').text().split(' ').pop().replace("'", "")
            });
        }

        if (bodyElement.hasClass('cms-page-view')) {
            //content landing page
            dataLayer.push({
                'event': 'contentLandingView',
                'source': $('li.cms_page').text()
            });

            //content landing click
            bodyElement.on('click', '.page-wrapper a', function () {
                var self,pageTitle;

                self = $(this);
                pageTitle = self.text();

                if (self.find('span').length) {
                    pageTitle = self.find('span').text();
                }
                dataLayer.push({
                    'event': 'contentLandingClick',
                    'target': pageTitle
                });
            });
        }

        //Add All to cart in My Account page
        bodyElement.on('click', '.form-wishlist-items button.action.tocart', function () {
            var product = [];

            $(".products-grid.wishlist .product-items li").each(function () {
                var self,trimProductName;

                self = $(this);
                trimProductName = $.trim(self.find('.product-item-name').text());

                if (self.find('.attribute-section li:first-child .value').text()) {
                    product.push({
                        'id'  : self.find('.attribute-section li:first-child .value').text(),
                        'name':trimProductName,
                        'qty':self.find('.field.qty input').val(),
                        'price':self.find('.price-final_price li span.price').text().replace('$','')
                    });
                }
            });

            dataLayer.push({
                'event': 'addToCart',
                'target': { 'products': product }
            });
        });
    });
});
