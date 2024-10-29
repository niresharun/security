<?php
/**
 * This module is used to create custom artwork catalogs
 *
 * @category: Magento
 * @package: Perficient/MyCatalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyCatalog
 */
declare(strict_types=1);

namespace Perficient\MyCatalog\Controller;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;

/**
 * Class AbstractAction
 * @package Perficient\MyCatalog\Controller
 */
abstract class AbstractAction implements ActionInterface
{
    /**
     * AbstractAction constructor.
     *
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlInterface $url
     */
    public function __construct(
        protected PageFactory $resultPageFactory,
        protected Session $customerSession,
        protected UrlInterface $url
    ) {
    }

    /**
     * Method used to validate customer, whether user is logged-in or not.
     * If not then redirect to login page.
     */
    public function validateCustomer()
    {
        if (!$this->customerSession->isLoggedIn()) {
            $this->customerSession->setAfterAuthUrl($this->url->getCurrentUrl());
            $this->customerSession->authenticate();
        }
    }
}
