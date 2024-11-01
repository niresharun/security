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
    'mage/translate',
    'jquery-ui-modules/widget',
    'Magento_Ui/js/modal/modal'
], function ($, _, $t) {
    'use strict';

    $.widget('mage.confirm', $.mage.modal, {
        options: {
            modalClass: 'confirm',
            title: '',
            focus: '.action-close',
            actions: {

                /**
                 * Callback always - called on all actions.
                 */
                always: function () {},

                /**
                 * Callback confirm.
                 */
                confirm: function () {},

                /**
                 * Callback cancel.
                 */
                cancel: function () {}
            },
            buttons: [{
                text: $t('Cancel'),
                class: 'action-secondary action-dismiss',

                /**
                 * Click handler.
                 */
                click: function (event) {
                    this.closeModal(event);
                }
            }, {
                text: $t('OK'),
                class: 'action-primary action-accept',

                /**
                 * Click handler.
                 */
                click: function (event) {
                    this.closeModal(event, true);
                }
            }]
        },

        /**
         * Create widget.
         */
        _create: function () {
            this._super();
            this.modal.find(this.options.modalCloseBtn).off().on('click', _.bind(this.closeModal, this));
            this.openModal();
        },

        /**
         * Remove modal window.
         */
        _remove: function () {
            this.modal.remove();
        },

        /**
         * Open modal window.
         */
        openModal: function () {
            return this._super();
        },

        /**
         * Close modal window.
         */
        closeModal: function (event, result) {
            result = result || false;

            if (result) {
                this.options.actions.confirm(event);
            } else {
                this.options.actions.cancel(event);
            }
            this.options.actions.always(event);
            this.element.bind('confirmclosed', _.bind(this._remove, this));

            return this._super();
        }
    });

    return function (config) {
        return $('<div></div>').html(config.content).confirm(config);
    };
});
