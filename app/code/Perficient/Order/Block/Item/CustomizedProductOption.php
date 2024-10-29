<?php
/**
 * Modified Order Receipt Page and Email
 * @category: Magento
 * @package: Perficient/Order
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<vikramraj.sahu@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Order
 */
declare(strict_types=1);

namespace Perficient\Order\Block\Item;

/**
 * Class CustomizedProductOption
 * @package Perficient\Order\Block\Item
 */
class CustomizedProductOption {

    /**
     * @param $options
     * @return array|mixed
     * @throws \JsonException
     */
    public function getOptions($options) {

        $cartProperties = [];
        if (isset($options['info_buyRequest']['pz_cart_properties']) && !empty($options['info_buyRequest']['pz_cart_properties'])) {
            $cartProperties = json_decode((string) $options['info_buyRequest']['pz_cart_properties'],true, 512, JSON_THROW_ON_ERROR);
            if(isset($cartProperties[' CustomImage'])) {
                unset($cartProperties[' CustomImage']);
            }
        }

        return $cartProperties;
    }
}
