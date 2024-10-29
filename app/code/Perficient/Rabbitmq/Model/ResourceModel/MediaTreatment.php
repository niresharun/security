<?php
/**
 * Custom Table Data Management
 * @category: Magento
 * @package: Perficient/Rabbitmq
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Rabbitmq
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class MediaTreatment
 * @package Perficient\Rabbitmq\Model\ResourceModel
 */
class MediaTreatment extends AbstractDb
{
    /**
     * MediaTreatment constructor.
     * @param Context $context
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('media_treatment', 'media_treatment_id');
    }
}
