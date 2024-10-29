<?php
/**
 * PriceMultiplier module for multiplier price .
 *
 * @category:  JS
 * @package:   Perficient/PriceMultiplier
 * @copyright:
 * See COPYING.txt for license details.
 * @author: Sandeep Mude <sandeep.mude@perficient.com>
 * @keywords:  Module Perficient_PriceMultiplier
 */
declare(strict_types=1);

namespace Perficient\PriceMultiplier\Controller\Product;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Perficient\PriceMultiplier\Model\ProductPrice;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\RequestInterface;

/**
 * Fetch product stock data
 * @package Perficient\PriceMultiplier\Controller\Product
 */
class Fetch implements ActionInterface
{
    /**
     * Fetch constructor.
     *
     * @param JsonFactory $resultJsonFactory
     * @param RequestInterface $request
     */
    public function __construct(
        protected JsonFactory $resultJsonFactory,
        protected ProductCollection $productCollection,
        protected ProductPrice $productPrice,
        protected RequestInterface $request
    ) {

    }

    /**
     * Function to retrieve product price and stock
     *
     */
    public function execute(): Json
    {
        $resultJson = $this->resultJsonFactory->create();
        $response   = [
            'success' => false,
            'data'    => []
        ];
        try {
            $ids = $this->request->getParam('ids');
            $response = $this->productPrice->getItemPrice($ids);
        } catch (LocalizedException $e) {
            $response['message'] = $e->getMessage();
        }

        return $resultJson->setData($response);
    }
}
