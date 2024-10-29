<?php

namespace DCKAP\Productimize\Helper;

use Magento\Catalog\Model\ProductFactory;
use Magento\Checkout\Model\SessionFactory as CheckoutSessionFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Wishlist\Model\Item\OptionFactory;
use Perficient\Company\Helper\Data as PerficientCompanyHelper;
use Perficient\Productimize\Model\ProductConfiguredPrice;


class Data extends AbstractHelper
{
    public static $defaultConfLabel = array(
        'treatment' => 'Treatment',
        'medium' => 'Medium',
        'item_height' => 'Item Height',
        'item_width' => 'Item Width',
        'frame_default_sku' => 'Frame',
        'frame_width' => 'Frame Width',
        'default_frame_depth' => 'Frame Depth',
        'default_frame_color' => 'Frame Color',
        'liner_sku' => 'Liner',
        'liner_width' => 'Liner Width',
        'default_liner_depth' => 'Liner Depth',
        'default_liner_color' => 'Liner Color',
        'top_mat_default_sku' => 'Top Mat',
        'top_mat_size_bottom' => 'Top Mat Size Bottom',
        'top_mat_size_left' => 'Top Mat Size Left',
        'top_mat_size_right' => 'Top Mat Size Right',
        'top_mat_size_top' => 'Top Mat Size Top',
        'default_top_mat_color' => 'Top Mat Color',
        'bottom_mat_default_sku' => 'Bottom Mat',
        'bottom_mat_size_bottom' => 'Bottom Mat Size Bottom',
        'bottom_mat_size_left' => 'Bottom Mat Size Left',
        'bottom_mat_size_right' => 'Bottom Mat Size Right',
        'bottom_mat_size_top' => 'Bottom Mat Size Top',
        'default_bottom_mat_color' => 'Bottom Mat Color',
        'image_height' => 'Image Height',
        'image_width' => 'Image Width',
        'glass_width' => 'Glass Width',
        'glass_height' => 'Glass Height',
        'art_work_color' => 'Artwork Color',
        'side_mark' => 'Side Mark'
    );

    public static $alternateDefaultConfKeys = [
        'medium' => 'medium_default_sku',
        'treatment' => 'treatment_default_sku',
        'Size' => 'size_default_sku',
        'frame_default_sku' => 'frame_default_sku',
        'top_mat_default_sku' => 'top_mat_default_sku',
        'bottom_mat_default_sku' => 'bottom_mat_default_sku',
        'liner_sku' => 'liner_default_sku',
        'glass_width' => 'glass_width',
        'glass_height' => 'glass_height',
        'art_work_color' => 'art_work_color',
        'side_mark' => 'side_mark'
    ];

    public static $editUrlLabelKeys = array(
        'glass_width' => 'glass_width',
        'glass_height' => 'glass_height',
        'liner_sku' => 'liner',
        'frame_default_sku' => 'frame',
        'top_mat_default_sku' => 'top mat',
        'top mat' => 'top mat',
        'topMat' => 'top mat',
        'topmat' => 'top mat',
        'bottom_mat_default_sku' => 'bottom mat',
        'bottom mat' => 'bottom mat',
        'bottomMat' => 'bottom mat',
        'bottommat' => 'bottom mat',
        'medium' => 'medium',
        'treatment' => 'treatment',
        'size' => 'size',
        'liner' => 'liner',
        'frame' => 'frame',
        'artworkcolor' => 'artwork color',
        'art_work_color' => 'artwork color',
        'sidemark' => 'sidemark',
        'side_mark' => 'sidemark',
        'side mark' => 'sidemark'
    );
    public static $rendererFrameCustomImageType = [
        'Corner'=>'cornerImage',
        'Length'=>'lengthImage'
    ];
    public static $rendererMatCustomImageType = array(
        'renderer'=>'rendererImage'
    );

    protected $request;
    protected $_checkoutSession;
    protected $optionFactory;
    protected $customerSession;
    protected $priceCurrencyInterface;
    protected $perficientCompanyHelper;
    protected $perficientPriceModel;
    protected $storeManager;
    protected $collectionFactory;
    protected $curl;
    protected $scopeConfig;
    protected $directoryList;
    protected $json;
    private $productimizeProductDataModel;
    private $productFactory;
    private $resourceConnection;

    public function __construct(
        Context                 $context,
        Http                    $request,
        CheckoutSessionFactory  $_checkoutSession,
        OptionFactory           $optionFactory,
        SessionFactory          $customerSession,
        PerficientCompanyHelper $perficientCompanyHelper,
        PriceCurrencyInterface  $priceCurrencyInterface,
        StoreManagerInterface   $storeManager,
        ProductConfiguredPrice  $productConfiguredPrice,
        Curl                    $curl,
        ScopeConfigInterface    $scopeConfig,
        DirectoryList           $directoryList,
        Json                    $json,
        ProductFactory $productFactory,
        ResourceConnection $resourceConnection
    )
    {
        parent::__construct($context);
        $this->request = $request;
        $this->_checkoutSession = $_checkoutSession;
        $this->optionFactory = $optionFactory;
        $this->customerSession = $customerSession;
        $this->perficientCompanyHelper = $perficientCompanyHelper;
        $this->priceCurrencyInterface = $priceCurrencyInterface;
        $this->storeManager = $storeManager;
        $this->perficientPriceModel = $productConfiguredPrice;
        $this->curl = $curl;
        $this->scopeConfig = $scopeConfig;
        $this->directoryList = $directoryList;
        $this->json = $json;
        $this->productFactory = $productFactory;
        $this->resourceConnection = $resourceConnection;
    }
    public function getProductimizeJsonPath () {

        $pubFolderPath = $this->directoryList->getPath('pub');
        $productimizeJsonFolder = $pubFolderPath . '/productimize_json/';
        if (!file_exists($productimizeJsonFolder)) {
            mkdir($productimizeJsonFolder, 0777, true);
        }

        return $productimizeJsonFolder;
    }

    /**
     * @param $jsonString
     * @return array|bool|float|int|mixed|null|string
     */
    public function getUnserializeData($jsonString)
    {
        return $this->json->unserialize($jsonString);
    }

    /**
     * @param $data
     * @return bool|false|string
     */
    public function getSerializeData($data)
    {
        return $this->json->serialize($data);
    }

    public function getCurrentpagehandle()
    {
        return $this->request->getFullActionName();
    }

    public function checkEditidinurl()
    {
        if ($this->_getRequest()->getParam('edit_id'))
            return true;
        else if ($this->_getRequest()->getParam('type'))
            return true;
        else return false;
    }

    public function getParaminrequest()
    {
        if ($this->_getRequest()->getParam('id'))
            return $this->_getRequest()->getParam('id');
        else return '';
    }

    public function getAdditionaloptionsbyquoteId($quoteId)
    {
        $session = $this->_checkoutSession->create();
        $items = $session->getQuote()->getAllItems();
        $movetoWishlistvalues = [];
        if (!empty($items)) {
            foreach ($items as $item) {
                if ($item->getId() == $quoteId) {
                    $additionalOptions = $item->getOptionByCode('additional_options');
                    if ($additionalOptions) {
                        $additionalOptions = json_decode($item->getOptionByCode('additional_options')->getValue());
                        if (!empty($additionalOptions)) {
                            foreach ($additionalOptions as $additionalOption) {
                                $giftParameters[] = [
                                    'label' => $additionalOption->label,
                                    'value' => $additionalOption->value
                                ];
                                $trimval = trim($additionalOption->value, ',');
                                $Val = explode(',', $trimval);
                                $movetoWishlistvalues[strtolower($additionalOption->label)] = $Val[0];
                            }
                        }
                    }
                }
            }
        }
        return $movetoWishlistvalues;
    }

    public function getQuoteItemQty($quoteItemId)
    {
        $qty = 1;
        $session = $this->_checkoutSession->create();
        $items = $session->getQuote()->getAllItems();
        if (!empty($items)) {
            foreach ($items as $item) {
                if ($item->getId() == $quoteItemId) {
                    $qty = $item->getQty();
                }
            }
        }
        return $qty;
    }

    public function getWishlistItemQty($itemId)
    {
        $qty = 1;
        $options = $this->optionFactory->create()->getCollection()->addItemFilter([$itemId]);
        $options->addFieldToFilter('code', 'info_buyRequest');
        if (!empty($data = $options->getData())) {
            foreach ($data as $_data) {
                if (isset($_data['value'])) {
                    $additionalOptions = json_decode($_data['value']);
                }
            }
            if (!empty($additionalOptions)) {
                foreach ($additionalOptions as $key => $value) {
                    if ($key == 'qty') {
                        $qty = $value;
                    }
                }
            }
        }
        return $qty;
    }

    public function getAllAdditionalOptionsByQuoteId($quoteId, $defaultConf)
    {
        $session = $this->_checkoutSession->create();
        $items = $session->getQuote()->getAllItems();
        $movetoWishlistvalues = [];

        $productLabelKeyJson = $this->getKeysAndLabelsByDefaultConfigurations($defaultConf);
        $productLabelKeys = ($productLabelKeyJson) ? json_decode($productLabelKeyJson['labelsByKey'], 1) : '';

        $labelKeys = self::$editUrlLabelKeys;
        //print_r($labelKeys);

        $sizeWidth = ''; $sizeHeight = '';

        $data = [];
        if (!empty($items)) {
            foreach ($items as $item) {
                if ($item->getId() == $quoteId) {
                    $additionalOptions = $item->getOptionByCode('additional_options');


                    if ($additionalOptions) {

                        $additionalOptions = json_decode($item->getOptionByCode('additional_options')->getValue());
                        if (!empty($additionalOptions)) {

                            foreach ($additionalOptions as $additionalOption) {
                                $giftParameters[] = [
                                    'label' => $additionalOption->label,
                                    'value' => $additionalOption->value
                                ];
                                $trimval = trim($additionalOption->value, ',');
                                $Val = explode(',', $trimval);


                                $currLabelKey = array_search($additionalOption->label, $productLabelKeys);

                                if (isset($labelKeys[strtolower($currLabelKey)])) {
                                    if (strtolower($currLabelKey) == 'glass_width' || strtolower($currLabelKey) == 'glass_height') {
                                        if (strtolower($currLabelKey) == 'glass_width')
                                        {
                                            $sizeWidth = $additionalOption->value;
                                        }
                                        if (strtolower($currLabelKey) == 'glass_height')
                                        {
                                            $sizeHeight = $additionalOption->value;
                                        }
                                    }
                                    else {
                                        $data[$labelKeys[strtolower($currLabelKey)]] = $additionalOption->value;
                                        $movetoWishlistvalues[$labelKeys[strtolower($currLabelKey)]] = $Val[0];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $size = $sizeWidth . '×' . $sizeHeight;
        $data['size'] = $size;
        $movetoWishlistvalues['size'] = $size;

        return ['fullOptions' => $data, 'optionValueWithSkuOnly' => $movetoWishlistvalues];
    }

    public function getAdditionaloptionsbywishlistId($id)
    {
        $options = $this->optionFactory->create()->getCollection()->addItemFilter([$id]);
        $options->addFieldToFilter('code', 'additional_options');
        $additionalOptionsarray = $options->getData();
        $additionalOptions = [];
        $movetoWishlistvalues = [];
        foreach ($additionalOptionsarray as $additionOptionsarraydata) {
            if (isset($additionOptionsarraydata['value'])) {
                $additionalOptions = json_decode($additionOptionsarraydata['value']);
            }
        }
        if (!empty($additionalOptions)) {
            foreach ($additionalOptions as $additionalOption) {
                $giftParameters[] = [
                    'label' => $additionalOption->label,
                    'value' => $additionalOption->value
                ];
                $trimval = trim($additionalOption->value, ',');
                $Val = explode(',', $trimval);
                $movetoWishlistvalues[strtolower($additionalOption->label)] = $Val[0];
            }
        }
        return $movetoWishlistvalues;
    }


    public function getAllAdditionalOptionsByWishlistId($id, $defaultConf)
    {
        $options = $this->optionFactory->create()->getCollection()->addItemFilter([$id]);
        $options->addFieldToFilter('code', 'additional_options');
        $additionalOptionsarray = $options->getData();
        $additionalOptions = [];
        $movetoWishlistvalues = [];
        $data = [];
        $sizeWidth = ''; $sizeHeight = '';


        $productLabelKeyJson = $this->getKeysAndLabelsByDefaultConfigurations($defaultConf);
        $productLabelKeys = ($productLabelKeyJson) ? json_decode($productLabelKeyJson['labelsByKey'], 1) : '';


        $labelKeys = self::$editUrlLabelKeys;

        foreach ($additionalOptionsarray as $additionOptionsarraydata) {
            if (isset($additionOptionsarraydata['value'])) {
                $additionalOptions = json_decode($additionOptionsarraydata['value']);
            }
        }
        if (!empty($additionalOptions)) {
            foreach ($additionalOptions as $additionalOption) {
                $giftParameters[] = [
                    'label' => $additionalOption->label,
                    'value' => $additionalOption->value
                ];
                $trimval = trim($additionalOption->value, ',');
                $Val = explode(',', $trimval);

                $currLabelKey = array_search($additionalOption->label, $productLabelKeys);
                if (isset($labelKeys[strtolower($currLabelKey)])) {
                    if (strtolower($currLabelKey) == 'glass_width' || strtolower($currLabelKey) == 'glass_height') {
                        if (strtolower($currLabelKey) == 'glass_width')
                        {
                            $sizeWidth = $additionalOption->value;
                        }
                        if (strtolower($currLabelKey) == 'glass_height')
                        {
                            $sizeHeight = $additionalOption->value;
                        }
                    }
                    else {
                        $data[$labelKeys[strtolower($currLabelKey)]] = $additionalOption->value;
                        $movetoWishlistvalues[$labelKeys[strtolower($currLabelKey)]] = $Val[0];
                    }
                }
            }
        }
        $size = $sizeWidth . '×' . $sizeHeight;
        $data['size'] = $size;
        $movetoWishlistvalues['size'] = $size;
        //return $movetoWishlistvalues;
        return ['fullOptions' => $data, 'optionValueWithSkuOnly' => $movetoWishlistvalues];
    }

    public function getImageurlforwishlistId($id)
    {
        $imageUrl = '';
        $options = $this->optionFactory->create()->getCollection()->addItemFilter([$id]);
        $options->addFieldToFilter('code', 'info_buyRequest');
        $infoBuyrequestarray = $options->getData();
        $infoBuyrequest = [];
        foreach ($infoBuyrequestarray as $infoBuyrequestarraydata) {
            if (isset($infoBuyrequestarraydata['value'])) {
                $infoBuyrequest = json_decode($infoBuyrequestarraydata['value'], true);
            }
        }
        if (!empty($infoBuyrequest)) {
            return $this->getImageurlfrombuyrequestdata($infoBuyrequest);
        }
        return $imageUrl;
    }

    public function getImageurlfrombuyrequestdata($buyRequestdata)
    {
        $imageUrl = '';
        if (isset($buyRequestdata['pz_cart_properties'])) {
            if ($buyRequestdata['pz_cart_properties'] != '') {
                $pzCartproperties = json_decode($buyRequestdata['pz_cart_properties'], true);
                if (isset($pzCartproperties['CustomImage'])) {
                    $imageUrl = $pzCartproperties['CustomImage'];
                }
            }
        }
        return $imageUrl;
    }

    public function getEditUrlQryString($urlQryStr)
    {
        $keyLabelArr = array();

        if ($urlQryStr) {
            $urlQryStringArr = explode('--', $urlQryStr);
            $editLabelKeys = self::$editUrlLabelKeys;
            foreach ($urlQryStringArr as $key => $value) {
                $currKeyValue = explode('=', $value);

                if ($currKeyValue[0] && $currKeyValue[0] != "type" && array_key_exists(strtolower($currKeyValue[0]), $editLabelKeys)) {
                    $keyLabelArr[$editLabelKeys[strtolower($currKeyValue[0])]] = $currKeyValue[1];
                }
            }
        }
        return $keyLabelArr;
    }

    public function getAdditionaloptionsWithCustomImage($quoteId, $statusId)
    {
        $productimizeData = false;
        if ($statusId == 1) {
            $session = $this->_checkoutSession->create();
            $items = $session->getQuote()->getAllItems();
            if (!empty($items)) {
                foreach ($items as $item) {
                    if ($item->getId() == $quoteId) {

                        $infoBuyRequest  = $item->getOptionByCode('info_buyRequest');
                        $infoBuyrequestarray = $infoBuyRequest->getData();
                        $infoBuyrequest = [];
                        foreach ($infoBuyrequestarray as $infoBuyrequestarraykey => $infoBuyrequestarraydata) {
                            if ($infoBuyrequestarraykey == 'value') {
                                $infoBuyrequest = json_decode($infoBuyrequestarraydata, true);
                                if(isset($infoBuyrequest['pz_cart_properties']))    {
                                    $infoCartProperties = json_decode($infoBuyrequest['pz_cart_properties'], true);
                                    if(isset($infoCartProperties['CustomImage']))   {
                                        $productimizeData = true;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $options = $this->optionFactory->create()->getCollection()->addItemFilter([$quoteId]);
            $infoBuyRequest  = $options->addFieldToFilter('code', 'info_buyRequest');
            $infoBuyrequestarray = $infoBuyRequest->getData();
            $infoBuyrequest = [];
            foreach ($infoBuyrequestarray as $infoBuyrequestarraykey => $infoBuyrequestarraydata) {
                if ($infoBuyrequestarraykey == 'value') {
                    $infoBuyrequest = json_decode($infoBuyrequestarraydata['value'], true);
                    if(isset($infoBuyrequest['pz_cart_properties']))    {
                        $infoCartProperties = json_decode($infoBuyrequest['pz_cart_properties'], true);
                        if(isset($infoCartProperties['CustomImage']))   {
                            $productimizeData = true;
                        }
                    }
                }
            }
        }
        return $productimizeData;
    }

    public function getAdditionaloptionsWithCustomImage_old($quoteId, $statusId)
    {
        $productimizeData = false;
        if ($statusId == 1) {
            $session = $this->_checkoutSession->create();
            $items = $session->getQuote()->getAllItems();
            if (!empty($items)) {
                foreach ($items as $item) {
                    if ($item->getId() == $quoteId) {
                        $additionalOptions = $item->getOptionByCode('additional_options');
                        if ($additionalOptions) {
                            $additionalOptions = json_decode($item->getOptionByCode('additional_options')->getValue());
                        }
                    }
                }
            }
        } else {
            $options = $this->optionFactory->create()->getCollection()->addItemFilter([$quoteId]);
            $options->addFieldToFilter('code', 'additional_options');
            $additionalOptionsarray = $options->getData();
            $additionalOptions = [];
            foreach ($additionalOptionsarray as $additionOptionsarraydata) {
                if (isset($additionOptionsarraydata['value'])) {
                    $additionalOptions = json_decode($additionOptionsarraydata['value']);
                }
            }
        }
        if (!empty($additionalOptions)) {
            foreach ($additionalOptions as $additionalOption) {
                if (strpos(strtolower($additionalOption->label), 'medium') !== false) {
                    $productimizeData = true;
                }
            }
        }
        return $productimizeData;
    }



    public function getKeysAndLabelsByDefaultConfigurations($defaultConf)
    {
        $label = [];
        if ($defaultConf) {
            $dataArray = json_decode($defaultConf, true);
            foreach (self::$alternateDefaultConfKeys as $key => $value) {
                if (array_key_exists($key, $dataArray)) {
                    $keyLabelArr = explode(':', $dataArray[$key]);

                    $label[$key] = isset($keyLabelArr[1]) ? $keyLabelArr[1] : '';
                } else {
                    $label[$key] = isset(self::$defaultConfLabel[$key]) ? self::$defaultConfLabel[$key] : $key;
                }

            }
            $label['Size'] = 'Size';
        }
        return ['labelsByKey' => json_encode($label, 1)];
    }

    public function getDefaultConfigurationJson($defaultConf)
    {
        $defaultSize = '';
        $json = [];
        $label = [];


        if ($defaultConf) {
            $dataArray = json_decode($defaultConf, true);
            if (isset($dataArray['image_width']) && isset($dataArray['image_height'])) {

                $imageWidthArr = explode(':', $dataArray['image_width']);
                $imageHeightArr = explode(':', $dataArray['image_height']);

                $defaultSize = $imageWidthArr[0] . '×' . $imageHeightArr[0];
                if ($imageWidthArr[0] < $imageHeightArr[0]) {
                    $defaultSize = $imageHeightArr[0] . '×' . $imageWidthArr[0];
                }
                //$label['Size'] = $defaultSize;
                $json['size_default_sku'] = $defaultSize;
            }
            foreach ($dataArray as $key => $value) {
                if ($key != 'item_width' && $key != 'item_height') {
                    $keyLabelArr = explode(':', $value);
                    if (array_key_exists($key, self::$defaultConfLabel)) {

                        $currJsonKey = $key;
                        $currentKey = self::$defaultConfLabel[$key];

                        if (array_key_exists($key, self::$alternateDefaultConfKeys)) {
                            $currJsonKey = $currentKey = self::$alternateDefaultConfKeys[$key];
                        }
                        $json[$currJsonKey] = $keyLabelArr[0];
                        if (array_key_exists($key, self::$alternateDefaultConfKeys)) {
                            $label[$currentKey] = isset($keyLabelArr[1]) ? $keyLabelArr[1] : '';
                        }

                    }
                }
            }

            $allDefConfData = $this->getAllDefaultConfigLabelson($defaultConf);
        }

        return ['jsonStr' => json_encode($json, 1), 'labelStr' => json_encode($label, 1), 'pzCartPropertiesData' => $allDefConfData];
    }

    public function getDefaultConfJsonSpecificationLabel($defaultConf)
    {
        $defaultSize = '';
        $json = [];
        $label = [];
        $displayData = [];


        if ($defaultConf) {
            $dataArray = json_decode($defaultConf, true);
            if (isset($dataArray['item_width']) && isset($dataArray['item_height'])) {

                $imageWidthArr = explode(':', $dataArray['item_width']);
                $imageHeightArr = explode(':', $dataArray['item_height']);

                $defaultSize = $imageWidthArr[0] . '″w × ' . $imageHeightArr[0] . '″h';
                /*if ($imageWidthArr[0] < $imageHeightArr[0]) {
                    echo "coming here ";
                    $defaultSize = $imageHeightArr[0] . '″w ×' . $imageWidthArr[0] . '″h';
                }*/
                $displayData['Size'] = $defaultSize;
            }
            foreach ($dataArray as $key => $value) {
                if ($key != 'image_width' && $key != 'image_height') {
                    $keyLabelArr = explode(':', $value);
                    if (array_key_exists($key, self::$alternateDefaultConfKeys)) {

                        $displayData[$key] = array(
                            'value' => trim($keyLabelArr[0]) ? trim($keyLabelArr[0]) : 'None',
                            "label" => $keyLabelArr[1]
                        );

                    }
                    //}
                }
            }
        }
        $outputData = array();

        if (isset($displayData['medium'])) {
            $outputData[$displayData['medium']['label']] = $displayData['medium']['value'];
        } else {
            $outputData[self::$defaultConfLabel['medium']] = 'None';
        }
        if (isset($displayData['treatment'])) {
            $outputData[$displayData['treatment']['label']] = $displayData['treatment']['value'];
        } else {
            $outputData[self::$defaultConfLabel['treatment']] = 'None';
        }
        if (isset($displayData['Size'])) {
            $outputData['Size'] = $displayData['Size'];
        } else {
            $outputData['Size'] = 'None';
        }
        if (isset($displayData['frame_default_sku'])) {
            $outputData[$displayData['frame_default_sku']['label']] = $displayData['frame_default_sku']['value'];
        } else {
            $outputData[self::$defaultConfLabel['frame_default_sku']] = 'None';
        }
        if (isset($displayData['top_mat_default_sku'])) {
            $outputData[$displayData['top_mat_default_sku']['label']] = $displayData['top_mat_default_sku']['value'];
        } else {
            $outputData[self::$defaultConfLabel['top_mat_default_sku']] = 'None';
        }
        if (isset($displayData['bottom_mat_default_sku'])) {
            $outputData[$displayData['bottom_mat_default_sku']['label']] = $displayData['bottom_mat_default_sku']['value'];
        } else {
            $outputData[self::$defaultConfLabel['bottom_mat_default_sku']] = 'None';
        }
        if (isset($displayData['liner_sku'])) {
            $outputData[$displayData['liner_sku']['label']] = $displayData['liner_sku']['value'];
        } else {
            $outputData[self::$defaultConfLabel['liner_sku']] = 'None';
        }
        return ['displayFormattedStr' => json_encode($outputData)];
    }

    public function isChanged($firstValue, $secondValue)
    {
        if ($firstValue != $secondValue) {
            return true;
        }
        return false;

    }

    // Get Module's system configuration By configuration code
    public function getGeneralConfigByCode($code)
    {
        return $this->scopeConfig->getValue($code, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getCustomerAccessRestrictionCode()
    {
        $restrictedAccess = 1;
        $session = $this->customerSession->create();
        if ($this->perficientCompanyHelper->isRestrictCartAndCheckout()) {

            if ($session->isLoggedIn()) {
                // Restrict Add to Cart Button for customer's customer
                $currentUserRole = $this->perficientCompanyHelper->getCurrentUserRole();
                $currentUserRole = $currentUserRole ? htmlspecialchars_decode($currentUserRole, ENT_QUOTES) : '';
                if (strcmp($currentUserRole, "Customer's Customer") == 0) {
                    $restrictedAccess = 2;
                }
                // Restrict Add to Cart button if price multiplier is 0x
                $multiplier = $session->getMultiplier() ?? 1;
                if ($multiplier == 0) {
                    $restrictedAccess = 3;
                }

            } else {
                // Restrict Add to Cart Button for guest customer
                $restrictedAccess = 3;
            }
        }
        return $restrictedAccess;
    }

    public function getCurrencySymbol()
    {
        return $this->_storeManager->getStore()->getBaseCurrency()->getCurrencySymbol();
    }

    public function formatPrice($price)
    {
        return $this->priceCurrencyInterface->format($price);
    }

    public function getDisplayPrice($productId, $superScript = false)
    {
        $productPrice = $this->priceCurrencyInterface->format(0);
        if ($productId) {
            $productPrice = $this->priceCurrencyInterface->format($this->perficientPriceModel->getDisplayPrice($productId));
        }

        if ($superScript && !empty($productPrice)) {
            //$pattern = '/\$\d\.([\d]{2})/';
            $pattern = '/\.([\d]{2})/';
            $productPrice = preg_replace($pattern, '<sup>$1</sup>', $productPrice);
        }

        return $productPrice;
    }

    public function getDisplayAndSellingPrice($productId, $productJson)
    {

        $configuredDisplayPrice = 0.00;
        $configuredSellingPrice = 0.00;

        if (isset($productId) && isset($productJson)) {
            $configuredDisplayPrice = $this->perficientPriceModel->getConfiguredDisplayPrice($productId, $productJson);
            $configuredSellingPrice = $this->perficientPriceModel->getConfiguredSellingPrice($productId, $productJson);

        }
        $priceArr = [
            'configureddisplayprice' => $this->priceCurrencyInterface->format($configuredDisplayPrice),
            'configuredsellingprice' => number_format($configuredSellingPrice, 2, '.', '')
        ];
        return $priceArr;

    }
    public function generateImageInNodeJs($relatedProductIds, $buyRequestdata, $defaultConf)
    {

        $productLabelKeyJson = $this->getAllDefaultConfigLabelson($defaultConf);
        $productLabelKeys = ($productLabelKeyJson) ? json_decode($productLabelKeyJson, 1) : '';
        $paramSkus = array();
        $paramSkus = $relatedProductIds;

        $artworkData = array();
        $valueWithDefKey = array();

        $designedImageNameParams = array();
        $existingDesignedImageNames = array();

        if (isset($buyRequestdata['pz_cart_properties'])) {
            if ($buyRequestdata['pz_cart_properties'] != '') {

                $pzCartproperties = json_decode($buyRequestdata['pz_cart_properties'], true);
                foreach ($pzCartproperties as $pzCartproperty => $pzCartpropertyValue) {
                    $currLabelKey = array_search($pzCartproperty, $productLabelKeys);

                    if (isset($currLabelKey)) {
                        $valueWithDefKey[$currLabelKey] = $pzCartpropertyValue;
                    }
                }
                if (isset($valueWithDefKey)) {

                    if (isset($valueWithDefKey['medium'])) {
                        $designedImageNameParams['medium'] = $valueWithDefKey['medium'];
                    }
                    if (isset($valueWithDefKey['treatment'])) {
                        $designedImageNameParams['treatment'] = $valueWithDefKey['treatment'];
                    }
                    if (isset($valueWithDefKey['item_width'])) {
                        $artworkData['image'] = [
                            "dimension" => [
                                "x" => isset($valueWithDefKey['item_width']) ? (float)$valueWithDefKey['item_width'] : 15,
                                "y" => isset($valueWithDefKey['item_height']) ? (float)$valueWithDefKey['item_height'] : 15,
                            ]
                        ];
                        $artworkWatermarkData = $this->getWatermarkImageDataByType('image');
                        if ($artworkWatermarkData && isset($artworkWatermarkData['url'])) {
                            $artworkData['watermark'] = [
                                "url" => $artworkWatermarkData['url'],
                                "position" => isset($artworkWatermarkData['position']) ? $artworkWatermarkData['position'] : 'center',
                                "opacity" => isset($artworkWatermarkData['opacity']) ? $artworkWatermarkData['opacity'] : 100,
                                "dimension" => [
                                    "x" => isset($artworkWatermarkData['width']) ? ( (float)$artworkWatermarkData['width']) : 100,
                                    "y" => isset($artworkWatermarkData['height']) ? ((float) $artworkWatermarkData['height'] ): 100

                                ]
                            ];
                        }
                        $designedImageNameParams['width'] = $valueWithDefKey['glass_width'];
                        $designedImageNameParams['height'] = $valueWithDefKey['glass_height'];
                    }
                    if (isset($valueWithDefKey['treatment'])) {
                        $treatmentData = $this->getTreatmentData ($valueWithDefKey['treatment'], self::$rendererFrameCustomImageType);
                        if (isset($treatmentData) && isset($treatmentData['lengthImage'])) {
                            $artworkData['treatment'] = [
                                'url' => $treatmentData['lengthImage'],
                                "width" => isset($treatmentData['width']) ? (float)$treatmentData['width'] : 0.2,
                            ];
                        }
                    }
                    if (isset($valueWithDefKey['frame_default_sku']) && (isset($valueWithDefKey['frame_width']) && $valueWithDefKey['frame_width'] > 0)) {
                        $images = $this->getProductImagesBySku($valueWithDefKey['frame_default_sku'], self::$rendererFrameCustomImageType);
                        if (isset($images) && isset($images['lengthImage']) && isset($images['cornerImage'])) {
                            $artworkData['frame'] = [
                                'sideImage' => $images['lengthImage'],
                                'cornerImage' => $images['cornerImage'],
                                "width" => isset($valueWithDefKey['frame_width']) ? (float)$valueWithDefKey['frame_width'] : 1
                            ];

                            $designedImageNameParams['frame'] = $valueWithDefKey['frame_default_sku'];
                        }
                    }
                    if (isset($valueWithDefKey['liner_sku']) && (isset($valueWithDefKey['liner_width']) && $valueWithDefKey['liner_width'] > 0)) {
                        $images = $this->getProductImagesBySku($valueWithDefKey['liner_sku'], self::$rendererFrameCustomImageType);
                        if (isset($images) && isset($images['lengthImage']) && isset($images['cornerImage'])) {
                            $artworkData['liner'] = [
                                "cornerImage" => $images['cornerImage'],
                                "sideImage" => $images['lengthImage'],
                                "width" => isset($valueWithDefKey['liner_width']) ? (float)$valueWithDefKey['liner_width'] : 1
                            ];

                            $designedImageNameParams['liner'] = $valueWithDefKey['liner_sku'];
                        }
                    }
                    if (isset($valueWithDefKey['top_mat_default_sku']) && (isset($valueWithDefKey['top_mat_size_left']) && $valueWithDefKey['top_mat_size_left'] > 0)) {
                        $images = $this->getProductImagesBySku($valueWithDefKey['top_mat_default_sku'], self::$rendererMatCustomImageType);
                        if (isset($images) && isset($images['rendererImage'])) {
                            $artworkData['topMat'] = [
                                "sideImage" => $images['rendererImage'],
                                "width" => [
                                    "left" => isset($valueWithDefKey['top_mat_size_left']) ? (float)$valueWithDefKey['top_mat_size_left'] : 1,
                                    "right" => isset($valueWithDefKey['top_mat_size_right']) ? (float)$valueWithDefKey['top_mat_size_right'] : 1,
                                    "top" => isset($valueWithDefKey['top_mat_size_top']) ? (float)$valueWithDefKey['top_mat_size_top'] : 1,
                                    "bottom" => isset($valueWithDefKey['top_mat_size_bottom']) ? (float)$valueWithDefKey['top_mat_size_bottom'] : 1
                                ]
                            ];

                            $designedImageNameParams['topMat'] = $valueWithDefKey['top_mat_default_sku'];
                            $designedImageNameParams['topMatLeft'] = $artworkData['topMat']['width']['left'];
                            $designedImageNameParams['topMatRight'] = $artworkData['topMat']['width']['right'];
                            $designedImageNameParams['topMatTop'] = $artworkData['topMat']['width']['top'];
                            $designedImageNameParams['topMatBottom'] = $artworkData['topMat']['width']['bottom'];
                        }
                    }
                    if (isset($valueWithDefKey['bottom_mat_default_sku'])  && (isset($valueWithDefKey['bottom_mat_size_left']) && $valueWithDefKey['bottom_mat_size_left'] > 0)) {
                        $bottomMatLeft = isset($valueWithDefKey['bottom_mat_size_left']) ? (float)$valueWithDefKey['bottom_mat_size_left'] : 0;
                        $bottomMatRight = isset($valueWithDefKey['bottom_mat_size_right']) ? (float)$valueWithDefKey['bottom_mat_size_right'] : 0;
                        $bottomMatTop = isset($valueWithDefKey['bottom_mat_size_top']) ? (float)$valueWithDefKey['bottom_mat_size_top'] : 0;
                        $bottomMatBottom = isset($valueWithDefKey['bottom_mat_size_bottom']) ? (float)$valueWithDefKey['bottom_mat_size_bottom'] : 0;


                        $designedImageNameParams['bottomMat'] = $valueWithDefKey['bottom_mat_default_sku'];
                        $designedImageNameParams['bottomMatLeft'] = $bottomMatLeft;
                        $designedImageNameParams['bottomMatRight'] = $bottomMatRight;
                        $designedImageNameParams['bottomMatTop'] = $bottomMatTop;
                        $designedImageNameParams['bottomMatBottom'] = $bottomMatBottom;

                        if (isset($artworkData['topMat'])) {
                            $topMatWidth = $artworkData['topMat']['width'];
                            if ($topMatWidth) {
                                $bottomMatLeft -= $topMatWidth['left'];
                                $bottomMatRight -= $topMatWidth['right'];
                                $bottomMatTop -= $topMatWidth['top'];
                                $bottomMatBottom -= $topMatWidth['bottom'];
                            }
                        }
                        if ($bottomMatLeft > 0 && $bottomMatTop > 0) {
                            $images = $this->getProductImagesBySku($valueWithDefKey['bottom_mat_default_sku'], self::$rendererMatCustomImageType);
                            if (isset($images) && isset($images['rendererImage'])) {
                                $artworkData['bottomMat'] = [
                                    "sideImage" => $images['rendererImage'],
                                    "width" => [
                                        "left" => $bottomMatLeft,
                                        "right" => $bottomMatRight,
                                        "top" => $bottomMatTop,
                                        "bottom" => $bottomMatBottom
                                    ]
                                ];
                            }
                        }
                    }
                }
            }
        }

        if ($designedImageNameParams && count($designedImageNameParams) > 0) {

            foreach ($paramSkus as $productIdKey => $productImageValue) {

                $designedImageNameParams['productId'] = $productIdKey;
                $productArtworkInfo = $this->productArtworkName($designedImageNameParams);

                $existingDesignedImageNames[$productIdKey] =  $productArtworkInfo['name'];

                if ($productArtworkInfo['rowCount'] == 1) {
                    $relatedProductIds[$productIdKey] = $productArtworkInfo['imgURL'];
                    unset($paramSkus[$productIdKey]);
                }
            }
        }

        if (is_array($paramSkus) && count($paramSkus) > 0) {
            $fields = [];
            $fields['data'] = [];

            if (is_array($artworkData)) {
                foreach ($paramSkus as $productIdKey => $productImageValue) {
                    if (isset($artworkData['image']['dimension'])) {
                        $fields['data'][$productIdKey]['image'] = [
                            "url" => $productImageValue,
                            "dimension" => $artworkData['image']['dimension']
                        ];
                    }
                    if (isset($artworkData['watermark'])) {
                        $fields['data'][$productIdKey]['watermark'] = $artworkData['watermark'];
                    }
                    if (isset($artworkData['treatment'])) {
                        $fields['data'][$productIdKey]['treatment'] = $artworkData['treatment'];
                    }
                    if (isset($artworkData['frame'])) {
                        $fields['data'][$productIdKey]['frame'] = $artworkData['frame'];
                    }
                    if (isset($artworkData['topMat'] )) {
                        $fields['data'][$productIdKey]['topMat'] = $artworkData['topMat'];
                    }
                    if (isset($artworkData['bottomMat'])) {
                        $fields['data'][$productIdKey]['bottomMat'] = $artworkData['bottomMat'];
                    }
                    if (isset($artworkData['liner'])) {
                        $fields['data'][$productIdKey]['liner'] = $artworkData['liner'];
                    }
                    $savedFileName = time();
                    if ((isset($existingDesignedImageNames[$productIdKey])) ) {
                        $savedFileName = $existingDesignedImageNames[$productIdKey];
                    }
                    $fields['data'][$productIdKey]["savedFileName"] = $savedFileName;
                }
            }

            $mediaFolderPath = $this->directoryList->getPath('media');

            $fields["url"] = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . "/productimize/savedcanvas/";
            $fields["path"] = $mediaFolderPath . "/productimize/savedcanvas/";
            $fields["format"] = "jpeg";

            $curlUrl = $this->getGeneralConfigByCode('productimize/general/productimize_generate_image_nodejs_url') . 'artworks/?time_' . time();

            $curlResponse = $this->executeCurl($curlUrl, $fields);
            if ($curlResponse["status"] == 1) {
                $mergedProdIds = [];

                try {
                    if ($curlResponse['response'] && $existingDesignedImageNames) {
                        $artProd = json_decode($curlResponse['response'], 1);
                        if ($artProd) {
                            foreach ($paramSkus as $pId => $pImgPath) {
                                //$mergedProdIds = array_merge($relatedProductIds, $artProd);
                                $relatedProductIds[$pId] = $artProd[$pId];
                                if (array_key_exists($pId, $artProd) && array_key_exists($pId, $existingDesignedImageNames) ) {
                                    $this->setproductArtworkNameDB(array("name" => $existingDesignedImageNames[$pId]));
                                }
                            }
                        }
                    }
                }
                catch(\Exception $e) {
                }

                $output = (count($mergedProdIds) > 0) ? $mergedProdIds : $relatedProductIds;

                return $this->getSerializeData($output);
                //return $curlResponse['response'];
            }
            else {

                return $this->getSerializeData($relatedProductIds);
            }
        }
        else {
            return $this->getSerializeData($relatedProductIds);
        }

    }

    /**
     * Get all Default configuration label to frontend
     */
    public function getAllDefaultConfigLabelson($defaultConf)
    {
        $jsonStr = "";
        // Get All default configuration label
        $defaultConfigurationAttributes = self::$defaultConfLabel;
        $rawDataArray = $this->json->unserialize($defaultConf);
        $attributeLabelValuePairArray = [];
        foreach ($defaultConfigurationAttributes as $key => $defaultConfigurationAttribute) {
            if (array_key_exists($key, $rawDataArray)) {
                $attributeValue = explode(':', $rawDataArray[$key]);
                if (!isset($attributeValue[1])) {
                    $attributeLabelValuePairArray[$key] = trim($defaultConfigurationAttribute);
                } else {
                    $attributeLabelValuePairArray[$key] = trim($attributeValue[1]);
                }
            }
            else {
                $attributeLabelValuePairArray[$key] = trim($defaultConfigurationAttribute);
            }
        }
        if (count($attributeLabelValuePairArray) > 0) {
            $jsonStr = $this->getSerializeData($attributeLabelValuePairArray);
        }
        //print_r($jsonStr);
        return $jsonStr;
    }

    public function generateImgInNode($productData, $artworkData, $saveImage = 0) {

        $response = $this->generateParamsForImageGeneration($productData, $artworkData, $saveImage);
        $curlUrl = $this->getGeneralConfigByCode('productimize/general/productimize_generate_image_nodejs_url');

        if ($saveImage == 1) {
            $curlUrl = $curlUrl . 'artworks/';
        }
        if ($curlUrl) {
            $curlUrl = $curlUrl . '?time_' . time();
        }
        if ($response["status"] == 1) {
            $curlResponse = $this->executeCurl($curlUrl, $response["response"]);
            return $curlResponse;
        }
        else {
            return $response;
        }
    }

    public function generateParamsForImageGeneration($productData, $artworkData, $saveImage = 0)
    {
        $fields = [];
        $fields['data'] = [];

        if ($saveImage == 1) {
            if (is_array($productData) && count($productData) > 0) {

                $topMatData = isset($artworkData['topMat']) ? $artworkData['topMat'] : "";
                $bottomMatData = isset($artworkData['bottomMat']) ? $artworkData['bottomMat'] : "";

                foreach ($productData as $productIdKey => $productImageValue) {

                    if (isset($artworkData) && isset($artworkData['imgWidth']) && $artworkData['imgWidth'] > 0) {
                        $fields['data'][$productIdKey]['image'] = [
                            "url" => $productImageValue,
                            "dimension" => ["x" => (int)$artworkData['imgWidth'], "y" => (int)$artworkData['imgHeight']]
                        ];
                        // Add Watermark image
                        $artworkWatermarkData = $this->getWatermarkImageDataByType('image');
                        if ($artworkWatermarkData && isset($artworkWatermarkData['url'])) {
                            $fields['data'][$productIdKey]['watermark'] = [
                                "url" => $artworkWatermarkData['url'],
                                "position" => isset($artworkWatermarkData['position']) ? $artworkWatermarkData['position'] : 'center',
                                "opacity" => isset($artworkWatermarkData['opacity']) ? $artworkWatermarkData['opacity'] : 100,
                                "dimension" => [
                                    "x" => isset($artworkWatermarkData['width']) ? ( (float)$artworkWatermarkData['width']) : 100,
                                    "y" => isset($artworkWatermarkData['height']) ? ((float) $artworkWatermarkData['height'] ): 100

                                ]
                            ];
                        }
                    }
                    if (isset($artworkData['treatment']) && (isset($artworkData['treatment']['width']) && $artworkData['treatment']['width'] > 0)) {
                        $fields['data'][$productIdKey]['treatment'] = [
                            "url" => $artworkData['treatment']['sideImg'],
                            "width" => (float)$artworkData['treatment']['width']
                        ];
                    }
                    if (isset($artworkData['frame']) && (isset($artworkData['frame']['width']) && $artworkData['frame']['width'] > 0)) {
                        $fields['data'][$productIdKey]['frame'] = [
                            "cornerImage" => $artworkData['frame']['cornerImg'],
                            "sideImage" => $artworkData['frame']['sideImg'],
                            "width" => (float)$artworkData['frame']['width']
                        ];
                    }
                    if (isset($topMatData) && (isset($topMatData['width']) && $topMatData['width']['left'] > 0)) {
                        $fields['data'][$productIdKey]['topMat'] = [
                            "sideImage" => $topMatData['sideImg'],
                            "width" => [
                                "left" => $topMatData['width']['left'],
                                "right" => $topMatData['width']['right'],
                                "top" => $topMatData['width']['top'],
                                "bottom" => $topMatData['width']['bottom']]
                        ];
                    }
                    if (isset($bottomMatData) && (isset($bottomMatData['width']) && $bottomMatData['width']['left'] > 0)) {
                        $fields['data'][$productIdKey]['bottomMat'] = [
                            "sideImage" => $bottomMatData['sideImg'],
                            "width" => [
                                "left" => $bottomMatData['width']['left'],
                                "right" => $bottomMatData['width']['right'],
                                "top" => $bottomMatData['width']['top'],
                                "bottom" => $bottomMatData['width']['bottom']]
                        ];
                    }
                    if (isset($artworkData) && isset($artworkData['liner'])  && (isset($artworkData['liner']['width']) && $artworkData['liner']['width'] > 0)) {
                        $fields['data'][$productIdKey]['liner'] = [
                            "cornerImage" => $artworkData['liner']['cornerImg'],
                            "sideImage" => $artworkData['liner']['sideImg'],
                            "width" => (float)$artworkData['liner']['width']
                        ];
                    }
                    $savedFileName = time();
                    if ((isset($artworkData['savedFileName'])) ) {
                        $savedFileName = $artworkData['savedFileName'];
                    }
                    $fields['data'][$productIdKey]["savedFileName"] = $savedFileName;
                }

                $mediaFolderPath = $this->directoryList->getPath('media');

                $fields["url"] = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'productimize/savedcanvas/';
                $fields["path"] = $mediaFolderPath . "/productimize/savedcanvas/";
                $fields["format"] = "jpeg";
                // $fields["savedFileName"] = (isset($artworkData['savedFileName']))  ? $artworkData['savedFileName'] : time();

                return array("response" => $fields, "status" => 1);



            } else {
                return array("response" => 'Error! Given param is not an array', "status" => 0);
            }
        } else {
            $fields['data'] = $artworkData;
            return array("response" => $fields, "status" => 1);

        }
    }

    public function executeCurl($url, $fields)
    {
        $this->curl->setOption(CURLOPT_POSTFIELDS, json_encode($fields));
        $this->curl->addHeader("Content-Type", "application/json");
        $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->curl->setOption(CURLOPT_CONNECTTIMEOUT, 5);
        $this->curl->setOption(CURLOPT_TIMEOUT, 120);
        $this->curl->setOption(CURLOPT_SSL_VERIFYPEER, 0);
        $this->curl->post($url, json_encode($fields));

        if ($this->curl->getStatus() <= 200) {
            $response = $this->curl->getBody();
            return array('status' => 1, 'response' => $response);
        } else {
            return array('status' => 0, 'response' => 'Error! ' . $this->curl->getBody());
        }
    }
    public function getConfiguredSellingPrice($productId, $productJson) {
        $configuredSellingPrice = 0.00;
        if (isset($productId) && isset($productJson)) {
            $configuredSellingPrice = $this->perficientPriceModel->getConfiguredSellingPrice($productId, $productJson);
        }
        return $configuredSellingPrice;
    }
    public function getCheckoutPrice($itemId, $productJson) {
        $checkoutPrice = 0.00;
        if (isset($productId) && isset($productJson)) {
            $checkoutPrice = $this->perficientPriceModel->getCheckoutPrice($itemId, $productJson);
        }
        return $checkoutPrice;
    }
    public function getPriceParam($itemPzCartProperties, $productId, $defaultConf = "") {

        if ($itemPzCartProperties) {
            $productJsonLabel = $this->getUnserializeData($itemPzCartProperties);
        }
        if (empty($defaultConf) && !empty($productId)) {
            $currProduct = $this->getProductById($productId);
            $defaultConf = $currProduct->getDefaultConfigurations();
        }
        if ($defaultConf) {
            $productimizeDefaultConLabels = $this->getUnserializeData($this->getAllDefaultConfigLabelson($defaultConf)); //self::$defaultConfLabel;
        }
        else {
            $productimizeDefaultConLabels = self::$defaultConfLabel;
        }
        $changedPriceParams = array(
            'medium' => (isset($productJsonLabel[$productimizeDefaultConLabels['medium']]) ? $productJsonLabel[$productimizeDefaultConLabels['medium']]  : ''),
            'treatment' => (isset($productJsonLabel[$productimizeDefaultConLabels['treatment']]) ? $productJsonLabel[$productimizeDefaultConLabels['treatment']]  : ''),
            'frame_default_sku' => (isset($productJsonLabel[$productimizeDefaultConLabels['frame_default_sku']]) ? $productJsonLabel[$productimizeDefaultConLabels['frame_default_sku']]  : ''),
            'liner_sku' => (isset($productJsonLabel[$productimizeDefaultConLabels['liner_sku']]) ? $productJsonLabel[$productimizeDefaultConLabels['liner_sku']]  : ''),
            'top_mat_default_sku' => (isset($productJsonLabel[$productimizeDefaultConLabels['top_mat_default_sku']]) ? $productJsonLabel[$productimizeDefaultConLabels['top_mat_default_sku']]  : ''),
            'bottom_mat_default_sku' => (isset($productJsonLabel[$productimizeDefaultConLabels['bottom_mat_default_sku']]) ? $productJsonLabel[$productimizeDefaultConLabels['bottom_mat_default_sku']]  : ''),
            'glass_width' => (isset($productJsonLabel[$productimizeDefaultConLabels['glass_width']]) ? $productJsonLabel[$productimizeDefaultConLabels['glass_width']]  : ''),
            'glass_height' => (isset($productJsonLabel[$productimizeDefaultConLabels['glass_height']]) ? $productJsonLabel[$productimizeDefaultConLabels['glass_height']]  : ''),
            'item_width' => (isset($productJsonLabel[$productimizeDefaultConLabels['item_width']]) ? $productJsonLabel[$productimizeDefaultConLabels['item_width']] : ''),
            'item_height' => (isset($productJsonLabel[$productimizeDefaultConLabels['item_height']]) ? $productJsonLabel[$productimizeDefaultConLabels['item_height']]  : ''),
            'image_width' => (isset($productJsonLabel[$productimizeDefaultConLabels['image_width']]) ? $productJsonLabel[$productimizeDefaultConLabels['image_width']] : ''),
            'image_height' => (isset($productJsonLabel[$productimizeDefaultConLabels['image_height']]) ? $productJsonLabel[$productimizeDefaultConLabels['image_height']]  : ''),
            'art_work_color' => (isset($productJsonLabel[$productimizeDefaultConLabels['art_work_color']]) ? $productJsonLabel[$productimizeDefaultConLabels['art_work_color']]  : ''),
            "product_id" => $productId,
            "product" => $productId
        );
        return $this->getSerializeData($changedPriceParams);
    }
    public function generateShareEditUrl($productData, $defaultConf) {

        if (isset($defaultConf)) {
            $productimizeDefaultConLabels = $this->getUnserializeData($this->getAllDefaultConfigLabelson($defaultConf)); //self::$defaultConfLabel;
        } else {
            $productimizeDefaultConLabels = self::$defaultConfLabel;
        }
        if (isset($productData[$productimizeDefaultConLabels['glass_width']]) && isset($productData[$productimizeDefaultConLabels['glass_height']])) {
            $size = $productData[$productimizeDefaultConLabels['glass_width']] . '×' . $productData[$productimizeDefaultConLabels['glass_height']];
        }
        $editUrl = 'type=configure';
        if (isset($productData[$productimizeDefaultConLabels['medium']])) {
            $editUrl .= '--medium=' . $productData[$productimizeDefaultConLabels['medium']];
        }
        if (isset($productData[$productimizeDefaultConLabels['treatment']])) {
            $editUrl .= '--treatment=' . $productData[$productimizeDefaultConLabels['treatment']];
        }
        if (isset($productData[$productimizeDefaultConLabels['frame_default_sku']])) {
            $editUrl .= '--frame=' . $productData[$productimizeDefaultConLabels['frame_default_sku']];
        }
        if (isset($productData[$productimizeDefaultConLabels['liner_sku']]) && !empty($productData[$productimizeDefaultConLabels['liner_sku']])) {
            $editUrl .= '--liner=' . $productData[$productimizeDefaultConLabels['liner_sku']];
        }
        if (isset($productData[$productimizeDefaultConLabels['top_mat_default_sku']])) {
            $editUrl .= '--topmat=' . $productData[$productimizeDefaultConLabels['top_mat_default_sku']];
        }
        if (isset($productData[$productimizeDefaultConLabels['bottom_mat_default_sku']])) {
            $editUrl .= '--bottommat=' . $productData[$productimizeDefaultConLabels['bottom_mat_default_sku']];
        }
        if (isset($productData[$productimizeDefaultConLabels['art_work_color']])) {
            $editUrl .= '--artworkcolor=' . $productData[$productimizeDefaultConLabels['art_work_color']];
        }
        if (isset($productData[$productimizeDefaultConLabels['side_mark']])) {
            $editUrl .= '--sidemark=' . $productData[$productimizeDefaultConLabels['side_mark']];
        }
        $editUrl .= '--size=' . $size;
        return $editUrl;
    }
    public function getProductById($productId)
    {
        try {
            return $product = $this->productFactory->create()->load($productId);
        } catch (\Exception $e) {
            return null;
        }
    }
    public function getProductBySku($productSku)
    {
        try {
            /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
            return $product = $this->productFactory->create()->loadByAttribute('sku', $productSku);
        } catch (\Exception $e) {
            return null;
        }
    }
    public function getProductImages($sku,$productImgType)  {
        $finalProduct = array();
        if ($sku) {
            $productSku = $this->getProductBySku($sku);
            if ($productSku && $productSku->getId()) {
                $product = $this->getProductById($productSku->getId());
                $productimages = $product->getMediaGalleryImages();
                if ($productimages) {
                    foreach ($productImgType as $key => $value) {
                        $cornerImg = $productimages->getItemByColumnValue('label', $key);
                        if ($cornerImg) {
                            $finalProduct[$value] = $cornerImg->getUrl();
                        }
                    }
                }
            }
        }
        return $finalProduct;
    }
    public function getProductImagesById($id, $productImgType) {

        $product = $this->getProductById($id);
        $images = array();
        if ($product) {
            $images = $this->getProductImagesByProduct($product,$productImgType);
        }
        return $images;
    }
    public function getProductImagesBySku($sku, $productImgType) {

        $product = $this->getProductBySku($sku);
        $images = array();
        if ($product) {
            $images = $this->getProductImagesByProduct($product,$productImgType);
        }
        return $images;
    }
    public function getProductImagesByProduct($product,$productImgType) {
        $finalProduct = array();
        if ($productImgType) {
            foreach ($productImgType as $key => $value) {
                if (strtolower($key) == 'corner') {
                    $cornerImg = $product->getRendererCorner();
                }
                else if (strtolower($key) == 'length') {
                    $cornerImg = $product->getRendererLength();
                }
                else if (strtolower($key) == 'renderer') {
                    $cornerImg = $product->getThumbnail();
                    if ( empty($cornerImg) || ($cornerImg && $cornerImg !="no_selection")) {
                        $cornerImg = $product->getImage();
                    }
                }
                else if (strtolower($key) == 'base') {
                    $cornerImg = $product->getImage();
                }
                if ($cornerImg && $cornerImg != "no_selection") {
                    $finalProduct[$value] = $this->getMediaCatalogProductPath() .$cornerImg;
                }
            }
        }
        return $finalProduct;
    }
    public function getMediaCatalogProductPath () {
        $store = $this->storeManager->getStore();
        $catalogProductPath = $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product';
        return $catalogProductPath;
    }
    public function getTreatmentData ($selectedTreatmentOption, $productImageType) {
        if ($selectedTreatmentOption) {
            $connection = $this->resourceConnection->getConnection();

            $mediaTreatmentQuery = $connection->select()
                ->from(
                    ['t' => 'treatment']
                )
                ->where('t.treatment_sku =?', $selectedTreatmentOption);

            $mediaTreatItemRow = $connection->fetchRow($mediaTreatmentQuery);

            if (!isset($mediaTreatItemRow)) {
                return false;
            }
            if ($mediaTreatItemRow && isset($mediaTreatItemRow['image_edge_treatment']) && !empty($mediaTreatItemRow['image_edge_treatment'] && strtolower($mediaTreatItemRow['image_edge_treatment']) != 'none') && count($productImageType) > 0) {
                $productSku = $this->getProductBySku($mediaTreatItemRow['image_edge_treatment']);
                if ($productSku && $productSku->getId()) {
                    $product = $productSku;

                    if ($product) {
                        $edgeTreatmentImage = $this->getProductImagesByProduct($product, self::$rendererFrameCustomImageType);
                        if ($edgeTreatmentImage) {
                            $mediaTreatItemRow['lengthImage'] = $edgeTreatmentImage['lengthImage'];
                            if (isset($product['frame_width'])) {
                                $mediaTreatItemRow['width'] = $product['frame_width'];
                            }
                        }
                    }
                }
                return $mediaTreatItemRow;
            }
            return false;
        }
        return false;
    }
    public function getWatermarkImageDataByType($type) {
        /*
         * $type = 'image', 'small' and 'thumbnail'
         */
        if (empty($type)) {
            $type = 'small_image';
        }
        $image = array();;
        $url = $this->getGeneralConfigByCode('design/watermark/'. $type . '_image');
        if (isset($url) && !empty($url)) {
            $image['url'] = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'catalog/product' . '/watermark/'. $url;
            $image['position'] = $this->getGeneralConfigByCode('design/watermark/' . $type . '_position');

            $size = $this->getGeneralConfigByCode('design/watermark/' . $type . '_size');
            list($width, $height) = explode('x', $size);
            $image['width'] = (float) $width * 2;
            $image['height'] = (float) $height * 2;

            $image['opacity'] = $this->getGeneralConfigByCode('design/watermark/' . $type . '_imageOpacity');
        }
        return $image;
    }

    public function productArtworkName($artworkDetails) {
        $productName = $artworkDetails['productId'];
        if (isset($artworkDetails['medium'])) {
            $productName .= '_M' . $artworkDetails['medium'];
        }
        if (isset($artworkDetails['treatment'])) {
            $productName .= '_T' . $artworkDetails['treatment'];
        }
        if (isset($artworkDetails['width'])) {
            $productName .= '_S' . $artworkDetails['width'] . 'x' . $artworkDetails['height'];
        }
        if (isset($artworkDetails['frame'])) {
            $productName .= '_F' . $artworkDetails['frame'];
        }
        if (isset($artworkDetails['topMat'])) {
            $productName .= '_TM' . $artworkDetails['topMat'];
            $productName .= $artworkDetails['topMatLeft'] ? 'l' . $artworkDetails['topMatLeft'] : '';
            $productName .= $artworkDetails['topMatTop'] ? 't' . $artworkDetails['topMatTop'] : '';
            $productName .= $artworkDetails['topMatRight'] ? 'r' . $artworkDetails['topMatRight'] : '';
            $productName .= $artworkDetails['topMatBottom'] ? 'b' . $artworkDetails['topMatBottom'] : '';
        }
        if (isset($artworkDetails['bottomMat'])) {
            $productName .= '_BM' . $artworkDetails['bottomMat'];
            $productName .= $artworkDetails['bottomMatLeft'] ? 'l' . $artworkDetails['bottomMatLeft'] : '';
            $productName .= $artworkDetails['bottomMatTop'] ? 't' . $artworkDetails['bottomMatTop'] : '';
            $productName .= $artworkDetails['bottomMatRight'] ? 'r' . $artworkDetails['bottomMatRight'] : '';
            $productName .= $artworkDetails['bottomMatBottom'] ? 'b' . $artworkDetails['bottomMatBottom'] : '';
        }
        if (isset($artworkDetails['liner'])) {
            $productName .= '_L' . $artworkDetails['liner'];
        }

        $connection = $this->resourceConnection->getConnection();
        $query = "SELECT * FROM custom_image_detail where custom_image_name = '" . $productName . "'";
        $rowCount = $connection->query($query)->rowCount();
        $nameDetail = [];
        $nameDetail['name'] = $productName;
        $nameDetail['rowCount'] = $rowCount;
        $nameDetail['imgURL'] = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . "/productimize/savedcanvas/" . $productName . '.jpeg';
        return $nameDetail;
    }

    public function setproductArtworkNameDB($artworkNameInfo)    {
        $connection  = $this->resourceConnection->getConnection();
        $tableName = $connection->getTableName('custom_image_detail');
        $data = [
            'custom_image_name' => $artworkNameInfo['name']
        ];
        $connection->insert('custom_image_detail', $data);
    }
}
