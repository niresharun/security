/**
 * JS file for Perficient_Company module
 *
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<vikramraj.sahu@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */
var config = {
    map: {
        '*': {
            customercartrestrict: 'Perficient_Company/js/restrict_addtocart',
            perficient_company: 'Perficient_Company/js/company_registration'
        }
    },
    config: {
        mixins: {
            'Magento_Company/js/user-edit': {
                'Perficient_Company/js/user-edit-mixin': true
            },
            'Magento_Company/js/user-delete': {
                'Perficient_Company/js/user-delete-mixin': true
            }
        }
    },
    deps: [
        'customercartrestrict'
    ]
};
