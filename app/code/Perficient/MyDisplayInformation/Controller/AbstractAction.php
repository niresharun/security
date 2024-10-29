<?php
/**
 * This module is used by employee who can add/update his personal information which needs to display his customers
 * @category: Magento
 * @package: Perficient/MyDisplayInformation
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyDisplayInformation
 */
declare(strict_types=1);

namespace Perficient\MyDisplayInformation\Controller;

use Magento\Customer\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class AbstractAction
 * @package Perficient\MyCatalog\Controller
 */
abstract class AbstractAction implements ActionInterface
{
    /**
     * AbstractAction constructor.
     */
    public function __construct(
        private readonly PageFactory $resultPageFactory,
        private readonly Session $customerSession,
        private readonly UrlInterface $url
    ) {
    }

    /**
     * validate customer
     */
    public function validateCustomer()
    {
        $this->customerSession->getCustomer()->getId();
        if (!$this->customerSession->isLoggedIn()) {
            $this->customerSession->setAfterAuthUrl($this->url->getCurrentUrl());
            $this->customerSession->authenticate();
        }
    }
}
