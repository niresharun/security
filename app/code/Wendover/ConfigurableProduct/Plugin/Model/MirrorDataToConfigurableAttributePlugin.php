<?php
declare(strict_types=1);

namespace Wendover\ConfigurableProduct\Plugin\Model;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Block\Product\ListProduct;
use Magento\Catalog\Model\ProductRepository;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as TypeConfigurable;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Perficient\Catalog\Helper\Data as CatalogDataHelper;
use Perficient\Catalog\ViewModel\CatalogViewModel;
use Wendover\ConfigurableProduct\ViewModel\FrameViewModel;
use Perficient\PriceMultiplier\Model\ProductPrice;

class MirrorDataToConfigurableAttributePlugin
{
    public const MIRROR_FRAME_KEY = 'Frame';

    private $weightUnit = null;
    private $frameDefaultSKUAttributeId = null;

    /**
     * @param CatalogViewModel $catalogViewModel
     * @param JsonSerializer $jsonSerializer
     * @param CatalogDataHelper $dataHelper
     * @param ProductRepository $productRepository
     * @param ListProduct $listProduct
     * @param ProductPrice $productPrice
     */
    public function __construct(
        protected readonly CatalogViewModel $catalogViewModel,
        protected readonly JsonSerializer $jsonSerializer,
        protected readonly CatalogDataHelper $dataHelper,
        protected readonly ProductRepository $productRepository,
        protected readonly FrameViewModel    $frameViewModel,
        protected readonly ListProduct       $listProduct,
        protected readonly ProductPrice      $productPrice,
    )
    {
    }

    public function afterGetJsonConfig(TypeConfigurable $subject, string $result): string
    {
        $result = $this->jsonSerializer->unserialize($result);

        $this->enhanceFrameOptionAttributeWithImageDetails($result['attributes']);
        $this->enhanceChildAdditionalData($subject->getProduct(), $result);
        $this->implementPriceMultiplier($result);

        return $this->jsonSerializer->serialize($result);
    }

    /**
     * Enhance the product option with frame image and frame-spec-image details
     * @return void
     */
    private function enhanceFrameOptionAttributeWithImageDetails(array &$attributes): void
    {
        if (empty($attributes)) {
            return;
        }

        $frameAttributeId = $this->getFrameDefaultSKUAttributeId();
        // retrieve the frame attribute from array by frame-attribute-id
        $frameAttribute = array_filter(
            $attributes,
            fn ($attributeId) => $frameAttributeId == $attributeId,
            ARRAY_FILTER_USE_KEY
        );

        if (empty($frameAttribute)) {
            return;
        }

        foreach ($frameAttribute as $attributeId => $attribute) {
            foreach ($attributes[$attributeId]['options'] as &$option) {
                $sku = $option['id'];
                $frameProduct = $this->productRepository->get($sku);
                $option['frameFinish'] = $frameProduct->getAttributeText('color_frame');
                $option['frameImage'] = $this->listProduct
                    ->getImage($frameProduct, 'category_page_list')->getImageUrl();
                $option['frameImageSpec'] = $this->dataHelper
                    ->getSwatchImagePathForDefaultConf($sku, CatalogDataHelper::MEDIA_PATH_PLP_PRODUCTS);
                $option['frameDimension'] = $this->dataHelper->frameDimension($frameProduct);
            }
        }
    }

    /**
     * @param ProductInterface $parentProduct
     * @param array $result
     * @return void
     */
    private function enhanceChildAdditionalData(ProductInterface $parentProduct, &$result):void {
        $children = $parentProduct->getTypeInstance()->getUsedProducts($parentProduct);
        $weightUnit = $this->getWeightUnit();
        $result['specialties']['default'] = $parentProduct->getData('specialty');
        $result['weights']['default'] = !empty($parentProduct->getWeight()) ?
            $this->formatWeight((float)$parentProduct->getWeight(), $weightUnit) : '';
        $result['pz_cart_properties']['default'] = $this->getPzCartProperties($parentProduct);
        foreach ($children as $child) {
            $childProduct = $this->productRepository->get($child->getSku());
            $childWeight = $childProduct->getWeight();
            $result['specialties'][$childProduct->getId()] = $childProduct->getData('specialty');
            $result['weights'][$childProduct->getId()] = !empty($childWeight) ?
                $this->formatWeight((float)$childWeight, $weightUnit) : '';
            $result['pz_cart_properties'][$childProduct->getId()] = $this->getPzCartProperties($childProduct);
        }
    }

    private function formatWeight(float $weight, string $unit): string {
        $precision = 2;

        if (floor((int)$weight) === (float)$weight) {
            $precision = 0;
        }
        return sprintf(
            "%.".$precision."f %s",
            number_format($weight, $precision),
            $unit
        );
    }

    private function getFrameDefaultSKUAttributeId(): int {
        if ($this->frameDefaultSKUAttributeId == null) {
            $this->frameDefaultSKUAttributeId = $this->frameViewModel->getFrameDefaultSkuConfigurableAttributeId();
        }
        return $this->frameDefaultSKUAttributeId;
    }

    private function getWeightUnit(): ?string {
        if ($this->weightUnit == null) {
            $this->weightUnit = $this->catalogViewModel->getWeightUnit();
        }
        return $this->weightUnit;
    }

    /**
     * Return the required mirror default-configuration
     * @param ProductInterface $product
     * @return array
     */
    private function getPzCartProperties(ProductInterface $product): array
    {
        $defaultConf = $product->getData('default_configurations');
        if (empty($defaultConf)) {
            return [];
        }
        list('dataArray' => $json) = $this->catalogViewModel
            ->getDefaultConfigurationJson($product);

        return $json ?: [];
    }

    /**
     * @param array $result
     * @return void
     */
    private function implementPriceMultiplier(&$result): void
    {
        $prices = $result['optionPrices'];

        $childIds = [];
        if (!empty($prices)) {
            foreach ($prices as $key => $price) {
                $childIds[] = $key;
            }
        }

        $priceData = $this->productPrice->getItemPrice($childIds);
        if (!empty($priceData)) {
            foreach ($priceData as $key => $data) {
                $prices[$key] = [
                    'basePrice' => [
                        'amount' => $data['unformatted_price']
                    ],
                    'baseOldPrice' => [
                        'amount' => $data['unformatted_price']
                    ],
                    'oldPrice' => [
                        'amount' => $data['unformatted_price']
                    ],
                    'finalPrice' => [
                        'amount' => $data['unformatted_price']
                    ]
                ];
            }
        }

        $result['optionPrices'] = $prices;
    }
}
