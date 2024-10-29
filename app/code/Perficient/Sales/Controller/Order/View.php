<?php
/**
 * Modify Customer Account Sales Order Navigation
 * @category: Magento
 * @package: Perficient/Sales
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani <Manish.Bhojwani@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Sales
 */
declare(strict_types=1);

namespace Perficient\Sales\Controller\Order;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Sales\Controller\OrderInterface;

class View extends \Perficient\Sales\Controller\AbstractController\View implements OrderInterface, HttpGetActionInterface
{
}
