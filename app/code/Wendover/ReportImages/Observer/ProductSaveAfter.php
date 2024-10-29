<?php

namespace Wendover\ReportImages\Observer;

use Magento\Framework\Event\ObserverInterface;
use Wendover\ReportImages\Model\MissingImagesFactory as ModelImages;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Framework\App\Request\Http;

class ProductSaveAfter implements ObserverInterface
{
    public function __construct(
        protected ModelImages $imageMissingModel,
        protected AttributeSetRepositoryInterface $eavAttribute,
        protected Http $request
    ) {
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $product = $observer->getProduct();
        $id = $product->getId();
        $item_id= $this->request->getParam('img_rep_id');
        $attributeSetRepository = $this->eavAttribute->get($product->getAttributeSetId());
        $attributeSetName = $attributeSetRepository->getAttributeSetName();
        $imageReportCollection = $this->imageMissingModel->create()->getCollection()
        ->addFieldToFilter('product_id', $id);
        foreach ($imageReportCollection as $imageReport) {
        if (($attributeSetName == 'Art' && (!empty($product->getImage()) && !empty($product->getSmallImage()) &&
             !empty($product->getThumbnail()) && !empty($product->getCropped())) ) || $attributeSetName == 'Mat' &&
             ((!empty($product->getImage()) && !empty($product->getSmallImage()) && !empty($product->getThumbnail())))
             || (($attributeSetName == 'Frame') || ($attributeSetName == 'Liner') || ($attributeSetName == 'Mirror'))
             && (!empty($product->getImage()) && !empty($product->getSmallImage()) && !empty($product->getThumbnail())
             && !empty($product->getSingleCorner()) && !empty($product->getDoubleCorner()) &&
             !empty($product->getSpecDetails()) && !empty($product->getRendererLength())  &&
             !empty($product->getRendererCorner()))) {
                $imageReport->load($id)->delete();
        } else {
            $imageReport->setData([
                    'base' => $product->getImage() ? 1 : 0,
                    'small' => $product->getSmallImage() ? 1 : 0,
                    'thumbnail' => $product->getThumbnail() ? 1 : 0,
                    'cropped_art' => $product->getCropped() ? 1 : 0,
                    'single_corner_image' => $product->getSingleCorner() ? 1 : 0,
                    'spec_detail_image' => $product->getSpecDetails() ? 1: 0,
                    'double_corner_image' => $product->getDoubleCorner() ? 1 : 0,
                    'renderer_length' => $product->getRendererLength() ? 1 : 0,
                    'renderer_corner' => $product->getRendererCorner() ? 1 : 0
                ]);
            $imageReport->save();
            }
        }
    }
}
