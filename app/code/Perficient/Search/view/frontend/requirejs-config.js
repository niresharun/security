/**
 * Modify catalog product search
 * @category: Magento
 * @package: Perficient/Search
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<Vikramraj.Sahu@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Search
 */
var config = {
    config: {
        mixins: {
            'Magento_Search/js/form-mini': {
                'Perficient_Search/js/mixins/form-mini': true
            }

        }
    }
};