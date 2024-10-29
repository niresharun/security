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
define([
    'jquery',
    'Magento_Ui/js/form/element/select'
], function ($, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            customName: '${ $.parentName }.${ $.index }_input',
            selectedValue: null,
            count: 0
        },
        loadJsCustomAfterKoRender: function () {
            //setTimeout(function(){ $('select[name="information[business_type]"]').change(); },1000);
        },
        initialize: function () {
            this.selectedValue = this._super().initialValue;
        },

        selectOption: function (id) {
            var business_type = this.selectedValue;

            this.count += 1;

            if (this.count <= 1) {
                return;
            }

            if (typeof business_type === 'undefined' || business_type === null) {
                business_type = $('#' + id).val();
            }

            this.selectedValue = null;


            if (typeof business_type === 'undefined') {
                return;
            }

            var no_of_stores, sq_ft_per_store, type_of_projects, no_of_jobs_per_year;

            no_of_stores = $('div[data-index=no_of_stores]');
            sq_ft_per_store = $('div[data-index=sq_ft_per_store]');
            type_of_projects = $('div[data-index=type_of_projects]');
            no_of_jobs_per_year = $('[data-index=no_of_jobs_per_year]');

            no_of_stores.hide();
            sq_ft_per_store.hide();
            type_of_projects.hide();
            no_of_jobs_per_year.hide();

            $('select[name="information[type_of_projects]"] optgroup').each(function () {
                $(this).show();
            });

            $('select[name="information[type_of_projects]"] option').each(function () {
                $(this).show();
            });

            switch (business_type) {
                case 'Designer':
                case 'Commercial FF&E':
                case 'Commercial Purchasing Firm':
                case 'Commercial Property Owner':
                    type_of_projects.show();
                    no_of_jobs_per_year.show();

                    //$('[name="information[no_of_stores]"]').val('');
                    // $('[name="information[sq_ft_per_store]"]').val('');

                    if (business_type !== 'Designer') {
                        $('select[name="information[type_of_projects]"] optgroup').each(function () {
                            if ($(this).prop('label') === 'Residential') {
                                $(this).hide();
                            }
                        });

                        $('select[name="information[type_of_projects]"] option').each(function () {
                            if ($(this).prop('value') === 'Residential' ||
                                $(this).prop('value') === 'Model Home / Staging' ||
                                $(this).prop('value') === 'Set Design') {
                                $(this).hide();
                            }
                        });
                    }

                    break;
                case 'Retailer':
                    no_of_stores.show();
                    sq_ft_per_store.show();

                    //$('[name="information[type_of_projects]"] option:selected').removeAttr("selected");
                    //$('[name="information[no_of_jobs_per_year]"] option:selected').removeAttr("selected");

                    break;
                case 'Retailer + Interior Design':
                    type_of_projects.show();
                    no_of_jobs_per_year.show();
                    no_of_stores.show();
                    sq_ft_per_store.show();
                    break;
                default:
                    break;
            }
        }
    });
});

