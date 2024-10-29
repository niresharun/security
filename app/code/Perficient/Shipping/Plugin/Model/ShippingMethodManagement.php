<?php
/**
 * File used to hide flatrate from frontend.
 *
 * @category: Magento
 * @package: Perficient/Shipping
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Manish Bhojwani <Manish.Bhojwani@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Shipping
 */
declare(strict_types=1);

namespace Perficient\Shipping\Plugin\Model;

/**
 * Class Flatrate
 * @package Perficient\Shipping\Plugin\Model
 */
class ShippingMethodManagement
{
    const CODE = 'flatrate';

    /**
     * Plugin afterEstimateByAddress
     * @param $shippingMethodManagement
     * @param $output
     *
     * @return mixed
     */
    public function afterEstimateByAddress($shippingMethodManagement, $output)
    {
        return $this->filterOutput($output);
    }

    /**
     * Plugin afterEstimateByExtendedAddress
     * @param $shippingMethodManagement
     * @param $output
     *
     * @return mixed
     */
    public function afterEstimateByExtendedAddress($shippingMethodManagement, $output)
    {
        return $this->filterOutput($output);
    }

    /**
     * Plugin afterEstimateByAddressId
     * @param $shippingMethodManagement
     * @param $output
     *
     * @return mixed
     */
    public function afterEstimateByAddressId($shippingMethodManagement, $output)
    {
        return $this->filterOutput($output);
    }

    /**
     * Hide shipping method flatrate from frontend
     * @param $output
     * @return mixed
     */
    private function filterOutput($output)
    {
        foreach ($output as $key => $shippingMethod) {
            if ($shippingMethod->getCarrierCode() == self::CODE && $shippingMethod->getMethodCode() == self::CODE) {
//                unset($output[$key]);
            }
        }

        return $output;
    }

}
