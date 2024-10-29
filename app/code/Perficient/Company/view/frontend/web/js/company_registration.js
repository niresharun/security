<!--
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
-->
require([
    'jquery',
    'domReady!'
], function ($) {
    'use strict';

    (function ($){

        var business_info = $('#business_info');

        $(document).ready(function () {

            // Toggle As per DBA CheckBox

            $('input[name="company[is_dba]"]').change(function () {
                if ($(this).val() === 'yes') {
                    $('div.dba_name').addClass('required');
                    $('div.dba_name').show();
                    $('#dba_name').attr('data-validate', '{required:true}');

                } else {
                    $('#dba_name').val('');
                    $('div.dba_name').removeClass('required');
                    $('div.dba_name').hide();
                    $('#dba_name').attr('data-validate','');
                }
            });
            // Show customer registration field based on BusinessType
            business_info.on('change', function () {
                var no_of_stores, sq_ft_per_store, type_of_projects, no_of_jobs_per_year, business_type;

                no_of_stores = $('div.no_of_stores');
                sq_ft_per_store = $('div.sq_ft_per_store');
                type_of_projects = $('div.type_of_projects');
                no_of_jobs_per_year = $('div.no_of_jobs_per_year');
                business_type = $(this).children("option:selected").val();

                no_of_stores.hide();
                sq_ft_per_store.hide();
                type_of_projects.hide();
                no_of_jobs_per_year.hide();
                type_of_projects.removeClass('required');
                no_of_jobs_per_year.removeClass('required');
                $('#type_of_projects').attr('data-validate', '');
                $('#no_of_jobs_per_year').attr('data-validate', '');

                $('#type_of_projects optgroup').each(function () {
                    $(this).show();
                });

                switch (business_type) {
                    case 'Designer':
                    case 'Commercial FF&E':
                    case 'Commercial Purchasing Firm':
                    case 'Commercial Property Owner':
                        type_of_projects.show();
                        no_of_jobs_per_year.show();

                        type_of_projects.addClass('required');
                        no_of_jobs_per_year.addClass('required');
                        $('#type_of_projects').attr('data-validate', '{required:true}');
                        $('#no_of_jobs_per_year').attr('data-validate', '{required:true}');

                        $('#no_of_stores').val('');
                        $('#sq_ft_per_store').val('');

                        if (business_type !== 'Designer') {
                            $('#type_of_projects  optgroup option:selected').removeAttr('selected');
                            $('#type_of_projects optgroup').each(function () {
                                if ($(this).data('type') === 'residential') {
                                    $(this).hide();
                                }
                            });
                        }

                       break;
                    case 'Retailer':
                        no_of_stores.show();
                        sq_ft_per_store.show();

                        no_of_stores.addClass('required');
                        sq_ft_per_store.addClass('required');
                        $('#no_of_stores').attr('data-validate', '{required:true}');
                        $('#sq_ft_per_store').attr('data-validate', '{required:true}');

                        $('#type_of_projects  optgroup option:selected').removeAttr('selected');
                        $('#no_of_jobs_per_year option:selected').removeAttr('selected');


                        break;
                    case 'Retailer + Interior Design':
                        type_of_projects.show();
                        no_of_jobs_per_year.show();
                        no_of_stores.show();
                        sq_ft_per_store.show();

                        type_of_projects.addClass('required');
                        no_of_jobs_per_year.addClass('required');
                        $('#type_of_projects').attr('data-validate', '{required:true}');
                        $('#no_of_jobs_per_year').attr('data-validate', '{required:true}');

                        no_of_stores.addClass('required');
                        sq_ft_per_store.addClass('required');
                        $('#no_of_stores').attr('data-validate', '{required:true}');
                        $('#sq_ft_per_store').attr('data-validate', '{required:true}');
                        break;
                    default:
                        break;
                }
            });

            business_info.change();
        });
    })(jQuery);

});
