<?php
/**
 * Log Company Change Information
 * @category: Magento
 * @package: Perficient/Reports
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<Vikramraj.Sahu@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Reports
 */

declare(strict_types=1);

namespace Perficient\Reports\Block\Adminhtml\Details;

use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Framework\Registry;
use Perficient\Reports\Block\Adminhtml\Details\Renderer\Diff;
use Perficient\Reports\Block\Adminhtml\Details\Renderer\Sourcename;
use Perficient\Reports\Model\ResourceModel\PerficientLoggingEventChanges\Collection as PerficientLoggingEventChangesCollection;

/**
 * Present Information on Grid
 */
class Grid extends Extended
{
    /**
     * @param Context $context
     * @param Data $backendHelper
     * @param Registry $_coreRegistry
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        protected PerficientLoggingEventChangesCollection $collectionFactory,
        protected Registry $_coreRegistry,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize default sorting and html ID
     *
     * @return void
     */
    protected function _construct()
    {
        $this->setId('changeLogDetailsGrid');
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    /**
     * Prepare grid collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $changeLog = $this->_coreRegistry->registry('current_change');
        $collection = $this->collectionFactory->addFieldToFilter('event_id', $changeLog->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'source_name',
            [
                'header' => __('Source Data'),
                'sortable' => false,
                'renderer' => Sourcename::class,
                'index' => 'source_name',
                'width' => 1
            ]
        );

        $this->addColumn(
            'original_data',
            [
                'header' => __('Value Before Change'),
                'sortable' => false,
                'renderer' => Diff::class,
                'index' => 'original_data'
            ]
        );

        $this->addColumn(
            'result_data',
            [
                'header' => __('Value After Change'),
                'sortable' => false,
                'renderer' => Diff::class,
                'index' => 'result_data'
            ]
        );

        return parent::_prepareColumns();
    }
}
