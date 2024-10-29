<?php

namespace DCKAP\Productimize\Model\Rewrite\Model;


use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Math\Random;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Wishlist\Helper\Data;
use Magento\Wishlist\Model\ItemFactory;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory;
use Magento\Wishlist\Model\ResourceModel\Wishlist as ResourceWishlist;
use Magento\Wishlist\Model\ResourceModel\Wishlist\Collection;


class Wishlist extends \Magento\Wishlist\Model\Wishlist
{

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Catalog\Helper\Product $catalogProduct,
        Data $wishlistData,
        ResourceWishlist $resource,
        Collection $resourceCollection,
        StoreManagerInterface $storeManager,
        DateTime\DateTime $date,
        ItemFactory $wishlistItemFactory,
        CollectionFactory $wishlistCollectionFactory,
        ProductFactory $productFactory,
        Random $mathRandom,
        DateTime $dateTime,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Checkout\Model\SessionFactory $_checkoutSession,
        $useCurrentWebsite = true,
        array $data = [],
        Json $serializer = null,
        StockRegistryInterface $stockRegistry = null,
        ScopeConfigInterface $scopeConfig = null
    )
    {
        parent::__construct($context, $registry, $catalogProduct, $wishlistData, $resource, $resourceCollection, $storeManager, $date, $wishlistItemFactory, $wishlistCollectionFactory, $productFactory, $mathRandom, $dateTime, $productRepository, $useCurrentWebsite, $data, $serializer, $stockRegistry, $scopeConfig);
        $this->request = $request;
        $this->_checkoutSession = $_checkoutSession;
    }

    protected function _addCatalogProduct(Product $product, $qty = 1, $forciblySetQty = false)
    {

        $params = $this->request->getParams();
        $movetoWishlistvalues = [];
        $selectedCustomizedoptions = [];
        if (isset($params['pz_cart_properties'])) {
            if ($params['pz_cart_properties'] != '') {
                $addedParams = json_decode($params['pz_cart_properties'], true);
                if (is_array($addedParams)) {
                    if (!empty($addedParams)) {
                        foreach ($addedParams as $addedParamlabel => $addedParamValue) {
                            if($addedParamlabel != 'CustomImage') {
                                $selectedCustomizedoptions[] = [
                                    'label' => $addedParamlabel,
                                    'value' => $addedParamValue
                                ];
                            }
                        }
                    }
                }
            }
        }

        if (isset($params['item'])) {
            $selectedCustomizedoptions = [];
            $itemId = $params['item'];
            $session = $this->_checkoutSession->create();
            $items = $session->getQuote()->getAllItems();
            foreach ($items as $item) {
                if ($item->getId() == $itemId) {
                    $buyRequest = $item->getBuyRequest();
                    if (isset($buyRequest['pz_cart_properties'])) {
                        $pzCartProperties = $buyRequest['pz_cart_properties'];
                    }
                    //$additionalOptions = $item->getOptionByCode('additional_options');
                    if ($pzCartProperties) {
                        $additionalOptions = json_decode($pzCartProperties, true);
                        if (!empty($additionalOptions)) {
                            foreach ($additionalOptions as $addedParamlabel => $addedParamValue) {
                                if($addedParamlabel != 'CustomImage') {
                                    $selectedCustomizedoptions[] = [
                                        'label' => $addedParamlabel,
                                        'value' => $addedParamValue
                                    ];
                                }
                                else {
                                    $movetoWishlistvalues['edit_id'] = 1;
                                }
                                //$movetoWishlistvalues[$addedParamValue['label']] = $addedParamValue['value'];
                            }
                        }
                    }
                }
            }
            //if (!empty($selectedCustomizedoptions))
                //$movetoWishlistvalues['edit_id'] = 1;
        }

         if(isset($params['configurator_price'])){
            if(!empty($params['configurator_price']))
                $movetoWishlistvalues['configurator_price'] = $params['configurator_price'];
        }

        /*if(isset($params['params_addtocart'])){
            if(!empty($params['params_addtocart']))
                $movetoWishlistvalues['params_addtocart'] = $params['params_addtocart'];
        }*/
        $product->addCustomOption('additional_options', json_encode($selectedCustomizedoptions));
        if(empty($params) && empty($selectedCustomizedoptions)){
            if($product->getCustomOptions()){
                $customOptions = $product->getCustomOptions();
                foreach($customOptions as $customOption){
                    $customOptionValue = $customOption->getValue();
                    $selectedOptions = json_decode($customOptionValue,true);
                    if(isset($selectedOptions['pz_cart_properties'])){
                        $selectedOption = json_decode($selectedOptions['pz_cart_properties'],true);
                        if (array_key_exists("CustomImage",$selectedOption)){
                            foreach ($selectedOption as $addedParamlabel => $addedParamValue) {
                                $selectedCustomizedoptions[] = [
                                    'label' => $addedParamlabel,
                                    'value' => $addedParamValue
                                ];
                            }
                        }

                    }
                }
            }
        }
        $product->addCustomOption('additional_options', json_encode($selectedCustomizedoptions));

        $item = null;
        foreach ($this->getItemCollection() as $_item) {
            if ($_item->representProduct($product)) {
                $item = $_item;
                break;
            }
        }

        if ($item === null) {
            $storeId = $product->hasWishlistStoreId() ? $product->getWishlistStoreId() : $this->getStore()->getId();
            $item = $this->_wishlistItemFactory->create();
            $item->setProductId($product->getId());
            $item->setWishlistId($this->getId());
            $item->setAddedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));
            $item->setStoreId($storeId);
            $item->setOptions($product->getCustomOptions());
            $item->setProduct($product);
            $item->setQty($qty);
            $item->save();
            if ($item->getId()) {
                $this->getItemCollection()->addItem($item);
            }
        } else {
            $qty = $forciblySetQty ? $qty : $item->getQty() + $qty;
            $item->setQty($qty)->save();
        }
        if (!empty($movetoWishlistvalues)) {
            $item->mergeBuyRequest($movetoWishlistvalues);
        }

        $this->addItem($item);


        return $item;
    }
}
