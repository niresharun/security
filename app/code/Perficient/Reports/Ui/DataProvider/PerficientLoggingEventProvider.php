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

namespace Perficient\Reports\Ui\DataProvider;

use Magento\Company\Api\Data\CompanyInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Perficient\Reports\Model\ResourceModel\PerficientLoggingEvent\CollectionFactory;

/**
 * Class PerficientLoggingEventProvider
 * @package Perficient\Reports\Ui\DataProvider
 */
class PerficientLoggingEventProvider extends AbstractDataProvider
{
    /**
     * @var
     */
    protected $collection;

    /**
     * PerficientLoggingEventProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        protected CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    )
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $this->collectionFactory->create();
        $this->collection->addFilter('entity_model', CompanyInterface::class)->load();
    }

}
