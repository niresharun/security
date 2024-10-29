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
use \Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class Result
 *
 * @package Perficient\Catalog\Controller\Adminhtml\Index
 */
class ImgUpload extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Magento_Catalog::catalog';

    /**
     * Constructor
     *
     * @param Action\Context $context
     * @param JsonFactory $jsonResultFactory
     */
    public function __construct(
        private readonly \Magento\Framework\View\Result\PageFactory $pageFactory,
        protected \Magento\Store\Model\StoreManagerInterface        $storeManager,
        \Magento\Framework\Message\ManagerInterface                 $messageManager,
        protected \Perficient\Catalog\Block\GetData                 $getData,
        protected \Magento\Backend\Helper\Data                      $backendHelper,
        Action\Context                                              $context,
        private readonly JsonFactory                                $jsonResultFactory
    )
    {
        parent::__construct($context);
    }
    // end __construct


    /**
     * Execute Method
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(): \Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\Result\Redirect
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $params = $this->getRequest()->getParams();
        if (empty($params)) {
            $resultRedirect->setPath('productbulkupload/index/index/');
            return $resultRedirect;
        }
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');

        $this->getData->coreLogic();

        // Set the response.
        $response = [
            'success' => true,
            'message' => __('Bulk image import process completed!')
        ];
        $result = $this->jsonResultFactory->create();
        $result->setData($response);
        return $result;

    }
}
