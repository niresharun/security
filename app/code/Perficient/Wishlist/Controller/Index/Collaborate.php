<?php
/**
 * Controller for collaboration
 * @category: Magento
 * @package: Perficient/Wishlist
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<Sandeep.mude@Perficient.com>
 * @keywords: Module Perficient_Wishlist
 */
declare(strict_types=1);

namespace Perficient\Wishlist\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session;

class Collaborate extends \Magento\Wishlist\Controller\AbstractIndex
{
    /**
     * Collaborate constructor.
     * @param Context $context
     */
    public function __construct(
        Context           $context,
        protected Session $customerSession
    )
    {
        parent::__construct($context);
    }

    /**
     * Prepare wishlist for share
     *
     * @return void|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->customerSession->authenticate()) {
            /** @var \Magento\Framework\View\Result\Page $resultPage */
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
            return $resultPage;
        }
    }
}
