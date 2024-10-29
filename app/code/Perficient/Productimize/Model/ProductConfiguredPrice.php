<?php
/**
 * Product Configured Selling Price calculation based on product default configuration or customized data.
 *
 * @category: Magento
 * @package: Perficient/Productimize
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani <Manish.Bhojwani@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Productimize
 */
declare(strict_types=1);

namespace Perficient\Productimize\Model;

use Perficient\Productimize\Helper\Data as ProductimizeHelper;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Perficient\Rabbitmq\Model\BaseCostFactory;
use Perficient\Rabbitmq\Model\MediaFactory;
use Perficient\Rabbitmq\Model\TreatmentFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Serialize\Serializer\Json;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Wendover\FreightEstimator\Logger\Logger as ProductLogger;

/**
 * Class ProductConfiguredPrice
 * @package Perficient\Productimize\Model
 */
class ProductConfiguredPrice
{
    /**
     * Constants used for calculation.
     */
    const NUM_ONE = 1;
    const NUM_ONEDOTONE = 1.1;
    const NUM_TWO = 2;
    const NUM_SIX = 6;
    const NUM_TWELVE = 12;
    const NUM_FOURTYEIGHT = 48;
    const NUM_HUNDRED = 100;
    const CONST_MARKUP = 'markup';
    const CONST_PERCENT = 'percent';
    const CONST_FLAT = 'flat';
    const CONST_DISCOUNT = 'discount';

    const LOG_FILE_PATH = '/var/log/configuredPrice.log';
    const LOG_DETAILED_FILE_PATH = '/var/log/configuredPriceDetailed.log';

    /**
     * @var $artProduct
     */
    private $artProduct;

    /**
     * @var ProductimizeHelper
     */
    private ProductimizeHelper $productimizeHelper;

    /**
     * @var ProductRepositoryInterface
     */
    private \Magento\Catalog\Api\ProductRepositoryInterface $productRepository;

    /**
     * @var BaseCostFactory
     */
    private \Perficient\Rabbitmq\Model\BaseCostFactory $baseCostFactory;

    /**
     * @var MediaFactory
     */

    private \Perficient\Rabbitmq\Model\MediaFactory $mediaFactory;

    /**
     * @var TreatmentFactory
     */
    private \Perficient\Rabbitmq\Model\TreatmentFactory $treatmentFactory;

    /**
     * @var ConfigOptions
     */
    private \Perficient\Productimize\Model\ConfigOptions $configOptions;

    /**
     * @var CustomerSession
     */
    private CustomerSession $customerSession;

    /**
     * @var Json
     */
    private \Magento\Framework\Serialize\Serializer\Json $json;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var object
     */
    private \Magento\Framework\DB\Adapter\AdapterInterface $connection;

    /**
     * @var PriceCurrencyInterface
     */
    private \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ProductConfiguredPrice constructor.
     * @param ProductimizeHelper $productimizeHelper
     * @param ProductRepositoryInterface $productRepository
     * @param BaseCostFactory $baseCostFactory
     * @param MediaFactory $mediaFactory
     * @param TreatmentFactory $treatmentFactory
     * @param ConfigOptions $configOptions
     * @param CustomerSession $customerSession
     * @param Json $json
     * @param ResourceConnection $resource
     * @param PriceCurrencyInterface $priceCurrency
     * @param LoggerInterface $logger
     */
    public function __construct(
        ProductimizeHelper $productimizeHelper,
        ProductRepositoryInterface $productRepository,
        BaseCostFactory $baseCostFactory,
        MediaFactory $mediaFactory,
        TreatmentFactory $treatmentFactory,
        ConfigOptions $configOptions,
        CustomerSession $customerSession,
        Json $json,
        ResourceConnection $resource,
        PriceCurrencyInterface $priceCurrency,
        LoggerInterface $logger,
        protected ProductLogger $productLogger
    ) {
        $this->productimizeHelper = $productimizeHelper;
        $this->productRepository = $productRepository;
        $this->baseCostFactory = $baseCostFactory;
        $this->mediaFactory = $mediaFactory;
        $this->treatmentFactory = $treatmentFactory;
        $this->configOptions = $configOptions;
        $this->customerSession = $customerSession;
        $this->json = $json;
        $this->connection = $resource->getConnection();
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * Get the multiple value from customer session
     */
    public function getPriceMultiplerValue()
    {
        $multiplier = $this->customerSession->getMultiplier() ?? 1;
        if ((int) $multiplier > 0) {
            return $multiplier;
        } else {
            return 1;
        }
    }

    /**
     * Get Display Price
     * @param int $artProductId
     * @return float
     */
    public function getDisplayPrice($artProductId) :float
    {
        // Load art product
        $artProduct = $this->loadProductById($artProductId);
        $checkoutPrice = (float) $this->applyCompanyDiscount($artProduct->getPrice());
        $multiplier = $this->getPriceMultiplerValue();
        $displayPrice = $checkoutPrice * $multiplier;

        /*if($this->productimizeHelper->isProductimizePricingLoggerEnabled()) {
            $this->logConfiguredPriceMessage("Display Price", $displayPrice);
        }*/
        if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
            $this->logDetailedPricingMessage('Display Price = ' . $displayPrice);
        }

        return (float) $displayPrice;
    }

    /**
     * Get Configured Display Price
     * @param int $artProductId
     * @param string $productJson
     * @return float
     */
    public function getConfiguredDisplayPrice(
        $artProductId,
        $productJson = null
    ) :float
    {
        $checkoutPrice = $this->getCheckoutPrice($artProductId, $productJson);
        $multiplier = $this->getPriceMultiplerValue();
        $displayPrice = $checkoutPrice * $multiplier;

        /*if($this->productimizeHelper->isProductimizePricingLoggerEnabled()) {
            $this->logConfiguredPriceMessage("Configured Display Price", $displayPrice);
        }*/
        if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
            $this->logDetailedPricingMessage('Configured Display Price = ' . $displayPrice);
        }

        return (float) $displayPrice;
    }

    /**
     * Get Checkout Price
     * @param int $artProductId
     * @param string $productJson
     * @return float
     */
    public function getCheckoutPrice(
        $artProductId,
        $productJson = null
    ) :float
    {
        $configuredSellingPrice = $this->getConfiguredSellingPrice($artProductId, $productJson);
        $checkoutPrice = (float) $this->applyCompanyDiscount($configuredSellingPrice);

        /*if($this->productimizeHelper->isProductimizePricingLoggerEnabled()) {
            $this->logConfiguredPriceMessage("checkoutPrice", $checkoutPrice);
        }*/
        if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
            $this->logDetailedPricingMessage('CheckoutPrice = ' . $checkoutPrice);
        }

        return (float) $checkoutPrice;
    }

    /**
     * @param string $productJson
     * @return bool|string
     */
    public function convertDefaultConfigString($productJson = '')
    {
        $finalJson = '';
        if ($productJson != '') {
            $rawDataArray = $this->json->unserialize($productJson);
            foreach ($rawDataArray as $key => $defaultVal) {
                $attributeValue = explode(':', $defaultVal);
                $finalJson .= '"'.$key.'":"'.$attributeValue[0].'",';
            }
            $finalJson = substr($finalJson, 0, strlen($finalJson)-1);
            $finalJson = '{' . $finalJson .'}';
        }
        return $finalJson;
    }

    /**
     * Calculate Item configured selling price
     * @param int $artProductId
     * @param string $productJson
     * @return float
     */
    public function getConfiguredSellingPrice(
        $artProductId,
        $productJson = null
    ) :float
    {
        // Load art product
        $artProduct = $this->loadProductById($artProductId);

        // Load product default price
        $defaultSellingPrice = $artProduct->getPrice();

        if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
            $this->logDetailedPricingMessage('in getConfiguredSellingPrice');
            $this->logDetailedPricingMessage('PID = ' . $artProductId);
            $this->logDetailedPricingMessage('Recevied String = ' . $productJson);
            $this->logDetailedPricingMessage('Base Price = ' . $defaultSellingPrice);
        }

        // Check if pricing calculation feature is enabled or not.
        if (!$this->productimizeHelper->isProductimizePricingIsEnabled()) {
            return (float) $defaultSellingPrice;
        }

        $configuredEstimatedCost = $this->getConfiguredEstimatedCost($artProductId, $artProduct, $productJson);
        $defaultEstimatedCost = $this->getDefaultEstimatedCost($artProductId, $artProduct);
        //$defaultEstimatedCost = $this->getDefaultEstimatedCost($artProductId, $artProduct, $artProduct->getDefaultConfigurations());
        $customizationMarkupPct = $this->productimizeHelper->getPricingCustomizationMarkupPct();

        if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
            $this->logDetailedPricingMessage('Configured Estimated Cost = ' . $configuredEstimatedCost);
            $this->logDetailedPricingMessage('Default Estimated Cost = ' . $defaultEstimatedCost);
            $this->logDetailedPricingMessage('Customize Markup Pct = ' . $customizationMarkupPct);
        }

        if ($configuredEstimatedCost === $defaultEstimatedCost) {
            //With this if customer add to cart for PDP with default options he will get product in little less amount
            // compared to customized product with same options but wendover is fine with this. because of 1 + Customization Markup %.
            $customizationMarkupPct = 0;
        }

        /**
         * Calculate Configured Selling Price
         * Configured Selling Price = (Configured Estimated Cost / Default Estimated Cost)* Default Selling Price * (1 + Customization Markup %)
         */
        if ($defaultEstimatedCost < 1) {
            $defaultEstimatedCost = 1;
        }
        if ($configuredEstimatedCost < 1) {
            $configuredEstimatedCost = 1;
        }
        if ($configuredEstimatedCost < 1 && $defaultEstimatedCost < 1) {
            $defaultEstimatedCost = 1;
            $configuredEstimatedCost = 1;
        }
        $itemPrice = ($configuredEstimatedCost / $defaultEstimatedCost) * $defaultSellingPrice * (self::NUM_ONE + ($customizationMarkupPct / self::NUM_HUNDRED));

        /*if($this->productimizeHelper->isProductimizePricingLoggerEnabled()) {
            $this->logConfiguredPriceMessage("Configured Selling Price", $itemPrice);
        }*/
        if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
            $this->logDetailedPricingMessage('Final Configured Selling Price = ' . $itemPrice);
        }

        return (float) $itemPrice;
    }

    /**
     * Get configured estimated price
     * @param int $artProductId
     * @param $artProduct
     * @param string $productJson
     * @return float
     */
    private function getConfiguredEstimatedCost(
        $artProductId,
        $artProduct = null,
        $productJson = null
    ) :float
    {
        if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
            $this->logDetailedPricingMessage('inside getConfiguredEstimatedCost');
        }

        $configuredEstimatedCost = $this->getCustomizedEstimatedCost($artProductId, $artProduct, $productJson);

        /*if($this->productimizeHelper->isProductimizePricingLoggerEnabled()) {
            $this->logConfiguredPriceMessage("Configured Product", $artProductId);
            $this->logConfiguredPriceMessage("Configured Product Json", $productJson);
            $this->logConfiguredPriceMessage("Configured Estimated Cost", $configuredEstimatedCost);
        }*/
        if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
            $this->logDetailedPricingMessage('Configured Product = ' . $artProductId);
            $this->logDetailedPricingMessage('Configured Product Json = ' . $productJson);
            $this->logDetailedPricingMessage('Configured Estimated Cost = ' . $configuredEstimatedCost);
        }

        return (float) $configuredEstimatedCost;
    }

    /**
     * Get default estimated price
     * @param int $artProductId
     * @param $artProduct
     * @param string $productJson
     * @return float
     */
    private function getDefaultEstimatedCost(
        $artProductId,
        $artProduct = null,
        $productJson = null
    ) :float
    {
        if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
            $this->logDetailedPricingMessage('in getDefaultEstimatedCost');
        }

        $defaultEstimatedCost = $this->getCustomizedEstimatedCost($artProductId, $artProduct, $productJson);

        /*if($this->productimizeHelper->isProductimizePricingLoggerEnabled()) {
            $this->logConfiguredPriceMessage('Default Product' . $artProductId);
            $this->logConfiguredPriceMessage('Default Product Json' . $productJson);
            $this->logConfiguredPriceMessage('Default Estimated Cost' . $defaultEstimatedCost);
        }*/
        if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
            $this->logDetailedPricingMessage('Default Product = ' . $artProductId);
            $this->logDetailedPricingMessage('Default Product Json = ' . $productJson);
            $this->logDetailedPricingMessage('Default Estimated Cost = ' . $defaultEstimatedCost);
        }

        return (float) $defaultEstimatedCost;
    }

    /**
     * Calculate product customized estimated cost
     * @param int $artProductId
     * @param string $productJson
     * @return float
     */
    private function getCustomizedEstimatedCost(
        $artProductId,
        $artProduct = null,
        $productJson = null
    ) :float
    {
        if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
            $this->logDetailedPricingMessage('inside getCustomizedEstimatedCost');
            $this->logDetailedPricingMessage('param pid = ' . $artProductId);
            $this->logDetailedPricingMessage('param jsonstring = ' . $productJson);
        }

        // Check for art product or load if not exists
        if (!$artProduct) {
            $artProduct = $this->loadProductById($artProductId);
        }

        $itemPrice = 0;
        if ($artProduct) {
            // Check for product json string or load from default configuration if not exists
            if (empty($productJson)) {
                if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
                    $this->logDetailedPricingMessage('in empty json string');
                }
                $productJson = $artProduct->getDefaultConfigurations();
                $productJson = $this->convertDefaultConfigString($productJson);
            }

            // Check for product json string or calculate and create product json string if not exists
            if (empty($productJson)) {
                if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
                    $this->logDetailedPricingMessage('in second empty json string');
                }
                $productJson = $this->configOptions->getDefaultConfigurationOptions($artProductId, $productJson);
            }

            $productJson = $this->json->unserialize($productJson);

            /*if($this->productimizeHelper->isProductimizePricingLoggerEnabled()) {
                $this->logConfiguredPriceMessage('ID', $artProduct->getId());
                $this->logConfiguredPriceMessage('SKU', $artProduct->getSku());
                $this->logConfiguredPriceMessage('productJson', $productJson);
            }*/
            if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
                $this->logDetailedPricingMessage($productJson);
                $this->logDetailedPricingMessage('ID = ' . $artProduct->getId());
                $this->logDetailedPricingMessage('SKU = ' . $artProduct->getSku());
                $this->logDetailedPricingMessage('productJson = :');
                $this->logDetailedPricingMessage($productJson);
            }

            //$frameSku = isset($productJson['frame_sku']) ? $productJson['frame_sku'] : '';
            $frameSku = isset($productJson['frame_default_sku']) ? $productJson['frame_default_sku'] : '';
            $linerSku = isset($productJson['liner_sku']) ? $productJson['liner_sku'] : '';
            //$topMatSku = isset($productJson['top_mat_sku']) ? $productJson['top_mat_sku'] : '';
            $topMatSku = isset($productJson['top_mat_default_sku']) ? $productJson['top_mat_default_sku'] : '';
            //$bottomMatSku = isset($productJson['bottom_mat_sku']) ? $productJson['bottom_mat_sku'] : '';*/
            $bottomMatSku = isset($productJson['bottom_mat_default_sku']) ? $productJson['bottom_mat_default_sku'] : '';
            $medium = isset($productJson['medium']) ? $productJson['medium'] : '';
            $treatment = isset($productJson['treatment']) ? $productJson['treatment'] : '';

            $baseCost = $frameCost = $linerCost = $topMatCost = $bottomMatCost = 0;
            $frameMouldingWastePct = $linerMouldingWastePct = 0;
            $frameWidth = $linerWidth = 0;

            // Check for the frame-sku
            //Commented as if we keep this and configurator dont have frame it will add default
            /*if (!$frameSku || empty($frameSku)) {
                $frameSku = $artProduct->getFrameDefaultSku();
            }*/
            if (!empty($frameSku)) {
                $frameProduct = $this->loadProductBySku($frameSku);
                if ($frameProduct) {
                    $frameCost = (double)$frameProduct->getLandedCostPerFoot() ?? 0;
                    $frameMouldingWastePct = (double)$frameProduct->getMouldingWastePct() ?? 0;
                    $frameWidth = (double)$frameProduct->getFrameWidth() ?? 0;

                    if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
                        $this->logDetailedPricingMessage('$frameCost = ' . $frameCost);
                        $this->logDetailedPricingMessage('$frameMouldingWastePct = ' . $frameMouldingWastePct);
                        $this->logDetailedPricingMessage('$frameWidth = ' . $frameWidth);
                    }
                }
            }

            // Check for the liner-sku
            /*if (!$linerSku || empty($linerSku)) {
                $linerSku = $artProduct->getLinerSku();
            }*/
            if (!empty($linerSku)) {
                $linerProduct = $this->loadProductBySku($linerSku);
                if ($linerProduct) {
                    $linerCost = (double)$linerProduct->getLandedCostPerFoot() ?? 0;
                    $linerMouldingWastePct = (double)$linerProduct->getMouldingWastePct() ?? 0;
                    $linerWidth = (double)$linerProduct->getFrameWidth() ?? 0;

                    if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
                        $this->logDetailedPricingMessage('$linerCost = ' . $linerCost);
                        $this->logDetailedPricingMessage('$linerMouldingWastePct = ' . $linerMouldingWastePct);
                        $this->logDetailedPricingMessage('$linerWidth = ' . $linerWidth);
                    }
                }
            }

            // Check for the top-mat-sku
            /*if (!$topMatSku || empty($topMatSku)) {
                $topMatSku = $artProduct->getTopMatDefaultSku();
            }*/
            if (!empty($topMatSku)) {
                $topMatProduct = $this->loadProductBySku($topMatSku);
                if ($topMatProduct) {
                    $topMatCost = (double)$topMatProduct->getFabricCostPerLinFt() ?? 0;

                    if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
                        $this->logDetailedPricingMessage('$topMatCost = ' . $topMatCost);
                    }
                }
            }

            // Check for the bottom-mat-sku
            /*if (!$bottomMatSku || empty($bottomMatSku)) {
                $bottomMatSku = $artProduct->getBottomMatDefaultSku();
            }*/
            if (!empty($bottomMatSku)) {
                $bottomMatProduct = $this->loadProductBySku($bottomMatSku);
                if ($bottomMatProduct) {
                    $bottomMatCost = (double)$bottomMatProduct->getFabricCostPerLinFt() ?? 0;

                    if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
                        $this->logDetailedPricingMessage('$bottomMatCost = ' . $bottomMatCost);
                    }
                }
            }

            // Fetch glass width and height from product or calculate if doesn't exists
            $glassWidth = isset($productJson['glass_width']) ? (double) $productJson['glass_width'] : 0;
            $glassHeight = isset($productJson['glass_width']) ? (double) $productJson['glass_height'] : 0;

            // Calculate long and short glass side
            $longSide = (float) max($glassWidth, $glassHeight);
            $shortSide = (float) min($glassWidth, $glassHeight);

            // Check for the medium
            /*if (!$medium || empty($medium)) {
                $medium = $artProduct->getMedium();
            }*/

            // Check for the treatment
            /*if (!$treatment || empty($treatment)) {
                $treatment = $artProduct->getTreatment();
            }*/

            if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
                $this->logDetailedPricingMessage('Glass = ' . $glassWidth. ' ' . $glassHeight);
                $this->logDetailedPricingMessage('Long Short = ' . $longSide. ' ' . $shortSide);
                $this->logDetailedPricingMessage('medium = ' . $medium);
                $this->logDetailedPricingMessage('$treatment = ' . $treatment);
            }

            $baseCostArr = $this->getBaseCost($medium, $treatment, $longSide, $shortSide);
            if (isset($baseCostArr['base_cost'])) {
                $baseCost = (float) $baseCostArr['base_cost'];
            }

            if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
                $this->logDetailedPricingMessage('$baseCostArr');
                $this->logDetailedPricingMessage($baseCostArr);
                $this->logDetailedPricingMessage('$baseCost = ' . $baseCost);
            }

            /**
             * Calculate Configured Estimated Cost
             * Configured Estimated Cost = (Base Cost + (Glass Width + Glass Height) * 2/12 * (1+ Moulding Waste %) * Liner Cost + (Glass Width + Glass Height + 2*Liner Width) * 2/12 * (1+ Moulding Waste %) * Frame Cost + (6 + (IF Long Glass Side <=48 THEN Short Side ELSE Long Side)) * (Top Mat Cost + Bottom Mat Cost) )* (1.1 if user selects Custom Color option in item config)
             */
            /*$itemPrice = $baseCost + ($glassWidth + $glassHeight);

            if (!empty($linerSku) && $linerCost > 0)
                $itemPrice = $itemPrice * (self::NUM_TWO / self::NUM_TWELVE * (self::NUM_ONE + ($linerMouldingWastePct / self::NUM_HUNDRED)) * $linerCost);

            $itemPrice = $itemPrice + ($glassWidth + $glassHeight + (self::NUM_TWO * $linerWidth));

            if (!empty($frameSku) && $frameCost > 0)
                $itemPrice = $itemPrice * (self::NUM_TWO / self::NUM_TWELVE * (self::NUM_ONE + ($frameMouldingWastePct / self::NUM_HUNDRED)) * $frameCost);

            if ($longSide <= self::NUM_FOURTYEIGHT)
                $itemPrice = $itemPrice + (self::NUM_SIX + $shortSide);
            else
                $itemPrice = $itemPrice + (self::NUM_SIX + $longSide);

            if ((!empty($topMatSku) && $topMatCost > 0) || (!empty($bottomMatSku) && $bottomMatCost > 0))
                $itemPrice = $itemPrice * ($topMatCost + $bottomMatCost);

            if (isset($productJson['custom_color']) && !empty($productJson['custom_color'])) {
                $itemPrice = $itemPrice * self::NUM_ONEDOTONE;
            }*/




            $glassCalc = ($longSide <= self::NUM_FOURTYEIGHT) ? $shortSide : $longSide;
            $linerMouldingPercent = ($linerMouldingWastePct);
            $frameMouldingPercent = ($frameMouldingWastePct);
            $colorCostPercentVal = $this->productimizeHelper->getColorConstPct();
            $colorCostPercent = ($colorCostPercentVal / self::NUM_HUNDRED);

            //LINER PRICE
            $linerCalcPrize = 0;
            if (!empty($linerSku))
                $linerCalcPrize = ($glassWidth + $glassHeight) * self::NUM_TWO / self::NUM_TWELVE * (self::NUM_ONE + $linerMouldingPercent) * $linerCost;

            //FRAME PRICE
            $frameCalcPrize = 0;
            if (!empty($frameSku))
                $frameCalcPrize = ($glassWidth + $glassHeight + self::NUM_TWO * $linerWidth) * self::NUM_TWO / self::NUM_TWELVE * (self::NUM_ONE + $frameMouldingPercent) * $frameCost;

            //MAT PRICE
            $matCalcPrize = 0;
            if (!empty($topMatSku) || !empty($bottomMatSku)) {
                $matCalcPrize = (self::NUM_SIX + $glassCalc) / self::NUM_TWELVE * ($topMatCost + $bottomMatCost);
            }

            //COLOR PRICE
            $colorCalcPrize = 0;
            if (isset($productJson['art_work_color']) && !empty($productJson['art_work_color'])) {
                //$colorCalcPrize = ($baseCost + $frameCalcPrize + $linerCalcPrize + $matCalcPrize) * self::NUM_ONEDOTONE;
                $colorCalcPrize = ($baseCost + $frameCalcPrize + $linerCalcPrize + $matCalcPrize) * $colorCostPercent;
            }

            //TOTAL PRICE
            $itemPrice = $baseCost + $frameCalcPrize + $linerCalcPrize + $matCalcPrize + $colorCalcPrize;

            /*if($this->productimizeHelper->isProductimizePricingLoggerEnabled()) {
                $this->logConfiguredPriceMessage("Customized Cost", $itemPrice);
            }*/
            if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
                $this->logDetailedPricingMessage('$baseCost = ' . $baseCost);
                $this->logDetailedPricingMessage('$frameCalcPrize = ' . $frameCalcPrize);
                $this->logDetailedPricingMessage('$linerCalcPrize = ' . $linerCalcPrize);
                $this->logDetailedPricingMessage('$matCalcPrize = ' . $matCalcPrize);
                $this->logDetailedPricingMessage('$colorCalcPrize = ' . $colorCalcPrize);
                $this->logDetailedPricingMessage('$itemPrice = ' . $itemPrice);
            }
        }

        return (float) $itemPrice;
    }

    /**
     * Load product ty ID (Magento internally handle product cache in getById and get method)
     * @param int $productId
     * @return object|null
     */
    public function loadProductById($productId)
    {
        try {
            /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
            return $this->productRepository->getById($productId);
        } catch (\Exception $e) {
            $this->logger->error(__('Unable to load product #%1.', $productId));
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    /**
     * Load product ty SKU (Magento internally handle product cache in getById and get method)
     * @param string $sku
     * @return object|null
     */
    private function loadProductBySku($sku)
    {
        try {
            /** @var \Magento\Catalog\Api\Data\ProductInterface $product */
            return $this->productRepository->get($sku);
        } catch (\Exception $e) {
            $this->logger->error(__('Unable to load product #%1.', $sku));
            $this->logger->error($e->getMessage());
            return null;
        }
    }

    /**
     * Get Product Base Cost
     * SQL queries added as disucssed in tech call to improve performance
     * @param string $mediaSku
     * @param string $treatmentSku
     * @param float $longSide
     * @param float $shortSide
     * @return null|array
     */
    private function getBaseCost($mediaSku, $treatmentSku, $longSide, $shortSide)
    {
        if (empty($mediaSku) || empty($treatmentSku) || empty($longSide) || empty($shortSide)) {
            return null;
        }

        $longSide = (int)$longSide;
        $shortSide = (int)$shortSide;
        $media = $this->getMediaFromMediaSku($mediaSku);
        $treatment = $this->getTreatmentFromTreatmentSku($treatmentSku);

        if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
            $this->logDetailedPricingMessage('inside getBaseCost');
            $this->logDetailedPricingMessage($media);
            $this->logDetailedPricingMessage($treatment);
        }

        if (
            (isset($media['base_cost_media']) && !empty($media['base_cost_media'])) &&
            (isset($treatment['base_cost_treatment']) && !empty($treatment['base_cost_treatment']))
        ) {
            $select = $this->connection->select();
            $select->from(
                ['cpe' => $this->connection->getTableName('base_cost')],
                [
                    'base_cost'
                ]
            )->where('base_cost_media = ?', (string) $media['base_cost_media'])
                ->where('base_cost_treatment = ?', (string) $treatment['base_cost_treatment'])
                ->where('glass_size_short = ?', $shortSide)
                ->where('glass_size_long = ?', $longSide);

            return $result = $this->connection->fetchRow($select);
        }

        return null;
    }

    /**
     * Get media data from media sku
     * SQL queries added as disucssed in tech call to improve performance
     * @param string $mediaSku
     * @return null|array
     */
    private function getMediaFromMediaSku($mediaSku)
    {
        $select = $this->connection->select();
        $select->from(
            ['cg' => $this->connection->getTableName('media')],
            [
                'base_cost_media'
            ]
        )->where('sku = ?', $mediaSku);

        return $result = $this->connection->fetchRow($select);
    }

    /**
     * Get treatment data from treatment sku
     * SQL queries added as disucssed in tech call to improve performance
     * @param string $treatmentSku
     * @return null|array
     */
    private function getTreatmentFromTreatmentSku($treatmentSku)
    {
        $select = $this->connection->select();
        $select->from(
            ['cg' => $this->connection->getTableName('treatment')],
            [
                'base_cost_treatment'
            ]
        )->where('treatment_sku = ?', $treatmentSku);

        return $result = $this->connection->fetchRow($select);
    }


    /**
     * Apply Company Discounted Amount
     *
     * @param $price float
     * @return float
     */
    public function applyCompanyDiscount($price, $isAddToCart = false): float
    {
        $discountType = $this->customerSession->getDiscountType();
        $discountAvailable = $this->customerSession->getDiscountAvailable();
        $discountMarkup = (string) $this->customerSession->getDiscountMarkup() ?? self::CONST_MARKUP;
        $discountApplicationType = (string) $this->customerSession->getDiscountApplicationType() ?? self::CONST_PERCENT;
        $discountValue = (float) $this->customerSession->getDiscountValue() ?? 0;

        if ($isAddToCart) {
            if ($discountAvailable && $discountValue) {
                $discount = $discountApplicationType == self::CONST_FLAT ? $discountValue : $price * ($discountValue / 100);

                $price = $discountMarkup == self::CONST_DISCOUNT ? $price - $discount : $price + $discount;
            }
        } elseif ($discountAvailable && $discountType == ProductimizeHelper::DISCOUNT_TYPE_POST_DISCOUNTED && $discountValue) {
            $discount = $discountApplicationType == self::CONST_FLAT ? $discountValue : $price * ($discountValue / 100);
            $price = $discountMarkup == self::CONST_DISCOUNT ? $price - $discount : $price + $discount;
        }

        return (float) $price;
    }

    /**
     * Apply Company Discounted Amount
     *
     * @param $price float
     * @return float
     */
    public function getCompanyDiscount($price): float
    {
        $discountType = $this->customerSession->getDiscountType();
        $discountAvailable = $this->customerSession->getDiscountAvailable();
        $discountMarkup = (string) $this->customerSession->getDiscountMarkup() ?? self::CONST_MARKUP;
        $discountApplicationType = (string) $this->customerSession->getDiscountApplicationType() ?? self::CONST_PERCENT;
        $discountValue = (float) $this->customerSession->getDiscountValue() ?? 0;
        $discount = 0;

        if ($discountAvailable && $discountType == ProductimizeHelper::DISCOUNT_TYPE_POST_DISCOUNTED && $discountValue) {
            $discount = $discountApplicationType == self::CONST_FLAT ? $discountValue : $price * ($discountValue / 100);

            if ($discountMarkup == self::CONST_MARKUP) {
                $discount = - $discount;
            }
        }

        return (float) $discount;
    }

    /**
     * Get Configurator Item Price include discount + multiplier + strikeout price
     * @param float $price
     * @return array
     */
    public function getConfigratorItemPrice($price) :array
    {
        /** Get the price-multiplier of logged-in customer.*/
        $priceMultiplier = $this->getPriceMultiplerValue();
        $strikeOutPrice = '';

        $discountType = $this->customerSession->getDiscountType();
        $discountAvailable = $this->customerSession->getDiscountAvailable();
        $displayStrikeOut = $this->customerSession->getStrikeOut();

        if ($discountAvailable && $displayStrikeOut) {
            if($discountType == ProductimizeHelper::DISCOUNT_TYPE_POST_DISCOUNTED) {
                $strikeOutPrice = $price * $priceMultiplier;
                $price = $this->applyCompanyDiscount($price);
            }
        }

        $displayPrice = ($price * $priceMultiplier);

        if($displayPrice == 0) {
            $displayPrice = '';
        }

        if($displayPrice >= $strikeOutPrice) {
            $strikeOutPrice = '';
        }

        $priceData = [
            'display_price' => $displayPrice ? $this->priceCurrency->format($displayPrice): '',
            'strikeout_price' => $strikeOutPrice ? $this->priceCurrency->convertAndFormat($strikeOutPrice, false) : ''
        ];

        return $priceData;
    }

    /**
     * Logger
     *
     * @param $label
     * @param $message
     */
    public function logConfiguredPriceMessage($label, $message)
    {
        if (gettype($message) === 'array') {
            $message = json_encode($message);
        }

        $this->productLogger->info($message);
    }

    /**
     * @param $message
     * @param $message
     */
    public function logDetailedPricingMessage($message)
    {
        if ($this->productimizeHelper->isPricingDetailedLoggerEnabled()) {
            if (gettype($message) === 'array') {
                $message = json_encode($message);
            }

            $this->productLogger->info($message);
        }
    }
}
