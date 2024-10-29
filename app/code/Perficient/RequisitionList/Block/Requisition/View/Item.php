<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Perficient\RequisitionList\Block\Requisition\View;

use Magento\Catalog\Block\Product\Context;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Tax\Helper\Data as TaxHelper;
use Magento\RequisitionList\Model\Checker\ProductChangesAvailability;
use Magento\RequisitionList\Api\Data\RequisitionListItemInterface;
use Magento\RequisitionList\Model\RequisitionListItem\Validator\Sku as SkuValidator;
use Magento\RequisitionList\Model\RequisitionListItemProduct;
use Magento\Framework\App\ObjectManager;
use Magento\RequisitionList\Model\RequisitionListItemOptionsLocator;
use Magento\Catalog\Model\Product\Configuration\Item\ItemResolverInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Escaper;
use Magento\Company\Block\Company\Account\Dashboard\RoleInfo;


/**
 * View block for requisition list item.
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @since 100.0.0
 */
class Item extends \Magento\RequisitionList\Block\Requisition\View\Item
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var Image
     */
    private $imageHelper;

    private readonly TaxHelper $taxHelper;

    private readonly CatalogHelper $catalogHelper;

    /**
     * @var ProductChangesAvailability
     */
    private $productChangesAvailabilityChecker;

    /**
     * @var RequisitionListItemProduct
     */
    private $requisitionListItemProduct;

    /**
     * @var RequisitionListItemOptionsLocator
     */
    private $itemOptionsLocator;

    /**
     * @var ItemResolverInterface
     */
    private $itemResolver;

    /**
     * @var RequisitionListItemInterface
     */
    private $item;

    private array $itemErrors = [];

    /**
     * @param Context $context
     * @param PriceCurrencyInterface $priceCurrency
     * @param ProductChangesAvailability $productChangesAvailabilityChecker
     * @param RequisitionListItemProduct $requisitionListItemProduct
     * @param array $data [optional]
     * @param RequisitionListItemOptionsLocator $itemOptionsLocator
     * @param ItemResolverInterface $itemResolver
     * @param SerializerInterface $serializer
     */
    public function __construct(
        Context $context,
        PriceCurrencyInterface $priceCurrency,
        ProductChangesAvailability $productChangesAvailabilityChecker,
        RequisitionListItemProduct $requisitionListItemProduct,
        private readonly SerializerInterface $serializer,
        private readonly RoleInfo $roleInfo,
        private readonly Escaper $escaper,
        array $data = [],
        RequisitionListItemOptionsLocator $itemOptionsLocator = null,
        ItemResolverInterface $itemResolver = null
    ) {
        parent::__construct($context,$priceCurrency,$productChangesAvailabilityChecker,$requisitionListItemProduct,$data,$itemOptionsLocator,$itemResolver);
        $this->imageHelper = $context->getImageHelper();
        $this->requisitionListItemProduct = $requisitionListItemProduct;
        $this->itemOptionsLocator = $itemOptionsLocator
            ?? ObjectManager::getInstance()->get(RequisitionListItemOptionsLocator::class);
        $this->itemResolver = $itemResolver
            ?? ObjectManager::getInstance()->get(ItemResolverInterface::class);
    }


    /**
     * Get url of product image from requisition list item.
     *
     * @return string|null
     */
    public function getImageUrl()
    {
        try {
           if($this->getItem()->getOptions()) {
               $options = $this->getItem()->getOptions();
               if (isset($options['info_buyRequest']) && isset($options['info_buyRequest']['pz_cart_properties']) && !empty($options['info_buyRequest']['pz_cart_properties'])) {
                   $pzCart = $this->serializer->unserialize($options['info_buyRequest']['pz_cart_properties']);
                   if (isset($pzCart['CustomImage'])) {
                       return $pzCart['CustomImage'];
                   }
               }
           }
               $product = $this->itemResolver->getFinalProduct($this->itemOptionsLocator->getOptions($this->getItem()));
               $imageUrl = $this->imageHelper->getDefaultPlaceholderUrl('thumbnail');
               if ($product !== null) {
                   //print $imageUrl;die();
                   $imageUrl = $this->imageHelper->init($product, 'product_thumbnail_image')->getUrl();
               }
               return $imageUrl;
        } catch (\Magento\Framework\Exception\NoSuchEntityException) {
            return null;
        }
    }

    public function getCurrentUserRole(): array|string
    {
        $currentUserRole = $this->roleInfo->getCustomerRoles();
        return $this->escaper->escapeHtml($currentUserRole);
    }
}
