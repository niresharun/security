<!--
/**
* Company Custom JS.
* @category: Magento
* @package: Perficient/Company
* @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
* See COPYING.txt for license details.
* @license: Magento Enterprise Edition (MEE) license
* @author: Sachin Badase <Sachin.Badase@Perficient.com>
* @project: Wendover
* @keywords: Module Perficient_Company
*/
-->
define([
    'jquery'
], function ($) {
    'use strict';
    return function (widget) {
        $.widget('mage.userEdit', widget, {
            /**
             * Callback for edit event
             *
             * @param {Object} e
             * @public
             */
            editUser: function (e) {
                let currentLocation,dynamicLabelAdd,dynamicLabelEdit,title;

				currentLocation = String(window.location).indexOf('emp');
                dynamicLabelAdd = 'Add New Customer';
                dynamicLabelEdit = 'Edit Customer';
                title = this.options.id ? $.mage.__(dynamicLabelEdit) : $.mage.__(dynamicLabelAdd);

                if (currentLocation > 0) {
                 dynamicLabelAdd = 'Add New Employee';
                 dynamicLabelEdit = 'Edit Employee';
                }
                if (e) {
                    e.preventDefault();
                }
                this.options.popup.modal('setTitle', title);
                this.options.popup.modal('openModal');
                this._populateForm();
                this._setIdFields();
                if (!this.options.id) {
                    this._filterRoles('role');
                }
            }, /**
             * Fill roles input field.
             *
             * @param {String} name
             * @param {String} value
             * @private
             */
            _filterRoles: function (name, value) {
                var selectRoles = this.options.popup.find(this.options.roleSelect),
                    statusSelect = this.options.popup.find(this.options.statusSelect),
                    optionsRole = selectRoles.find('option'),
                    adminRole = selectRoles.find('[value=' + this.options.adminUserRoleId + ']'),
                    condition = value === this.options.adminUserRoleId;

                if (_.isUndefined(value)) {
                    selectRoles.prop('disabled', condition);
                    statusSelect.prop('disabled', condition);
                } else {
                    if ($('#jobtitle').val()) {
                        $('#role').attr('disabled', 'disabled');
                    } else {
                        let checkIfAdmin = 'is_administrator_' + $('#check_add_edit').val();

                        $('#role').attr('disabled', 'disabled');
                    }
                    selectRoles.prop('disabled', true);
                    statusSelect.prop('disabled', true);
                }
                optionsRole.toggle(!condition);
                adminRole.toggle(condition);
                adminRole.attr('disabled', condition ? 'disabled' : '');
            },

            /**
             * populate edit field
             * @private
             */
            _populateForm: function () {
                var self = this;

                this.showAdditionalFields(!this.options.id);
                this.options.popup.find('input').val('');

                if (!this.options.isAjax && this.options.id) {
                    this.options.isAjax = true;

                    this.options.popup.addClass('unpopulated');
                    this.options.popup.find('input').attr('disabled', true);

                    $.ajax({
                        url: self.options.getUserUrl,
                        type: 'get',
                        showLoader: true,

                        /**
                         * @callback
                            */
                        success: $.proxy(function (data) {
                            var that = this, priceMultiplierAbsent = true;

                            this.options.popup.find('input').attr('disabled', false);

                            if (data.status === 'ok') {
                                $.each(data.data, function (idx, item) {
                                    console.log(idx);
                                    if (idx === 'custom_attributes') {
                                        $.each(item, function (name, itemData) {
                                            that._setPopupFields(itemData['attribute_code'], itemData.value);
                                            if (name === 'price_multiplier') {
                                                priceMultiplierAbsent = false;
                                            }
                                        });
                                    }

                                    that._setPopupFields(idx, item);
                                });
                                if (priceMultiplierAbsent) {
                                    that._setPopupFields('price_multiplier', 0);
                                }
                                this.options.popup.removeClass('unpopulated');
                            }

                        }, this),

                        /**
                         * @callback
                            */
                        complete: function () {
                            self.options.isAjax = false;
                        }
                    });
                }
            }
        });
        return $.mage.userEdit;
    };
});
