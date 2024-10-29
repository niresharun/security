<?php
/**
 * This module is used to prepare add to collection configurable url on checkout
 *
 * @category: Magento
 * @package: Perficient/Checkout
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Trupti Bobde <trupti.bobde@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Checkout
 */

namespace Perficient\Checkout\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Perficient\Company\Helper\Data as CompanyHelper;
use Magento\Checkout\Model\Session;
use Magento\Framework\Message\ManagerInterface;

class UpdateMessage implements ObserverInterface
{
    /**
     * UpdateMessage constructor.
     * @param RequestInterface $request
     * @param ProductRepository $productRepository
     */
    public function __construct(
        protected \Magento\Framework\Message\ManagerInterface $managerInterface,
        protected \Magento\Framework\UrlInterface             $url,
        protected RequestInterface                            $request,
        protected ProductRepositoryInterface                  $productRepository,
        protected CompanyHelper                               $companyHelper,
        protected Session                                     $_checkoutSession,
        protected ManagerInterface                            $messageManager
    )
    {
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $productId = $this->request->getParam('product');
        $product = $this->productRepository->getById($productId);
        $relatedProduct = $product->getRelatedProductIds();

        if (!$this->_checkoutSession->getQuote()->getHasError()) {
            $this->messageManager->getMessages(true);

            if (empty($relatedProduct)) {
                $message = __(
                    '%1 has been added to your cart.',
                    $product->getName()
                );
                $this->messageManager->addSuccessMessage($message);
            } else {
                $currentUserRole = $this->companyHelper->getCurrentUserRole();
                $currentUserRole = $currentUserRole ? htmlspecialchars_decode((string)$currentUserRole, ENT_QUOTES) : '';
                if (strcmp($currentUserRole, (string)CompanyHelper::CUSTOMER_CUSTOMER) == 0) {
                    $message = __(
                        '%1 has been added to your cart.',
                        $product->getName()
                    );
                    $this->messageManager->addSuccessMessage($message);
                } else {
                    $this->messageManager->addComplexSuccessMessage(
                        'addCartSuccessMessage',
                        [
                            'product_name' => $product->getName(),
                            'product_id' => $product->getId()
                        ]
                    );
                }
            }
        }

    }
}
