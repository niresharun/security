<?php

namespace Perficient\Checkout\ViewModel;

use Perficient\QuickShip\Helper\Data as QuickShipHelper;
use Magento\Company\Block\Company\Account\Dashboard\RoleInfo;
use Magento\Framework\Escaper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Customer\Model\Session as CustomerSeesion;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use Magento\Store\Model\ScopeInterface;

class CheckoutViewModel implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    final const CUSTOMER_CUSTOMER = "Customer's Customer";
    final const QUICK_SHIP_FIELD = 'is_quick_ship';
    final const XML_PATH_RESTRICT_CART_CHECKOUT = 'restrictcustomer/cartcheckout/is_enabled';

    public static $defaultConfLabel = [
        'liner_sku' => 'Liner',
        'frame_default_sku' => 'Frame',
        'top_mat_default_sku' => 'Top Mat',
        'bottom_mat_default_sku' => 'Bottom Mat',
        //'side-mark' => 'Side Mark',
        //'bottom_mat_sku' => 'Bottom Mat SKUs',
        'frame_width' => 'Frame Width',
        //'frame_depth' => 'Frame Depth',
        //'frame_color_default' => 'Frame Color',
        'item_height' => 'Item Height',
        'item_width' => 'Item Width',
        'medium' => 'Medium',
        'glass_width' => 'Glass Width',
        'glass_height' => 'Glass Height',
        'art_work_color' => 'Artwork Color',
        'side_mark' => 'Side Mark',
        'liner_width' => 'Liner Width',
        //'liner_color_default' => 'Liner Color',
        'bottom_mat_size_bottom' => 'Bottom Mat Size Bottom',
        'bottom_mat_size_left' => 'Bottom Mat Size Left',
        'bottom_mat_size_right' => 'Bottom Mat Size Right',
        'bottom_mat_size_top' => 'Bottom Mat Size Top',
        //'bottom_mat_color_default' => 'Bottom Mat Color',
        'image_height' => 'Image Height',
        'image_width' => 'Image Width',
        'top_mat_size_bottom' => 'Top Mat Size Bottom',
        'top_mat_size_left' => 'Top Mat Size Left',
        'top_mat_size_right' => 'Top Mat Size Right',
        'top_mat_size_top' => 'Top Mat Size Top',
        //'top_mat_color_default' => 'Top Mat Color',
        'treatment' => 'Treatment',
        'default_frame_depth' => 'Frame Depth',
        'default_liner_depth' => 'Liner Depth',
        'default_frame_color' => 'Frame Color',
        'default_liner_color' => 'Liner Color',
        'default_top_mat_color' => 'Top Mat Color',
        'default_bottom_mat_color' => 'Bottom Mat Color'
    ];

    public static $defaultConfSizeLabel = [
        'item_height' => 'Item Height',
        'item_width' => 'Item Width'
    ];

    /**
     * @var array
     * Used at multiple places - need to check if other details needs to be added****
     */
    public static $textOnlyOptions = [
        'medium' => 'Medium',
        'treatment' => 'Treatment',
        //?//'frame_skus' => 'Frame SKUs',
        'frame_default_sku' => 'Frame',
        'frame_width' => 'Frame Width',
        'item_height' => 'Item Height',
        'item_width' => 'Item Width',
        //?//'top_mat_skus' => 'Top Mat SKUs',
        'top_mat_default_sku' => 'Top Mat',
        'liner_width' => 'Liner Width',
        'bottom_mat_size_bottom' => 'Bottom Mat Size Bottom',
        'bottom_mat_size_left' => 'Bottom Mat Size Left',
        'bottom_mat_size_right' => 'Bottom Mat Size Right',
        'bottom_mat_size_top' => 'Bottom Mat Size Top',
        'top_mat_size_bottom' => 'Top Mat Size Bottom',
        'top_mat_size_left' => 'Top Mat Size Left',
        'top_mat_size_right' => 'Top Mat Size Right',
        'top_mat_size_top' => 'Top Mat Size Top',
    ];

    public function __construct(
        protected RoleInfo               $roleInfo,
        protected Escaper                $escaper,
        protected ScopeConfigInterface   $scopeConfig,
        protected CustomerSeesion        $customerSession,
        private readonly Json            $json,
        private readonly LoggerInterface $logger,
        protected QuickShipHelper        $quickShipHelperData
    )
    {
    }

    public function getCurrentUserRole()
    {
        $currentUserRole = $this->roleInfo->getCustomerRoles();
        $currentUserRole = $this->escaper->escapeHtml($currentUserRole);

        return $currentUserRole[0] ?? '';
    }

    public function isRestrictCartAndCheckout(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_RESTRICT_CART_CHECKOUT,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }

    public function getDefaultConfigurationJson($defaultConf)
    {
        $defaultSize = '';
        $jsonStr = '';
        $json = [];
        if (!empty($defaultConf)) {
            try {
                $defaultConfigurationAttributes = self::$defaultConfLabel;
                $rawDataArray = $this->json->unserialize($defaultConf);
                $attributeValueArray = [];
                $attributeLabelArray = [];
                foreach ($defaultConfigurationAttributes as $key => $defaultConfigurationAttribute) {
                    if (array_key_exists($key, $rawDataArray)) {
                        $attributeValue = explode(':', (string)$rawDataArray[$key]);
                        $attributeValueArray[$key] = trim($attributeValue[0]);
                        if (!isset($attributeValue[1])) {
                            $attributeLabelArray[$key] = self::$defaultConfLabel[$key];
                        } else {
                            $attributeLabelArray[$key] = trim($attributeValue[1]);
                        }
                    } else {
                        $attributeValueArray[$key] = '';
                        $attributeLabelArray[$key] = $defaultConfigurationAttribute;
                    }
                }

                if (!empty($attributeValueArray) && !empty($attributeLabelArray)) {
                    $dataArray = $attributeValueArray;
                    foreach ($dataArray as $key => $value) {
                        if (!array_key_exists($key, self::$textOnlyOptions)) {
                            $json[$attributeLabelArray[$key]] = $value;
                        } else {
                            $json[self::$defaultConfLabel[$key]] = $value;
                        }
                    }

                    if (count($json) > 0) {
                        $jsonStr = $this->json->serialize($json);
                    }
                }
            } catch (\Exception $e) {
                $this->logger->debug($e->getMessage());
            }
        }
        return ['dataArray' => $json, 'jsonStr' => $jsonStr];
    }

    public function getDefaultConfigurationSize($defaultConf)
    {
        $sizeLabels = [];
        try {
            if (isset($defaultConf) && !empty($defaultConf)) {
                $rawDataArray = $this->json->unserialize($defaultConf);
                $defaultConfigurationAttributes = self::$defaultConfSizeLabel;
                $itemWidth = $itemHeight = '';
                foreach ($defaultConfigurationAttributes as $key => $defaultConfigurationAttribute) {
                    if (array_key_exists($key, $rawDataArray)) {
                        $attributeValue = explode(':', (string)$rawDataArray[$key]);
                        if (in_array($key, array_keys(self::$defaultConfSizeLabel))) {
                            if ($key == 'item_width') {
                                $itemWidth = $attributeValue[0];
                            } elseif ($key == 'item_height') {
                                $itemHeight = $attributeValue[0];
                            } else {
                                // some other stuff can go here
                            }
                        }
                    }
                }
                if ($itemWidth != '' && $itemHeight != '') {
                    $sizeLabels['Size'] = $itemWidth . '"w' . ' x ' . $itemHeight . '"h';
                }
                return ['labels' => $sizeLabels];
            }
        } catch (\Exception $e) {
            $this->logger->debug($e->getMessage());
        }
        return ['labels' => $sizeLabels];
    }

    public function isFromQuickShip()
    {
        return $this->quickShipHelperData->isFromQuickShip();
    }
}
