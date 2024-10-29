<?php
/**
 * @author     DCKAP <extensions@dckap.com>
 * @package    DCKAP_Productimize
 * @copyright  Copyright (c) 2017 DCKAP Inc (http://www.dckap.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace DCKAP\Productimize\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;
use DCKAP\Productimize\Helper\Data;


/**
 * Class CustomizeButtonRestrict
 * @package DCKAP\Productimize\Controller\Index
 */
class CustomizeButtonRestrict extends Action
{

    /**
     * CustomizeButtonRestrict constructor.
     * @param Context $context
     */

    public function __construct(
        Context $context,
        private ProductRepositoryInterface $productRepository,
        private Data $productimizeData
    )
    {
        parent::__construct($context);
    }

    /**
     * @return array
     */
    public function execute()
    {        
        $productId = $this->getRequest()->getParam('product');
        $product = $this->productRepository->getById($productId);
        $croppedImage = $product->getCropped();
        $configLevel = 4;
        if(!empty($product->getData("product_customizer") && (!empty($croppedImage) && $croppedImage != "no_selection"))) {
            $configLevel = $product->getAttributeText('configuration_level');
        }
        $accessCode = $this->productimizeData->getCustomerAccessRestrictionCode();
        $output = array(
            'configLevel' => $configLevel,
            'restrictAccessCode' => $accessCode
        );
        echo $this->productimizeData->getSerializeData($output);

        //if (!empty($layoutType = $product->getData("product_customizer"))) {
            
            
    }
}
