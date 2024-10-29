/**
 * PriceMultiplier module for multiplier price .
 *
 * @category:  JS
 * @package:   Perficient/PriceMultiplier
 * @copyright:
 * See COPYING.txt for license details.
 * @author: Sandeep Mude <sandeep.mude@perficient.com>
 * @keywords:  Module Perficient_PriceMultiplier
 */
var config = {
    map: {
        '*': {
            storecatalogproduct: 'Perficient_PriceMultiplier/js/storecatalogproduct',
            'Magento_Catalog/template/product/price/regular_price.html': 'Perficient_PriceMultiplier/template/product/price/regular_price.html',
            'Magento_Catalog/template/product/price/special_price.html': 'Perficient_PriceMultiplier/template/product/price/special_price.html',
        }
    },
    config: {
        mixins: {
            'Magento_Wishlist/js/view/wishlist': {
                'Perficient_PriceMultiplier/js/view/wishlist-mixin': true
            }
        }
    },
    deps: [
        "storecatalogproduct"
    ]

};