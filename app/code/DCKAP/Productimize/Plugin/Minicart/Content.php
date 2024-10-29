<?php
/**
 * @author DCKAP <extensions@dckap.com>
 * @package DCKAP_Productimize
 * @copyright Copyright (c) 2017 DCKAP Inc (http://www.dckap.com)
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

namespace DCKAP\Productimize\Plugin\Minicart;

use Perficient\Catalog\Helper\Data;
use Magento\Checkout\Model\Cart;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;

/**
 * Class Image
 * @package DCKAP\Productimize\Plugin\Minicart
 */
class Content
{
    /**
     * Image constructor.
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * Data                                              $data
     * @param \Magento\Checkout\Model\Cart $cart
     */

    public function __construct(
        protected readonly \Magento\Framework\App\ResourceConnection $resource,
        protected readonly \Magento\Store\Model\StoreManagerInterface $storeManager,
        protected readonly  Data $data,
        protected readonly  Cart $cart
    ){
    }

    /**
     * @param $subject
     * @param $result
     * @param $item
     * @return mixed
     */
    public function afterGetItemData($subject, $result, $item)
    {

        $itemData = $item->getBuyRequest()->getData();
        if (isset($itemData['pz_cart_properties'])) {
            if ($itemData['pz_cart_properties'] != '') {
                $pzCartproperties = json_decode($itemData['pz_cart_properties'], true);
                if(isset($pzCartproperties['CustomImage'])){
                    $result['product_image']['src'] = $pzCartproperties['CustomImage'];

                    // minicart edit id changed
                    if (isset($itemData['edit_id'])) {
                        $result['configure_url'] .= 'edit_id/1/';
                    }
                }
            }
        }

        if(isset($item['product_type']) && $item['product_type'] === Configurable::TYPE_CODE) {
            $quoteItem = $this->cart?->getQuote()?->getItemById($item['item_id']);
            if (empty($quoteItem)) {
                return $result;
            }


            $configProduct = $quoteItem->getProduct()?->getCustomOption('simple_product')?->getProduct();

            if (empty($configProduct)) {
                return $result;
            }

            $result['product_name'] = $this->data->getSimpleProductName($quoteItem->getProduct());
            $result['product_url'] = $this->data->getSimpleProductURL($quoteItem->getProduct());
        }

        return $result;
    }
}
