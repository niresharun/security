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
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/single-checkbox',
    'ko'
], function (_, uiRegistry, select, ko) {
    'use strict';

    return select.extend({

        initialize: function () {
            this._super();
            this.fieldDepend(this.value());
            return this;
        },

        onUpdate: function (value) {
            var field_dba_name = uiRegistry.get('index = dba_name');

            if (value === 'no') {
                field_dba_name.hide();
            } else {
                field_dba_name.show();
            }
            return this._super();
        },
        fieldDepend: function (value) {
            setTimeout(function () {
                var field_dba_name = uiRegistry.get('index = dba_name');


                if (value === 'no') {
                    field_dba_name.hide();
                } else {
                    field_dba_name.show();
                }
            });
        }
    });

});

