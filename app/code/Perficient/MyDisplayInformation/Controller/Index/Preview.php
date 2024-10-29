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

namespace Perficient\MyDisplayInformation\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Perficient\MyDisplayInformation\Controller\AbstractAction;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;

/**
 * Class Preview
 * @package Perficient\MyDisplayInformation\Controller\Index
 */
class Preview extends AbstractAction
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * Preview constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlInterface $url
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Session $customerSession,
        UrlInterface $url
    ) {
        parent::__construct(
            $resultPageFactory,
            $customerSession,
            $url
        );
        $this->pageFactory = $resultPageFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $page = $this->pageFactory->create();
        $page->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0', true);
        return $page;
    }
}