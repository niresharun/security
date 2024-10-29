<?php
/**
 * Plugin used to update the config values to retrieve and display lead-time information at checkout page.
 *
 * @package: Perficient/LeadTime
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_LeadTime LeadTime
 */
declare(strict_types=1);

namespace Perficient\LeadTime\Plugin;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Checkout\Model\DefaultConfigProvider;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Api\CartItemRepositoryInterface as QuoteItemRepository;
use Perficient\LeadTime\Helper\Data;
use Perficient\Productimize\Helper\Data as ProductimizeHelper;
use Perficient\Catalog\Helper\Data as CatalogHelper;
use Perficient\Wishlist\ViewModel\WishListProductViewModel;

/**
 * Class UpdateDefaultConfigForLeadTime
 * @package Perficient\LeadTime\Model\Plugin
 */
class UpdateDefaultConfigForLeadTime
{
    /**
     * UpdateDefaultConfigForLeadTime constructor.
     * @param Json $serializer
     */
    public function __construct(
        private readonly CheckoutSession          $checkoutSession,
        private readonly QuoteItemRepository      $quoteItemRepository,
        private readonly Json                     $serializer,
        private readonly Data                     $leadTimeHelper,
        private readonly ProductimizeHelper       $productimizeHelper,
        private readonly CatalogHelper            $catalogHelper,
        private readonly WishListProductViewModel $wishListProductViewModel
    )
    {
    }

    /**
     * @param DefaultConfigProvider $subject
     * @param $result
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetConfig(DefaultConfigProvider $subject, $result)
    {
        $quoteId = $this->checkoutSession->getQuote()->getId();
        if ($quoteId && isset($result['totalsData'])) {
            $quoteItems = $this->quoteItemRepository->getList($quoteId);
            $leadTimeData = [];
            $configurableData = [];
            $customizedData = [];
            $propDefaultDetailsArray = [];
            $propDefaultDetailsJson = [];

            if(isset($result['quoteData']['lead_time'])){
                $leadTimeMessageArray = explode("#html-body",$result['quoteData']['lead_time']);
                if($leadTimeMessageArray && isset($leadTimeMessageArray['0'])){
                    $result['quoteData']['lead_time'] = $leadTimeMessageArray['0'];
                }
            }
            foreach ($quoteItems as $index => $quoteItem) {
                $options = $quoteItem->getOptions();
                if (!empty($options)) {
                    $leadTime = '';
                    $editId = '';
                    $editIdCode = $this->productimizeHelper->getEditIdCode();
                    $pzPropDefaultDetails = [];
                    $customizedFormatData = [];
                    foreach ($options as $option) {
                        if ($option->getCode() == 'info_buyRequest') {
                            $unserializedInfoBuyRequest = $this->serializer->unserialize($option->getValue());

                            if (isset($unserializedInfoBuyRequest['lead_time'])) {
                                $leadTime = $unserializedInfoBuyRequest['lead_time'];
                            }
                            if (isset($unserializedInfoBuyRequest[$editIdCode])
                                && !empty($unserializedInfoBuyRequest[$editIdCode])) {
                                $editId = $unserializedInfoBuyRequest[$editIdCode];
                            }
                            if (isset($unserializedInfoBuyRequest['pz_cart_properties'])
                                && !empty($unserializedInfoBuyRequest['pz_cart_properties'])) {
                                $pzCartProperties = $unserializedInfoBuyRequest['pz_cart_properties'];
                                $customizedFormatData = [];
                                $expectedProductLabels = null;
                                if ($this->catalogHelper->isMirrorProduct($quoteItem->getProduct())) {
                                    $expectedProductLabels = CatalogHelper::$expectedConfMirrorProductLabel;
                                }
                                $customizedCartProperties = $this->catalogHelper
                                    ->getValidCustomizedOptions(
                                        $pzCartProperties,
                                        false,
                                        $expectedProductLabels
                                    );
                                if (!empty($customizedCartProperties['dataArray'])) {
                                    $customizedFormatData = $this->getCheckoutItemOptions($customizedCartProperties['dataArray']);
                                }
                                $pzPropDefaultDetails = $this->serializer->unserialize($unserializedInfoBuyRequest['pz_cart_properties']);
                                $isWeightedTopMat = $this->isWeightedTopMat($pzPropDefaultDetails);
                                $isWeightedBottomMat = $this->isWeightedBottomMat($pzPropDefaultDetails);
                                if ($isWeightedTopMat) {
                                    $pzPropDefaultDetails['is_weighted_top_mat'] = $this->isWeightedTopMat($pzPropDefaultDetails);
                                }
                                if ($isWeightedBottomMat) {
                                    $pzPropDefaultDetails['is_weighted_bottom_mat'] = $this->isWeightedBottomMat($pzPropDefaultDetails);
                                }
                            }
                            break;
                        }
                    }
                    $leadTimeData[$quoteItem->getItemId()] = $leadTime;
                    $customizedData[$quoteItem->getItemId()] = $editId;
                    if ($quoteItem->getProductType() === Configurable::TYPE_CODE) {
                        $product = $this->wishListProductViewModel->getSimpleProduct($quoteItem);
                        $configurableData[$quoteItem->getItemId()] = [
                            'name' => $product->getName()
                        ];
                    }
                    if (!empty($customizedFormatData)) {
                        $propDefaultDetailsJson[$quoteItem->getItemId()] = $customizedFormatData;
                        $propDefaultDetailsArray[$quoteItem->getItemId()] = $this->serializer->serialize($customizedFormatData);
                    }
                }
            }
            foreach ($result['totalsData']['items'] as &$item) {
                if (isset($leadTimeData[$item['item_id']])) {
                    $item['lead_time'] = $leadTimeData[$item['item_id']];
                }
                if (isset($customizedData[$item['item_id']])) {
                    $item['edit_id'] = $customizedData[$item['item_id']];
                }
                if (isset($pzPropDefaultDetails['is_weighted_top_mat'])) {
                    $item['weighted_top_mat'] = true;
                }
                if (isset($pzPropDefaultDetails['is_weighted_bottom_mat'])) {
                    $item['weighted_bottom_mat'] = true;
                }
                if (isset($propDefaultDetailsArray[$item['item_id']])) {
                    $item['view_default_details'] = $propDefaultDetailsJson[$item['item_id']];
                    $item['options'] = $propDefaultDetailsArray[$item['item_id']];
                }
                if (!empty($configurableData[$item['item_id']])) {
                    $item["name"] = $configurableData[$item['item_id']]['name'];
                }
            }
        }
        return $result;
    }

    /**
     * Check if top mat is weighted or not
     *
     * @param array $pzProp
     * @return bool
     */
    public function isWeightedTopMat($pzProp)
    {
        if (isset($pzProp['Top Mat']) && !empty($pzProp['Top Mat'])) {
            if (isset($pzProp['Top Mat Size Bottom']) && !empty($pzProp['Top Mat Size Bottom'])
                && isset($pzProp['Top Mat Size Left']) && !empty($pzProp['Top Mat Size Left'])
                && isset($pzProp['Top Mat Size Right']) && !empty($pzProp['Top Mat Size Right'])
                && isset($pzProp['Top Mat Size Top']) && !empty($pzProp['Top Mat Size Top'])
                && $pzProp['Top Mat Size Bottom'] == $pzProp['Top Mat Size Left']
                && $pzProp['Top Mat Size Left'] == $pzProp['Top Mat Size Right']
                && $pzProp['Top Mat Size Right'] == $pzProp['Top Mat Size Top']) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if bottom mat is weighted or not
     *
     * @param array $pzProp
     * @return bool
     */
    public function isWeightedBottomMat($pzProp)
    {
        if (isset($pzProp['Bottom Mat']) && !empty($pzProp['Bottom Mat'])) {
            if (isset($pzProp['Bottom Mat Size Bottom']) && !empty($pzProp['Bottom Mat Size Bottom'])
                && isset($pzProp['Bottom Mat Size Left']) && !empty($pzProp['Bottom Mat Size Left'])
                && isset($pzProp['Bottom Mat Size Right']) && !empty($pzProp['Bottom Mat Size Right'])
                && isset($pzProp['Bottom Mat Size Top']) && !empty($pzProp['Bottom Mat Size Top'])
                && $pzProp['Bottom Mat Size Bottom'] == $pzProp['Bottom Mat Size Left']
                && $pzProp['Bottom Mat Size Left'] == $pzProp['Bottom Mat Size Right']
                && $pzProp['Bottom Mat Size Right'] == $pzProp['Bottom Mat Size Top']) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $customizedCartProperties
     * @return array
     */
    public function getCheckoutItemOptions($customizedCartProperties)
    {
        $tempOptions = [];
        $i = 0;
        foreach ($customizedCartProperties as $dataLabel => $dataValue) {
            $tempOptions[$i]['label'] = $dataLabel;
            $tempOptions[$i]['value'] = $dataValue;
            $i = $i + 1;
        }
        $customOptions = $tempOptions;
        return $customOptions;
    }
}
