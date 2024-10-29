/**
 * Add sets to My Projects (Wishlist)
 * @category: Magento
 * @package: Perficient/WishlistSet
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<Sandeep.mude@Perficient.com>
 * @keywords: Module Perficient_WishlistSet
 */
var config = {
    map: {
        '*': {
            addtowishlistcollection: 'Perficient_WishlistSet/js/addtowishlistcollection'
        }
    },
    deps: [
        "addtowishlistcollection"
    ]
};