<?php
/**
 * This module is used to download custom artwork catalogs.
 * This file contains the code to download PDF.
 *
 * @category: Magento
 * @package: Wendover/MyCatalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Wendover_MyCatalog
 */
declare(strict_types=1);

namespace Wendover\MyCatalog\Controller\Pdf;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Pdf
 * @package Wendover\MyCatalog\Controller\Index
 */
class Download extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        private readonly RequestInterface $request,
        private readonly \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
		\Magento\Framework\View\Result\PageFactory $pageFactory)
	{
		$this->_pageFactory = $pageFactory;
		return parent::__construct($context);
	}

	public function execute()
	{
        $resultPage =   $this->_pageFactory->create();
        return $resultPage;

	}

}
