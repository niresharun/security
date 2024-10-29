/**
 * Company Custom JS.
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<Vikramraj.Sahu@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */
require([
    'Magento_Ui/js/lib/validation/validator',
    'jquery',
    'mage/translate'
], function (validator, $) {
    'use strict';

    validator.addRule(
        'dba-custom-validation',
        function (value) {
            if ($('[name="information[is_dba]"]').is(':checked') && value.trim().length < 1) {
                return false;
            }

            return true;
        }
        , $.mage.__('This is a required field.')
    );

    validator.addRule(
        'no-of-stores-validation',
        function (value) {
            var business_type, validate;

            business_type = $('[name="information[business_type]"]').val();
            validate = false;
            switch (business_type) {
                case 'Retailer':
                    validate = true;
                    break;
                case 'Retailer + Interior Design':
                    validate = true;
                    break;
                default:
                    break;
            }

            if (validate && value.length <= 0) {
                return false;
            }

            return true;
        }
        , $.mage.__('This is a required field.')
    );

    validator.addRule(
        'sq-ft-per-store-validation',
        function (value) {
            var business_type, validate;

            business_type = $('[name="information[business_type]"]').val();
            validate = false;
            switch (business_type) {
                case 'Retailer':
                    validate = true;
                    break;
                case 'Retailer + Interior Design':
                    validate = true;
                    break;
                default:
                    break;
            }

            if (validate && value.length <= 0) {
                return false;
            }

            return true;
        }
        , $.mage.__('This is a required field.')
    );

    validator.addRule(
        'type-of-projects-validation',
        function (value) {
            var business_type, validate;

            business_type = $('[name="information[business_type]"]').val();
            validate = false;
            switch (business_type) {
                case 'Designer':
                case 'Commercial FF&E':
                case 'Commercial Purchasing Firm':
                case 'Commercial Property Owner':
                    validate = true;
                    break;
                case 'Retailer + Interior Design':
                    validate = true;
                    break;
                default:
                    break;
            }

            if (validate && value.length <= 0) {
                return false;
            }

            return true;
        }
        , $.mage.__('This is a required field.')
    );

    validator.addRule(
        'no-of-jobs-per-year-validation',
        function (value) {
            var business_type = $('[name="information[business_type]"]').val();

            var validate = false;
            switch (business_type) {
                case 'Designer':
                case 'Commercial FF&E':
                case 'Commercial Purchasing Firm':
                case 'Commercial Property Owner':
                    validate = true;
                    break;
                case 'Retailer + Interior Design':
                    validate = true;
                    break;
                default:
                    break;
            }

            if (validate && value.length <= 0) {
                return false;
            }

            return true;
        }
        , $.mage.__('This is a required field.')
    );
});
