<?php

namespace DCKAP\Productimize\Observer;

use Magento\Eav\Api\AttributeSetRepositoryInterface;
use DCKAP\Productimize\Helper\CustomProductCollection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Request\Http;
use Psr\Log\LoggerInterface;

class ProductSaveAfter implements ObserverInterface
{
    protected $attributeSetRepository;

    protected $customProductCollection;
    protected $request;
    protected $attributeSet;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        AttributeSetRepositoryInterface $attributeSetRepository,
        CustomProductCollection $customProductCollection,
        Http $request,
        LoggerInterface $logger)
    {
        $this->attributeSet = $attributeSetRepository;
        $this->customProductCollection = $customProductCollection;
        $this->request = $request;
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        $currentActionName = $this->request->getFullActionName();

        if (isset($currentActionName) && $currentActionName == "catalog_product_save") {

            $product = $observer->getProduct();  // you will get product object
            $attributeSetName = $this->getAttributeSetName($product);
            if (strtolower($attributeSetName) == 'frame') {
                $this->customProductCollection->getFrameCollection();
            } elseif (strtolower($attributeSetName) == 'mat') {
                $this->customProductCollection->getMatCollection();
            } elseif (strtolower($attributeSetName) == 'liner') {
                $this->customProductCollection->getLinerCollection();
            }
        }
    }

    public function getAttributeSetName($product)
    {
        $attributeSetRepository = $this->attributeSet->get($product->getAttributeSetId());
        return $attributeSetRepository->getAttributeSetName();
    }
}
