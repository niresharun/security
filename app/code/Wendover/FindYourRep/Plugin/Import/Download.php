<?php

namespace Wendover\FindYourRep\Plugin\Import;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Module\Dir\Reader;

class Download
{
    /**
     * @param RequestInterface $requestInterface
     * @param Reader $reader
     * @param ReadFactory $readFactory
     * @param ManagerInterface $messageManager
     * @param RedirectFactory $resultRedirectFactory
     * @param FileFactory $fileFactory
     * @param RawFactory $resultRawFactory
     */
    public function __construct(
        private readonly RequestInterface $requestInterface,
        private readonly Reader           $reader,
        private readonly ReadFactory      $readFactory,
        private readonly ManagerInterface $messageManager,
        private readonly RedirectFactory  $resultRedirectFactory,
        private readonly FileFactory      $fileFactory,
        private readonly RawFactory       $resultRawFactory
    )
    {
    }

    /**
     * @param $subject
     * @param $result
     * @return Redirect|Raw|\Magento\Framework\Controller\Result\Redirect|mixed
     * @throws FileSystemException
     * @throws ValidatorException
     */
    public function afterExecute(
        $subject,
        $result
    )
    {
        if ($this->requestInterface->getParam('filename') == 'find_your_rep_import') {
            $fileName = 'Rep_Upload_Template.csv';
            $moduleDir = $this->reader->getModuleDir('', 'Wendover_FindYourRep');
            $fileAbsolutePath = $moduleDir . '/Files/Sample/' . $fileName;
            $directoryRead = $this->readFactory->create($moduleDir);
            $filePath = $directoryRead->getRelativePath($fileAbsolutePath);

            if (!$directoryRead->isFile($filePath)) {
                /** @var Redirect $resultRedirect */
                $this->messageManager->addErrorMessage(__('There is no sample file for this entity.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/import');
                return $resultRedirect;
            } else {
                $this->messageManager->getMessages(true);
            }

            $fileSize = $directoryRead->stat($filePath)['size'] ?? null;

            $this->fileFactory->create(
                $fileName,
                null,
                DirectoryList::VAR_DIR,
                'application/octet-stream',
                $fileSize
            );

            $resultRaw = $this->resultRawFactory->create();
            $resultRaw->setContents($directoryRead->readFile($filePath));
            return $resultRaw;
        }

        return $result;
    }
}
