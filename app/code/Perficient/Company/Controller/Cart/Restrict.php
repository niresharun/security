<?php
/**
 * Company module for add to cart restrict .
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani <Manish.Bhojwani@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */
declare(strict_types=1);

namespace Perficient\Company\Controller\Cart;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Perficient\Company\Helper\Data;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Fetch product stock data
 * @package Perficient\PriceMultiplier\Controller\Product
 */
class Restrict implements ActionInterface
{
    /**
     * Fetch constructor.
     *
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context                          $context,
        protected JsonFactory            $resultJsonFactory,
        private readonly Data            $helper,
        private readonly CustomerSession $customerSession
    )
    {
        
    }

    /**
     * Function to retrieve product price and stock
     */
    public function execute(): \Magento\Framework\Controller\Result\Json
    {
        $response = [];
        $resultJson = $this->resultJsonFactory->create();
        $response['showcart'] = true;

        if ($this->helper->isRestrictCartAndCheckout()) {

            if ($this->customerSession->isLoggedIn()) {

                // Restrict Add to Cart button if price multiplier is 0x
                $multiplier = $this->customerSession->getMultiplier() ?? 1;
                if ($multiplier == 0) {
                    $response['showcart'] = false;
                    return $resultJson->setData($response);
                }

                // Restrict Add to Cart Button for customer's customer
                $currentUserRole = $this->helper->getCurrentUserRole();
                $currentUserRole = $currentUserRole ? htmlspecialchars_decode((string)$currentUserRole, ENT_QUOTES) : '';
                if (strcmp($currentUserRole, (string) Data::CUSTOMER_CUSTOMER) == 0) {
                    $response['showcart'] = false;
                    return $resultJson->setData($response);
                }

            } else {
                // Restrict Add to Cart Button for guest customer
                $response['showcart'] = false;
                return $resultJson->setData($response);
            }
        }

        return $resultJson->setData($response);
    }
}
