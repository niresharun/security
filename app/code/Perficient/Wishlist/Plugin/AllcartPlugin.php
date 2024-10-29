<?php
/**
 * overide for collaboration
 * @category: Magento
 * @package: Perficient/Wishlist
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<Sandeep.mude@Perficient.com>
 * @keywords: Module Perficient_Wishlist
 */
declare(strict_types=1);

namespace Perficient\Wishlist\Plugin;

use Magento\Wishlist\Controller\Index\Allcart;
use Magento\Framework\Controller\ResultFactory;

class AllcartPlugin
{
    /**
     * AllcartPlugin constructor.
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        private readonly ResultFactory $resultFactory
    )
    {
    }

    /**
     * @param Allcart $subject
     * @param $result
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function afterExecute(Allcart $subject, $result)
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('checkout/cart');
        return $resultRedirect;

    }

}
