<?php
/**
 * RequisitionList Converted to Market Scans with project specific configurations
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Tahir Aziz <tahir.aziz@perficient.com>
 * @project: Wendover
 * @keywords:  Module Perficient_ViewInRoom
 */

declare(strict_types=1);

namespace Perficient\ViewInRoom\Controller\Adminhtml\Category\Image;

use Magento\Framework\Controller\ResultFactory;

/**
 *  Adminhtml Category Image Upload Controller
 */
class Upload extends \Magento\Backend\App\Action
{
    /**
     * Media directory object (writable).
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    /**
     * Upload constructor.
     */
    public function __construct(
        \Magento\Backend\App\Action\Context                               $context,
        protected \Magento\Catalog\Model\ImageUploader                    $imageUploader,
        private readonly \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem                                     $filesystem,
        protected \Magento\Store\Model\StoreManagerInterface              $storeManager,
        protected \Magento\MediaStorage\Helper\File\Storage\Database      $coreFileStorageDatabase,
        protected \Psr\Log\LoggerInterface                                $logger
    )
    {
        parent::__construct($context);
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Vendor_Module::category');
    }

    /**
     * Upload file controller action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $result = $this->imageUploader->saveFileToTmpDir('vir_background_img');
            $result['cookie'] = [
                'name' => $this->_getSession()->getName(),
                'value' => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path' => $this->_getSession()->getCookiePath(),
                'domain' => $this->_getSession()->getCookieDomain(),
            ];
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
