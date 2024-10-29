/**
 * This module is used to create custom artwork catalogs
 * This file is used to include the additional JS file related to my-catalog functionality.
 *
 * @category: Magento
 * @package: Perficient/MyCatalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyCatalog JS
 */
var config = {
    map: {
        '*': {
            wendoverJS: 'Perficient_MyCatalog/js/wendover',
            catalogScriptJs: 'Perficient_MyCatalog/js/catalog-script',
            ddSlickJs: 'Perficient_MyCatalog/js/jquery-ddslick-min',
            jCarouselJs: 'Perficient_MyCatalog/js/jquery.jcarousel.min',
            jCarouselPaginationJs: 'Perficient_MyCatalog/js/jquery.jcarousel-pagination',
            touchPunch: 'Perficient_MyCatalog/js/jquery.ui.touch-punch.min'
        }
    },
    paths: {
        'owl.carousel': 'Perficient_MyCatalog/js/owl.carousel.min',
        'quick-catalog': 'Perficient_MyCatalog/js/quick-catalog'
    },
    shim: {
        'owl.carousel': ['jquery'],
        jCarouselJs: ['jquery'],
        jCarouselPaginationJs: ['jquery'],
        catalogScriptJs: ['jquery'],
        touchPunch: ['jquery'],
        'quick-catalog': {
            deps: ['jquery']
        }
    }
};
