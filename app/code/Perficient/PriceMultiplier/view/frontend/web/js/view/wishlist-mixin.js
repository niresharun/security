/**
 * Load real time price for widget
 *
 * @category: JS
 * @package: Perficient/PriceMultiplier
 * @copyright: Copyright © 2020 Magento. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude <sandeep.mude@perficient.com>
 * @keywords: Module Perficient_PriceMultiplier
 */
define([
        'Perficient_PriceMultiplier/js/storecatalogproduct',
        'Perficient_Company/js/restrict_addtocart'
    ],
    function (storecatalog, restrictAddtocart) {
        'use strict';
        var mixin = {

            /**
             * Load real time price for widget
             */
            callPrice: function () {
                storecatalog.fetchWidgetPrice();
                restrictAddtocart.restrictWidgetAddtoCart();
            }
        };

        return function (target) { // target == Result that Magento_Ui/.../columns returns.
            return target.extend(mixin); // new result that all other modules receive
        };
    });