<?php
/**
 * Display syspro_customer_id attribute on customer listing.
 *
 * @category: Magento
 * @package: Perficient/Rabbitmq
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Rabbitmq
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class SysproCustomerId
 * @package Perficient\Rabbitmq\Ui\Component\Listing\Column
 */
class SysproCustomerId extends Column
{
    /**
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                if (empty($item[$fieldName])) {
                    $item[$fieldName] = '';
                }
            }
        }
        return $dataSource;
    }
}