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
use Psr\Log\LoggerInterface;
use Perficient\MyCatalog\Helper\Data;
use Perficient\MyCatalog\Api\Data\TemplateInterface;

/**
 * Class GetTemplate
 * @package Perficient\MyCatalog\Controller\Index
 */
class GetTemplate extends AbstractAction
{
    /**
     * GetTemplate constructor.
     *
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlInterface $url
     * @param RequestInterface $request
     * @param JsonFactory $jsonResultFactory
     * @param LoggerInterface $logger
     * @param TemplateInterface $template
     * @param Data $helper
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Session $customerSession,
        UrlInterface $url,
        private readonly RequestInterface $request,
        private readonly JsonFactory $jsonResultFactory,
        private readonly LoggerInterface $logger,
        private readonly TemplateInterface $template,
        private readonly Data $helper
    ) {
        parent::__construct(
            $resultPageFactory,
            $customerSession,
            $url
        );
    }

    /**
     * Execute action based on request and return result
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface|ResponseInterface
    {
        $data = [];
        // First validate the customer.
        parent::validateCustomer();

        // Check, if email functionality is triggered.
        $params = $this->request->getParams();

        $templateName = 'template_' . $params['template'] . '.phtml';
        if (in_array($params['page'], ['front', 'back'])) {
            $templateName = 'template_' . $params['page'] . '.phtml';
        }

        $catalogId = $params['catalog_id'];
        if ($this->helper->isCatalogOwner($catalogId)) {
            $data = [
                'templateData' => $this->template->getTemplateData($params),
                'catalog_id'   => $catalogId
            ];

            $resultPage = $this->resultPageFactory->create();
            $block = $resultPage->getLayout()->createBlock(
                \Perficient\MyCatalog\Block\MyPages::class,
                'Template_Main',
                ['data' => $data]
            );

            $block->setTemplate('Perficient_MyCatalog::page_templates/' . $templateName);
            $data['html'] = $block->toHtml();
            $data['pageConfig'] = $data['templateData']['data'];
        } else {
            $data[] = 'myCatalogAuthError';
        }

        $result = $this->jsonResultFactory->create();
        $result->setData($data);
        return $result;
    }
}
