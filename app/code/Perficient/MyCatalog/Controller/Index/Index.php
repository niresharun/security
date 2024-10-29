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

namespace Perficient\MyCatalog\Controller\Index;

use Perficient\MyCatalog\Controller\AbstractAction;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Translate\Inline\StateInterface as InlineTranslation;
use Perficient\MyCatalog\Model\Mail\TransportBuilder;
use Magento\Framework\Filesystem\Io\File as IoFile;
use Perficient\MyCatalog\Helper\Data;
use Perficient\MyCatalog\Block\Pdf;
use Perficient\MyCatalog\Api\MyCatalogRepositoryInterface;
use \Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Message\ManagerInterface;
use Wendover\MyCatalog\Helper\Pdf as ZendPdf;
use Wendover\Catalog\ViewModel\WendoverViewModel as CatalogViewModel;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Index
 * @package Perficient\MyCatalog\Controller\Index
 */
class Index extends AbstractAction
{
    /**
     * Constants for email.
     */
    const EMAIL_TEMPLATE = 'perficient_mycatalog/email/template';
    const EMAIL_SENDER   = 'perficient_mycatalog/email/sender';
    const EMAIL_SUBJECT  = 'perficient_mycatalog/email/subject';
    const EMAIL_CONTENT_ATTACHMENT  = 'perficient_mycatalog/email/content_attachment';
    const EMAIL_CONTENT_DOWNLOAD_LINK  = 'perficient_mycatalog/email/content_download_link';
    const EMAIL_ATTACHMENT_SIZE = 'perficient_mycatalog/email/attachment_size';

    /**
     * @var Mail
     */
    private $mailHelper;

    /**
     * Index constructor.
     *
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlInterface $urlInterface
     * @param RequestInterface $request
     * @param JsonFactory $jsonResultFactory
     * @param TransportBuilder $transportBuilder
     * @param InlineTranslation $inlineTranslation
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface $logger
     * @param DirectoryList $directoryList
     * @param MyCatalogRepositoryInterface $myCatalogRepository
     * @param CustomerFactory $customerFactory
     * @param Pdf $pdf
     * @param ManagerInterface $messageManager
     * @param Data $helper
     * @param ZendPdf $zendPdf
     */
    public function __construct(
        PageFactory                                   $resultPageFactory,
        Session                                       $customerSession,
        protected UrlInterface                        $urlInterface,
        private readonly RequestInterface             $request,
        private readonly JsonFactory                  $jsonResultFactory,
        private readonly TransportBuilder             $transportBuilder,
        private readonly InlineTranslation            $inlineTranslation,
        private readonly StoreManagerInterface        $storeManager,
        private readonly LoggerInterface              $logger,
        private readonly DirectoryList                $directoryList,
        protected IoFile                              $file,
        private readonly MyCatalogRepositoryInterface $myCatalogRepository,
        private readonly CustomerFactory              $customerFactory,
        private readonly Pdf                          $pdf,
        private readonly ManagerInterface             $messageManager,
        private readonly Data                         $helper,
        private readonly ZendPdf                      $zendPdf,
        private readonly CatalogViewModel             $catalogViewModel,
        protected ScopeConfigInterface                $scopeConfig,
    ) {
        parent::__construct(
            $resultPageFactory,
            $customerSession,
            $urlInterface
        );
    }

    /**
     * Execute action based on request and return result
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface|ResponseInterface
    {
        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/checkoutIssue.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('================= Email START execute======================');
        try {
            // First validate the customer.
            parent::validateCustomer();

            // Check, if email functionality is triggered.
            $params = $this->request->getParams();
            $logger->info('================= Email END execute try before if ======================');
            if (isset($params['catalog_id']) && !empty($params['catalog_id']) &&
                isset($params['recipient']) && !empty($params['recipient'])) {
                    $logger->info('================= Email END execute try inside if ======================');
                $response = $this->sendCatalogEmail();

                $result = $this->jsonResultFactory->create();
                $result->setData($response);
                return $result;
            }
        } catch (\Exception $e) {
            $logger->info('================= Email END execute catch ======================'.$e->getMessage());
            $this->logger->critical($e->getMessage());
        }
        $logger->info('================= Email END execute======================');
        // Render the page.
        return $this->resultPageFactory->create();
    }

    /**
     * Method used to send the catalog in email.
     */
    private function sendCatalogEmail()
    {

        $writer = new \Zend_Log_Writer_Stream(BP . '/var/log/checkoutIssue.log');
        $logger = new \Zend_Log();
        $logger->addWriter($writer);
        $logger->info('================= Email START ======================');
        $downloadLink = 0;
        $filePath = '';
        $response  = ['success' => false];
      try {
          $logger->info('================= Email try ======================');
            $params    = $this->request->getParams();
            $sender    = $this->helper->getConfigValue(self::EMAIL_SENDER);
            $template  = $this->helper->getConfigValue(self::EMAIL_TEMPLATE);
            $catalogId = $params['catalog_id'];
            $this->pdf->setData('catalogId', $catalogId);
            $myCatalog = $this->myCatalogRepository->getById($catalogId);
            $pdfUrl = $this->zendPdf->createPdf($myCatalog->getCatalogTitle(), $catalogId, 0,true);
            $logger->info('================= Email pdf path ======================'.$pdfUrl);
            $parseUrl = parse_url($pdfUrl);
            $pubPath = $this->directoryList->getPath('pub');
            $filePath = $pubPath . $parseUrl['path'];
            $fileName = $this->file->getPathInfo($parseUrl['path'])['basename'];
            $logger->info('================= Email fileName path ======================'.$fileName);
            $this->file->chmod($filePath, 0777);
            $pdfContent = $this->file->read($filePath);
            // validate the PDF file size
            $fileSizeInBytes = filesize($filePath);
            $fileSizeInMB = round($fileSizeInBytes / (1024 * 1024), 2);
            $attachmentSizeLimit = $this->catalogViewModel->getConfigValue(self::EMAIL_ATTACHMENT_SIZE);
            $fileName = $myCatalog->getCatalogTitle().'.pdf';


            $templateContent ='perficient_mycatalog_email_template_attachment';
            if ($fileSizeInMB > $attachmentSizeLimit) {
                $downloadLink = 1;
                $templateContent ='perficient_mycatalog_email_template';
            }

            $transport = $this->transportBuilder->setTemplateIdentifier(
                $templateContent
            )
            ->setTemplateOptions([
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->storeManager->getStore()->getId(),
            ])
            ->setTemplateVars([
                'message' => $params['message'],
                'from'    => $this->customerSession->getCustomer()->getName(),
                'subject' => $this->helper->getConfigValue(self::EMAIL_SUBJECT),
                'download_url' => $pdfUrl
            ])
            ->setFromByScope($sender)
            ->addTo($params['recipient']);

            if ($downloadLink == 0) {
                // Assuming $pdfContent contains the actual content of the PDF file
                // and $fileName is the desired name for the attachment file.
                $transport->addAttachment($pdfContent, $fileName, 'application/pdf');
            }

            // Use the transport builder to prepare and send the email
            $transport = $this->transportBuilder->getTransport();


            if($transport->sendMessage()){
                $logger->info('================= Email send ======================');
            }else{
                $logger->info('================= Email not send ======================');
            }
            $this->inlineTranslation->resume();
            $this->markAsSharedCatalog($params['recipient'], $catalogId);
            $response['success'] = true;
            $this->messageManager->addSuccessMessage(__('Catalog sent successfully.'));

        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
          $response['success'] = $e->getMessage();
          $this->messageManager->addErrorMessage(__('Catalog not sent .'));
        }
        if($downloadLink == 0){
            $logger->info('================= Set Permission ======================');

            $logger->info('================= befor Removing the filePath  ======================'.$filePath);
            $this->file->rm($filePath);
            $logger->info('================= after Removing the file  ======================');
        }

        return $response;
    }

    /**
     * Mark As Shared Catalog
     *
     * @param $email
     * @param $catalogId
     */
    private function markAsSharedCatalog($email, $catalogId)
    {
        try {
            $websiteID = $this->storeManager->getStore()->getWebsiteId();
            $customer = $this->customerFactory->create()->setWebsiteId($websiteID)->loadByEmail($email);
            $customerId = $customer->getId();
            if ($customerId > 0 && $catalogId > 0) {
                $priceMultiplier = $this->customerSession->getMultiplier() ?? 1;
                $this->myCatalogRepository->markAsSharedCatalog($customerId, $catalogId, $priceMultiplier);
            }
        } catch (\Exception $e) {
            $this->logger->critical(__('Catalog Share: %1', $e->getMessage()));
        }
    }
}
