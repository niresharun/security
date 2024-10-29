<?php
/**
 * This module is used to bulk image upload
 *
 * @category:  Magento
 * @package:   Perficient/Catalog
 * @copyright: Copyright  - 2020 Magento, Inc. All rights reserved.
 * @license:   Magento Enterprise Edition (MEE) license
 * @author:    Vijayashanthi M
 * @project:   Wendover
 * @keywords:  Module Perficient_Catalog
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Perficient\Catalog\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

/**
 * Class Result
 *
 * @package Perficient\Catalog\Controller\Adminhtml\Index
 */
class Index extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Magento_Catalog::catalog';

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\Backend\App\Action $context
     */
    public function __construct(
        private readonly \Magento\Framework\View\Result\PageFactory $pageFactory,
        Action\Context                                              $context
    )
    {
        parent::__construct($context);
    }
    // End __construct.

    /**
     * Execute method.
     *
     * @return null
     */
    public function execute()
    {
        $page = $this->pageFactory->create();
        $page->setActiveMenu('Magento_Catalog::catalog');
        return $page;
    }// End execute.

}//end class.
