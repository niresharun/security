/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'mage/url',
    'Magento_Ui/js/grid/paging/paging'
], function ($,Ko,urlBuilder,gridPaging) {
    'use strict';

    return gridPaging.extend({
        defaults: {
            template: 'Magento_B2b/grid/paging/paging',
            sizesConfig: {
                template: 'Magento_B2b/grid/paging/sizes'
            }
        },

        /**
         * @return {Number}
         */
        getFirstNum: function () {
            return this.pageSize * (this.current - 1) + 1;
        },

        /**
         * @return {*}
         */
        getLastNum: function () {

            if (this.isLast()) {
                return this.totalRecords;
            }

            return this.pageSize * this.current;
        },

        /**
         * Updates number of pages.
         */
        updateCounter: function () {
            /**
             * Fixes for ticket WENDOVER-506 pagination not showing
             * getting value from cookie and assigning to totalRecord
             * if module is company and controller is users
             */
            var url = window.location.pathname;
            var splittedUrl = url.split('/');
            if (Array.isArray(splittedUrl)
                && splittedUrl[1] !== undefined && splittedUrl[1] == "company"
                && splittedUrl[2] !== undefined && splittedUrl[2] == "users") {
                if ($.cookie('customerOrEmpTotalRecords')) {
                    this.totalRecords = $.cookie('customerOrEmpTotalRecords');
                }
                $("html, body").animate({scrollTop: 0}, "fast");
            }

            this.pages = Math.ceil(this.totalRecords / this.pageSize ) || 1;
            return this;
        },

        /**
         * @return {Array}
         */
        getPages: function () {
            var pagesList = [],
                i;

            for (i = 1; i <= this.pages; i++) {
                pagesList.push(i);
            }

            return pagesList;
        }

    });
});
