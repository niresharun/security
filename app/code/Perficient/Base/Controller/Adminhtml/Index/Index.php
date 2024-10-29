<?php
/**
 * This module is used to bulk image upload
 *
 * @category:  Magento
 * @package:   Perficient/Base
 * @copyright: Copyright  - 2020 Magento, Inc. All rights reserved.
 * @license:   Magento Enterprise Edition (MEE) license
 * @author:    Vijayashanthi M
 * @project:   Wendover
 * @keywords:  Module Perficient_Base
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Perficient\Base\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Result
 *
 * @package Perficient\Base\Controller\Adminhtml\Index
 */
class Index extends Action
{

    final const ADMIN_RESOURCE = 'Magento_UrlRewrite::urlrewrite';

    /**
     * Constructor
     *
     * @param PageFactory $pageFactory
     * @param Context $context
     */
    public function __construct(
        private readonly PageFactory $pageFactory,
        Action\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Execute method.
     *
     * @return null
     */
    public function execute()
    {
        $page = $this->pageFactory->create();
        $page->setActiveMenu('Magento_UrlRewrite::urlrewrite');
        $page->getConfig()->getTitle()->set('Something');
        return $page;
    }

}
