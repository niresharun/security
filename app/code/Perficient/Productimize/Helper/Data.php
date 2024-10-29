<?php
/**
 * This file is used to get customizable product edit id from info-buy-request object.
 *
 * @category: Magento
 * @package: Perficient/Productimize
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Archana Lohakare <archana.lohakare@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Productimize
 */
declare(strict_types=1);

namespace Perficient\Productimize\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Perficient\Productimize\Helper
 */
class Data extends AbstractHelper
{
    const EDIT_ID = 'edit_id';

    /**
     * @var Json
     */
    private \Magento\Framework\Serialize\Serializer\Json $json;

    const DISCOUNT_TYPE_STANDARD = 'standard';
    const DISCOUNT_TYPE_POST_DISCOUNTED = 'post-discounted';

    const XML_PATH_PRODUCTIMIZE_PRICING_IS_ENABLED = 'productimize/pricing/is_enabled';
    const XML_PATH_PRODUCTIMIZE_PRICING_CUSTOMIZATION_MARKUP_PCT = 'productimize/pricing/customization_markup_pct';
    const XML_PATH_PRODUCTIMIZE_PRICING_COLOR_CONST_MARKUP_PCT = 'productimize/pricing/color_const_markup_pct';
    const XML_PATH_PRODUCTIMIZE_PRICING_LOGGER = 'productimize/pricing/logger';
    const XML_PATH_PRODUCTIMIZE_PRICING_DETAILED_LOGGER = 'productimize/pricing/detailed_logger';
    const XML_PATH_PRODUCTIMIZE_PRICING_DISC_LOGGER = 'productimize/pricing/disc_surcharge_logger';
    const LOG_DISC_SURCHARGE_FILE_PATH = '/var/log/discSurcharge.log';

    /**
     * Data constructor.
     * @param Context $context
     * @param Json $json
     */
    public function __construct(
        Context $context,
        Json $json
    ) {
        parent::__construct($context);
        $this->json = $json;
    }

    /**
     * Method used to get customizable product edit id from info-buy-request object
     * @param $infoBuyRequest
     * @return bool
     */
    public function getCustomizeEditId($infoBuyRequest)
    {
        $infoRequest = $this->json->unserialize($infoBuyRequest->getValue());
        if (isset($infoRequest[self::EDIT_ID])) {
            return $infoRequest[self::EDIT_ID];
        }
    }

    /**
     * get Edit url
     * @param $productUrl
     * @param $customizeEditId
     * @return mixed
     */
    public function getEditUrl($productUrl, $customizeEditId)
    {
        if ($customizeEditId) {
            return $productUrl. "?" .self::EDIT_ID. "=" .$customizeEditId;
        } else {
            return $productUrl;
        }
    }

    /**
     * Check configuration if pricing calculation is enabled or not
     * If enabled return configurator price else default magento price and apply discount on it..
     * @return bool
     */
    public function isProductimizePricingIsEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            Data::XML_PATH_PRODUCTIMIZE_PRICING_IS_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check configuration to get product pricing customization markup percentage
     * @return float
     */
    public function getPricingCustomizationMarkupPct(): float
    {
        return (float) $this->scopeConfig->getValue(
            Data::XML_PATH_PRODUCTIMIZE_PRICING_CUSTOMIZATION_MARKUP_PCT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check configuration to get color const markup percentage
     * @return float
     */
    public function getColorConstPct(): float
    {
        return (float) $this->scopeConfig->getValue(
            Data::XML_PATH_PRODUCTIMIZE_PRICING_COLOR_CONST_MARKUP_PCT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check configuration if pricing calculation logger is enabled or not
     * @return bool
     */
    public function isProductimizePricingLoggerEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            Data::XML_PATH_PRODUCTIMIZE_PRICING_LOGGER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check configuration if pricing calculation logger is enabled or not
     * @return bool
     */
    public function isPricingDetailedLoggerEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            Data::XML_PATH_PRODUCTIMIZE_PRICING_DETAILED_LOGGER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check configuration if discount surcharge calculation logger is enabled or not
     * @return bool
     */
    public function isDiscSurchargeLoggerEnabled(): bool
    {
        return (bool) $this->scopeConfig->getValue(
            Data::XML_PATH_PRODUCTIMIZE_PRICING_DISC_LOGGER,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $message
     * @param $message
     */
    public function logDiscSurchargeMessage($message)
    {
        if ($this->isDiscSurchargeLoggerEnabled()) {
            $writer = new \Zend\Log\Writer\Stream(BP . self::LOG_DISC_SURCHARGE_FILE_PATH);
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($message);
        }
    }

    /**
     * Get Edit ID Code
     * @return string
     */
	public function getEditIdCode(): string
    {
        return self::EDIT_ID;
    }
}
