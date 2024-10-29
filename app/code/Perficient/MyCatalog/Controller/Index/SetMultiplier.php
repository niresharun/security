<?php
/**
 * This module is used to create custom artwork catalogs.
 * This file contains the logic to set the multiplier.
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
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Perficient\MyCatalog\Api\MyCatalogRepositoryInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\UrlInterface;
use Perficient\MyCatalog\Helper\Data;
use Psr\Log\LoggerInterface;

/**
 * Class SetMultiplier
 * @package Perficient\MyCatalog\Controller\Index
 */
class SetMultiplier extends AbstractAction
{
    /**
     * SetMultiplier constructor.
     *
     * @param PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlInterface $url
     * @param JsonFactory $jsonResultFactory
     * @param RequestInterface $request
     * @param LoggerInterface $logger
     * @param MyCatalogRepositoryInterface $myCatalogRepository
     * @param Data $helper
     */
    public function __construct(
        PageFactory $resultPageFactory,
        Session $customerSession,
        UrlInterface $url,
        private readonly JsonFactory $jsonResultFactory,
        private readonly RequestInterface $request,
        private readonly LoggerInterface $logger,
        private readonly MyCatalogRepositoryInterface $myCatalogRepository,
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
     */
    public function execute(): \Magento\Framework\Controller\ResultInterface|ResponseInterface
    {
        // First validate the customer.
        parent::validateCustomer();

        $catalogId = (int)$this->request->getParam('catalog_id');
        $priceOn = (int)$this->request->getParam('price_on');
        $response  = ['success' => false];

        try {
            if ($this->helper->isCatalogOwner($catalogId)) {
                $model = $this->myCatalogRepository->getById($catalogId);
                $model->setPriceOn($priceOn);
                $this->myCatalogRepository->save($model);
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
        $result = $this->jsonResultFactory->create();
        $result->setData($response);
        return $result;
    }
}
