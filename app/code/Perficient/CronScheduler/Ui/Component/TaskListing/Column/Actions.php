<?php
/**
 * Displays detailed information of the cron
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Ui\Component\TaskListing\Column;

use Magento\Cron\Model\Schedule;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Perficient\CronScheduler\Helper\Url;

/**
 * Class Actions
 * @package Perficient\CronScheduler\Ui\Component\TaskListing\Column
 */
class Actions extends Column
{
    private string $viewUrl = Url::TASK_VIEW;

    /**
     * Class constructor
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        protected UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['schedule_id']) && $item['status'] != Schedule::STATUS_PENDING) {
                    $url = $this->urlBuilder->getUrl($this->viewUrl);
                    $item[$name]['view_more'] = [
                        'href' => "javascript:void(require(['cs_task'], function (task) {
                         task.view('" . $url . "','" . $item['schedule_id'] . "'); }))",
                        'label' => __('View More'),
                    ];
                }
            }
        }
        return $dataSource;
    }
}
