/**
 *
 * This file used to changes in search list page
 *
 * @category: Magento
 * @package: Perficient/UI
 * @copyright: Copyright  - 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: purushottam rathi <hpurushottam.athi@Perficient.com>
 * @project: Wendover
 * @keywords: Module UI WCAG Focus changes
 */

/**
 * @api
 */
define([
    'jquery',
    'underscore',
    'jquery-ui-modules/widget',
    'Magento_Ui/js/modal/confirm',
    'mage/translate'
], function ($, _) {
    'use strict';

    $.widget('mage.alert', $.mage.confirm, {
        options: {
            modalClass: 'confirm',
            title: $.mage.__('Attention'),
            focus: '.action-close',
            actions: {

                /**
                 * Callback always - called on all actions.
                 */
                always: function () {}
            },
            buttons: [{
                text: $.mage.__('OK'),
                class: 'action-primary action-accept',

                /**
                 * Click handler.
                 */
                click: function () {
                    this.closeModal(true);
                }
            }]
        },

        /**
         * Close modal window.
         */
        closeModal: function () {
            this.options.actions.always();
            this.element.bind('alertclosed', _.bind(this._remove, this));

            return this._super();
        }
    });

    return function (config) {
        return $('<div></div>').html(config.content).alert(config);
    };
});
