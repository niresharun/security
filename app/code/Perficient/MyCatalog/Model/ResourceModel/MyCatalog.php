<?php
/**
 * This module is used to create custom artwork catalogs,
 *
 * @category: Magento
 * @package: Perficient/MyCatalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyCatalog
 */
declare(strict_types=1);

namespace Perficient\MyCatalog\Model\ResourceModel;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Catalog\Helper\Image;
use Magento\Customer\Model\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Directory\Model\CurrencyFactory;
use Perficient\Productimize\Model\ProductConfiguredPrice;
use Magento\Framework\Serialize\Serializer\Json;
use Perficient\Catalog\Helper\Data;

/**
 * Class MyCatalog
 * @package Perficient\MyCatalog\Model\ResourceModel
 */
class MyCatalog extends AbstractDb
{
    /**
     *
     */
    const TABLE_CATALOG  = '';
    const TABLE_WISHLIST = 'wishlist';
    const TABLE_WISHLIST_ITEM = 'wishlist_item';
    const TABLE_WISHLIST_ITEM_OPTION = 'wishlist_item_option';
    const TABLE_PRODUCT  = 'catalog_product_entity';
    const TABLE_PRODUCT_IMAGE  = 'catalog_product_entity_media_gallery';

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('perficient_customer_gallery_catalog', 'catalog_id');
    }

    /**
     * MyCatalog constructor.
     * @param Context $context
     * @param ProductRepositoryInterface $productRepository
     * @param Image $imageHelper
     * @param Session $customerSession
     * @param RequestInterface $request
     * @param StoreManagerInterface $storeManager
     * @param CurrencyFactory $currencyFactory
     * @param null $connectionName
     * @param ProductConfiguredPrice $productConfiguredPrice
     * @param Json $json
     * @param Data $catalogHelper
     */
    public function __construct(
        Context $context,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly Image $imageHelper,
        private readonly Session $customerSession,
        private readonly RequestInterface $request,
        private readonly StoreManagerInterface $storeManager,
        private readonly CurrencyFactory $currencyFactory,
        private readonly ProductConfiguredPrice $productConfiguredPrice,
        private readonly Json $json,
        private readonly Data $catalogHelper,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
    }

    /**
     * @param $catalogId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getGalleryImages($catalogId)
    {
        $connection = $this->getConnection();

        $catalogSelect = $connection->select()
            ->from($this->getMainTable() . ' AS catalog')
            ->joinInner(
                ['wishlist' => $this->getTable(self::TABLE_WISHLIST)],
                'catalog.wishlist_id = wishlist.wishlist_id',
                ['name AS wishlist_name']
            )
            ->joinInner(
                ['wishlist_item' => $this->getTable(self::TABLE_WISHLIST_ITEM)],
                'wishlist.wishlist_id = wishlist_item.wishlist_id',
                ['*']
            )
            ->joinInner(
                ['wishlist_item_option' => $this->getTable(self::TABLE_WISHLIST_ITEM_OPTION)],
                'wishlist_item.wishlist_item_id = wishlist_item_option.wishlist_item_id and wishlist_item_option.code = "info_buyRequest"',
                ['value AS item_options']
            )
            ->where('catalog.catalog_id =?',  $catalogId)
        ;
        $galleryRows = $connection->fetchAll($catalogSelect);

        $galleryData = [];
        foreach ($galleryRows as $row) {
            $key = $row['wishlist_item_id'];
            $productImgUrl = "";
            try {
                $product = $this->productRepository->getById($row['product_id']);
                $itemOptions = (isset($row['item_options'])) ? json_decode((string) $row['item_options'], true, 512, JSON_THROW_ON_ERROR) : "";
                if (isset($itemOptions)) {
                    if (isset($itemOptions['edit_id'])) {
                        $pzCartProperties = (isset($itemOptions['pz_cart_properties'])) ? json_decode((string) $itemOptions['pz_cart_properties'], true, 512, JSON_THROW_ON_ERROR) : "";
                        $productImgUrl = $pzCartProperties['CustomImage'] ?? "";
                    }
                }
                if (empty($productImgUrl)) {
                    $productImgUrl = $this->imageHelper->init($product, 'product_page_main_image')->getUrl();
                }

                $galleryData['p' . $key] = $row;
                $galleryData['p' . $key]['url'] = $productImgUrl;
                $galleryData['p' . $key]['desc'] = $this->getProductData($product, $catalogId, $row['price_on'], $row['item_options']);
                $galleryData['p' . $key]['product_name'] = $product->getName();
            } catch (\Exception) {
                if (isset($galleryData['p' . $key])) {
                    unset($galleryData['p' . $key]);
                }
            }
        }

        return $galleryData;
    }


     /**
     * @param $catalogId
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getGalleryImagesPdf($catalogId)
    {
        $connection = $this->getConnection();

        $catalogSelect = $connection->select()
            ->from($this->getMainTable() . ' AS catalog')
            ->joinInner(
                ['wishlist' => $this->getTable(self::TABLE_WISHLIST)],
                'catalog.wishlist_id = wishlist.wishlist_id',
                ['name AS wishlist_name']
            )
            ->joinInner(
                ['wishlist_item' => $this->getTable(self::TABLE_WISHLIST_ITEM)],
                'wishlist.wishlist_id = wishlist_item.wishlist_id',
                ['*']
            )
            ->joinInner(
                ['wishlist_item_option' => $this->getTable(self::TABLE_WISHLIST_ITEM_OPTION)],
                'wishlist_item.wishlist_item_id = wishlist_item_option.wishlist_item_id and wishlist_item_option.code = "info_buyRequest"',
                ['value AS item_options']
            )
            ->where('catalog.catalog_id =?',  $catalogId)
        ;
        $galleryRows = $connection->fetchAll($catalogSelect);

        $galleryData = [];
        foreach ($galleryRows as $row) {
            $key = $row['wishlist_item_id'];
            $productImgUrl = "";
            try {
                $product = $this->productRepository->getById($row['product_id']);
                $itemOptions = (isset($row['item_options'])) ? json_decode((string) $row['item_options'], true, 512, JSON_THROW_ON_ERROR) : "";
                if (isset($itemOptions)) {
                    if (isset($itemOptions['edit_id'])) {
                        $pzCartProperties = (isset($itemOptions['pz_cart_properties'])) ? json_decode((string) $itemOptions['pz_cart_properties'], true, 512, JSON_THROW_ON_ERROR) : "";
                        $productImgUrl = $pzCartProperties['CustomImage'] ?? "";
                        //return $product->getData('image');
                          $productImgUrl =  $product->getData('image');
                    }
                }
                if (empty($productImgUrl)) {
                    $productImgUrl = $this->imageHelper->init($product, 'product_page_main_image')->getUrl();
                }
                $productImgUrl =  $product->getData('image');
                $galleryData['p' . $key] = $row;
                $galleryData['p' . $key]['url'] = $productImgUrl;
                $galleryData['p' . $key]['desc'] = $this->getProductDataPdf($product, $catalogId, $row['price_on'], $row['item_options']);
                $galleryData['p' . $key]['product_name'] = $product->getName();
            } catch (\Exception) {
                if (isset($galleryData['p' . $key])) {
                    unset($galleryData['p' . $key]);
                }
            }
        }

        return $galleryData;
    }

     /**
     * @param $product
     * @param $catalogId
     * @param $priceOn
     * @param $buyRequest
     * @return string
     */
    private function getProductDataPdf($product, $catalogId, $priceOn, $buyRequest)
    {
        if (is_string($buyRequest)) {
            $buyRequest = $this->json->unserialize($buyRequest);
        }

        $pz_cart_properties = $this->json->unserialize($buyRequest['pz_cart_properties']);
        $medium = $pz_cart_properties['Medium'];
        $frame = $pz_cart_properties['Frame'];
        $itemWidth = $pz_cart_properties['Item Width'];
        $itemHeight = $pz_cart_properties['Item Height'];
        $data = [];

        if (isset($buyRequest['edit_id']) && !empty($buyRequest['edit_id']) &&
            isset($buyRequest['configurator_price']) && !empty($buyRequest['configurator_price'])) {
            $price = $buyRequest['configurator_price'];
        } else {
            $price = $product->getPrice();
        }
        $price = $this->productConfiguredPrice->applyCompanyDiscount($price);
        $data['product_name'] = str_replace('&', 'and', (string) $product->getName());
        $data['sku'] = $product->getSku();
        $data['size'] = (!empty($itemWidth) && !empty($itemHeight))?$itemWidth . '"w' . ' x ' . $itemHeight . '"h':'';

        $myCatalog = $this->getMyCatalogRecordById($catalogId);
        // @todo: update schmema so it defaults to 1, to remove need for this if check
        $priceMultiplier = $myCatalog['price_modifier'] ?? 1;
        $isSharedCatalog = $this->request->getParam('shared', 0);
        if ($isSharedCatalog) {
            $sharedCatalogData = $this->isSharedCatalog($catalogId, $this->customerSession->getCustomerId(), true);
            if (isset($sharedCatalogData['price_multiplier'])) {
                $price *= $sharedCatalogData['price_multiplier'];
            }
        } else {
            $price *= $priceMultiplier;
        }

        $price = $this->getCurrencySymbol() . number_format((float)$price, 2);

        $style = '';
        if ($priceOn != null && $priceOn != 1) {
            $data['price'] ='';
        }else{
            $data['price'] =($priceMultiplier != 0)?$price:'';
        }

        $mediumName ='';
        if (isset($medium) && !empty($medium)) {
            $mediumName = $this->catalogHelper->getDisplayName($medium, 'medium');
        }

        $data['medium'] =(isset($medium) && !empty($medium))? $mediumName:'';

        $data['frame'] =(isset($frame) && !empty($frame))? $frame:'';
        return $data;
    }

    /**
     * @param $product
     * @param $catalogId
     * @param $priceOn
     * @param $buyRequest
     * @return string
     */
    private function getProductData($product, $catalogId, $priceOn, $buyRequest)
    {
        if (is_string($buyRequest)) {
            $buyRequest = $this->json->unserialize($buyRequest);
        }

        $pz_cart_properties = $this->json->unserialize($buyRequest['pz_cart_properties']);
        $medium = $pz_cart_properties['Medium'];
        $frame = $pz_cart_properties['Frame'];
        $itemWidth = $pz_cart_properties['Item Width'];
        $itemHeight = $pz_cart_properties['Item Height'];

        if (isset($buyRequest['edit_id']) && !empty($buyRequest['edit_id']) &&
            isset($buyRequest['configurator_price']) && !empty($buyRequest['configurator_price'])) {
            $price = $buyRequest['configurator_price'];
        } else {
            $price = $product->getPrice();
        }
        $price = $this->productConfiguredPrice->applyCompanyDiscount($price);

        $data = '<div class="name"><b>'.str_replace('&', 'and', (string) $product->getName()).'</b></div>';
        $data .= '<div class="sku"><b>'. __('Item #') . ': </b>'.$product->getSku().'</div>';
        if(!empty($itemWidth) && !empty($itemHeight)) {
            $data .= '<div class="sku"><b>' . __('Size') . ': </b>' . $itemWidth . '"w' . ' x ' . $itemHeight . '"h' . '</div>';
        }
        $myCatalog = $this->getMyCatalogRecordById($catalogId);
        // @todo: update schmema so it defaults to 1, to remove need for this if check
        $priceMultiplier = $myCatalog['price_modifier'] ?? 1;
        $isSharedCatalog = $this->request->getParam('shared', 0);
        if ($isSharedCatalog) {
            $sharedCatalogData = $this->isSharedCatalog($catalogId, $this->customerSession->getCustomerId(), true);
            if (isset($sharedCatalogData['price_multiplier'])) {
                $price *= $sharedCatalogData['price_multiplier'];
            }
        } else {
            $price *= $priceMultiplier;
        }

        $price = $this->getCurrencySymbol() . number_format((float)$price, 2);

        $style = '';
        if ($priceOn != null && $priceOn != 1) {
            $style = ' style="display: none;" ';
        }

        if ($priceMultiplier != 0) {
            $data .= '<div class="pricebox-container" '.$style.'>' . __('Price:')
                . ' <span class="pricebox"><span>'.$price.'</span>'
                . ' <input type="hidden" class="originalprice" value="'.$price.'" /></span></div>';
        }

        if (isset($medium) && !empty($medium)) {
            $mediumName = $this->catalogHelper->getDisplayName($medium, 'medium');
            $data .= '<div class="sku"><b>'. __('Medium') . ': </b>'.$mediumName.'</div>';
        }
        if (isset($frame) && !empty($frame)) {
            $data .= '<div class="sku"><b>'. __('Frame') . ': </b>'.$frame.'</div>';
        }

        return $data;
    }

    /**
     * Mark As Shared Catalog
     *
     * @param $customerId
     * @param $catalogId
     * @param $priceMultiplier
     * @return bool
     */
    public function markAsSharedCatalog($customerId, $catalogId, $priceMultiplier)
    {
        $this->getConnection()->insertOnDuplicate(
            $this->getTable('perficient_customer_catalog_share'),
            [
                'customer_id' => $customerId,
                'catalog_id' => $catalogId,
                'price_multiplier' => $priceMultiplier
            ],
            [
                'customer_id',
                'catalog_id',
                'price_multiplier'
            ]
        );

        return true;
    }


    /**
     * Is Shared Catalog
     *
     * @param $catalogId
     * @param $customerId
     * @param bool $getSharedCatalog
     * @return bool
     */
    public function isSharedCatalog($catalogId, $customerId, $getSharedCatalog = false)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('perficient_customer_catalog_share')
        )->where(
            implode(
                ' AND ',
                [
                    'catalog_id  = :catalog_id',
                    'customer_id  = :customer_id'
                ]
            )
        );

        $bind = [':catalog_id' => $catalogId, ':customer_id' => $customerId];

        $data = $this->getConnection()->fetchRow($select, $bind);
        if (!empty($data)) {
            if ($getSharedCatalog) {
                return $data;
            }
            return true;
        }

        return false;
    }

    /**
     * Method used to get current currency symbol.
     *
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getCurrencySymbol()
    {
        $currentCurrency = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $currencyFactory = $this->currencyFactory->create();
        $currency = $currencyFactory->load($currentCurrency);
        return $currency->getCurrencySymbol();
    }

    /**
     * Return the mycatalog record/row by catalog_id
     * @param int $catalogId
     * @return mixed
     */
    public function getMyCatalogRecordById($catalogId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('perficient_customer_gallery_catalog')
        )->where(
            implode(
                [
                    'catalog_id  = :catalog_id'
                ]
            )
        );

        $bind = [':catalog_id' => $catalogId];
        $data = $this->getConnection()->fetchRow($select, $bind);

        return $data;
    }
}
