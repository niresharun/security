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

/**
 * Ip-address grid filter
 */
namespace Perficient\Reports\Block\Adminhtml\Grid\Filter;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Filter\Text;
use Magento\Logging\Model\ResourceModel\Helper;

class Ip extends Text
{
    /**
     * Construct
     *
     * @param Context $context
     * @param Helper $resourceHelper
     */
    public function __construct(
        Context $context,
        Helper $resourceHelper,
        array $data = []
    ) {
        parent::__construct($context, $resourceHelper, $data);
    }

    /**
     * Collection condition filter getter
     *
     * @return array
     */
    public function getCondition()
    {
        $value = $this->getValue();
        if (preg_match('/^(\d+\.){3}\d+$/', (string) $value)) {
            return ip2long($value);
        }

        $likeExpression = $this->_resourceHelper->addLikeEscape($value, ['position' => 'any']);
        return ['ntoa' => $likeExpression];
    }
}
