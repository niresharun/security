<?php
/**
 * Set Default configuration to the wishlist.
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<sandeep.mude@Perficient.com>
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Plugin\Wishlist\Helper;

use Magento\Wishlist\Helper\Data;
use Perficient\Catalog\Helper\Data as PrftCatalogHelper;

class DataPlugin
{
    /**
     * DataPlugin constructor.
     */
    public function __construct(
        private readonly PrftCatalogHelper $helper
    ) {
    }

    /**
     * @param Data $subject
     * @param $item
     * @return array
     */
    public function beforeGetAddParams(Data $subject, $item, array $params = [])
    {
        $defaultConf = $item->getData('default_configurations');
        $confData = $this->helper->getDefaultConfigurationJson($defaultConf);
        if ($confData['jsonStr']) {
            $params['pz_cart_properties'] = $confData['jsonStr'];
        }
        $buyRequest = $item->getBuyRequest();
        $productimizeEditId = $buyRequest['edit_id'] ?? null;
        if (isset($productimizeEditId)) {
            $productConfiguration = $buyRequest['pz_cart_properties'] ?? null;
            if (isset($productConfiguration)) {
                if (isset($buyRequest['configurator_price'])) {
                    $params['configurator_price'] = $buyRequest['configurator_price'];
                }
                $params['qty'] = $buyRequest['qty'] ?? 1;
                $params['edit_id'] = $productimizeEditId;
                $params['pz_cart_properties'] = $productConfiguration;
            }
        }
        return [$item, $params];
    }
}
