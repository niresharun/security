<?php
/**
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Amin Akhtar <Amin.Akhtar@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Controller\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Raw;
use Psr\Log\LoggerInterface;
use Perficient\Catalog\Helper\Data as CatalogHelper;
use Magento\Framework\App\RequestInterface;

class CheckFrameStock implements ActionInterface
{
    public function __construct(
        protected JsonFactory       $resultJson,
        protected LoggerInterface   $loggerInterface,
        protected CatalogHelper     $catalogHelper,
        protected RequestInterface  $request
    ) {
    }

    /**
     * Trigger to check in stock
     *
     * @return Json
     */
    public function execute()
    {
        $resultJson = $this->resultJson->create();
        $response = [
            'is_in_stock' => true,
            'days_to_in_stock' => false,
            'message_one' => '',
            'message_two' => '',
            'notify_url' => ''
        ];

        try {
            $defaultFrameSku = $this->request->getParam('defaultFrameSku');
            $currentPdpUrl = $this->request->getParam('currentPdpUrl');
            $notifyUrl = '';
            if (!empty($defaultFrameSku)) {
                $defaultFrame = $this->catalogHelper->getProductBySku($defaultFrameSku);
                if (isset($defaultFrame) && !empty($defaultFrame)) {
                    $frameStockData = $this->catalogHelper->getFrameStockData($defaultFrame);
                    if (!empty($frameStockData)) {
                        $isFrameInStock = $this->catalogHelper->isFrameInStock($frameStockData);
                        if (!$isFrameInStock) {
                            $daysToInStock = $this->catalogHelper->getDaysToInStock($defaultFrame);
                            $productId = $defaultFrame->getId();
                            if ($productId && $currentPdpUrl) {
                                $notifyUrl = $this->catalogHelper->getNotifyUrl(
                                    'stock',
                                    $productId,
                                    $currentPdpUrl
                                );
                            }

                            $response = [
                                'is_in_stock' => false,
                                'notify_url' => $notifyUrl
                            ];

                            if (!empty($daysToInStock)) {
                                $response['days_to_in_stock'] = true;
                                $response['message_one'] = 'This moulding will be in stock in';
                                $response['message_two'] = $daysToInStock . ' Days';
                            } else {
                                $response['days_to_in_stock'] = false;
                                $response['message_one'] = 'This moulding is currently out of stock';
                            }

                            return $resultJson->setData($response);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $response = [
                'error' => true,
                'message' => $e->getMessage()
            ];
            $this->logger->error($e->getMessage());
        }

        /** @var Raw $resultRaw */
        return $resultJson->setData($response);
    }
}
