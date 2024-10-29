<?php
/**
 * Controller to save customer price multiplier value
 *
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Daniel Wargolet <Daniel.Wargolet@perficient.com>
 * @keywords: price multiplier my catalog entity
 */
declare(strict_types=1);

namespace Perficient\PriceMultiplier\Controller\Index;

use Magento\Customer\Model\Session;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultInterface;
use Perficient\MyCatalog\Api\MyCatalogRepositoryInterface;


class SaveMyCatalogMultiplier implements ActionInterface
{
    /**
     * Save constructor.
     * @param JsonFactory $resultJsonFactory
     * @param MyCatalogRepositoryInterface $myCatalogRepository
     * @param RequestInterface $request
     */
    public function __construct(
        private readonly JsonFactory                  $resultJsonFactory,
        private readonly MyCatalogRepositoryInterface $myCatalogRepository,
        protected RequestInterface                    $request
    )
    {

    }

    public function execute(): ResultInterface|ResponseInterface
    {
        $multiplier = $this->request->getParam('price_multiplier');
        $myCatId    = $this->request->getParam('catalog_id');
        /** @var JsonFactory $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        try {
            $myCatalogRepo = $this->myCatalogRepository->getById($myCatId);
            $myCatalogRepo->setPriceModifier($multiplier);
            $this->myCatalogRepository->save($myCatalogRepo);

            return $resultJson->setData(
                [
                    'status' => 1,
                    'message' => __("Your price setting information has been saved.")
                ]
            );
        } catch (\Exception) {
            return $resultJson->setData(
                [
                    'message' => __('Something went wrong. We are unable to process your request.')
                ]
            );
        }
        $this->_redirect($this->_redirect->getRefererUrl());
    }
}
