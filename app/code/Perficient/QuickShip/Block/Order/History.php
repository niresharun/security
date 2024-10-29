<?php
/**
 * Inventory for Quick Ship
 * @category: Magento
 * @package: Perficient/QuickShip
 * @copyright: Copyright 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude <Sandeep.mude@perficient.com>
 * @keywords: Module Perficient_QuickShip
 */
declare(strict_types=1);

namespace Perficient\QuickShip\Block\Order;

use Magento\Sales\Block\Order\History as OrderHistory;
class History extends OrderHistory
{
    /**
     * @var string
     */
    protected $_template = 'Perficient_QuickShip::order/history.phtml';

}