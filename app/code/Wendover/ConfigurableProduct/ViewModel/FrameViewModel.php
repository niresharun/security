<?php
declare(strict_types=1);

namespace Wendover\ConfigurableProduct\ViewModel;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as EavAttribute;
use Magento\Framework\Serialize\Serializer\Json;
use Perficient\ViewInRoom\Helper\Data as ViewInRoomHelper;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Wendover\Catalog\Setup\Patch\Data\CreateMirrorOptionAttribute;

class FrameViewModel implements ArgumentInterface
{
    public function __construct(
        protected readonly Json $json,
        protected readonly ViewInRoomHelper $viewInRoomHelper,
        protected readonly EavAttribute $eavAttribute
    ) {
    }

    public function getFrameDefaultSkuConfigurableAttributeId(): int {
        return (int)$this->eavAttribute
            ->getIdByCode(Product::ENTITY, CreateMirrorOptionAttribute::DEFAULT_FRAME_SKU_CONFIGURABLE);
    }

    /**
     * Return array of parent and all child product name
     *
     * @param ProductInterface $product
     * @return string
     */
    public function getAllProductNameJSON(ProductInterface $product): string {
        $productName = ['default' => $product->getName()];
        if ($product->getTypeId() === Configurable::TYPE_CODE) {
            $children = $product->getTypeInstance()->getUsedProducts($product);
            foreach ($children as $child){
                $productName[$child->getId()] = $child->getName();
            }
        }
        return $this->json->serialize($productName);
    }

    /**
     * @param ProductInterface $product
     * @return string
     */
    public function getAllChildProductViewInRoomJSON(ProductInterface $product): string {
        $defaultConfig = ['default' => $this->viewInRoomHelper->getConfig($product)];
        if ($product->getTypeId() === Configurable::TYPE_CODE) {
            $children = $product->getTypeInstance()->getUsedProducts($product);
            foreach ($children as $child){
                $defaultConfig[$child->getId()] = $this->viewInRoomHelper->getConfig($child);
            }
        }
        return $this->json->serialize($defaultConfig);
    }
}
