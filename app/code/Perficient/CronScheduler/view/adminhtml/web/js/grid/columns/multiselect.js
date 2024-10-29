/**
 * To select multiple cron job to schedule or run or disable
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
define([
    'underscore',
    'mage/translate',
    'Magento_Ui/js/grid/columns/multiselect'
], function (_, $t, Column) {
    'use strict';

    return Column.extend({
        selectAll: function () {
            this.clearExcluded()
                .selectPage();
            return this;
        }
    });
});
