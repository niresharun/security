/**
 * Display tasks list
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */

/**
 * Open a task in a modal window
 * @param {type} $
 * @returns {task_L10.taskAnonym$1}
 */
define(['jquery', 'Magento_Ui/js/modal/modal'], function ($) {
    'use strict';
    return {
        /**
         * OPen the modal window with the task information
         * @param string url
         * @param int schedule_id
         * @returns void
         */
        view: function (url, schedule_id) {
            var indices = ['schedule_id', 'job_code', 'status', 'created_at',
                'scheduled_at', 'executed_at', 'finished_at', 'messages'];

            $('#task-view').modal({
                'type': 'slide',
                'title': '',
                'modalClass': 'mage-new-category-dialog form-inline',
                buttons: []
            });

            for (var indice in indices) {
                $('#task-view #tr-task-' + indices[indice]).css({display: 'none'});
            }
            $('#task-view #task-status').attr('class', '');
            $('#task-view').modal('openModal');
            $.ajax({
                url: url,
                data: {schedule_id: schedule_id},
                type: 'POST',
                showLoader: true,
                success: function (data) {
                    if (typeof data.error !== 'undefined') {
                        $('#task-view table').css({display: 'none'});
                        $('#task-view #error').html(data.error);
                    } else {
                        $('#task-view table').css({display: 'table'});
                        $('#task-view #error').html('');

                        if (typeof data.schedule_id !== 'undefined') {
                            for (var indice in indices) {
                                if (indices[indice] !== 'status') {
                                    $('#task-view #task-' + indices[indice]).html(data[indices[indice]]);
                                }
                                if (data[indices[indice]] === '' || data[indices[indice]] === null) {
                                    $('#task-view #tr-task-' + indices[indice]).css({display: 'none'});
                                } else {
                                    $('#task-view #tr-task-' + indices[indice]).css({display: 'table-row'});
                                }
                            }
                        }
                        if (data['status'][0] === 'major') {
                            $('#task-view #tr-task-error_file').css({display: 'table-row'});
                            $('#task-view #tr-task-error_line').css({display: 'table-row'});
                        } else {
                            $('#task-view #tr-task-error_file').css({display: 'none'});
                            $('#task-view #tr-task-error_line').css({display: 'none'});
                        }

                        $('#task-view #task-status').html(data['status'][1]);
                        $('#task-view #task-status').attr("class", "grid-severity-" + data['status'][0]);
                    }
                },
                error: function (data) {
                    $('#task-view').html(data.responseText);
                }
            });
        }
    };
});
