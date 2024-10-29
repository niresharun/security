<?php
/**
 * Disable M1 customer attributes for admin
 * @category: Magento
 * @package: Perficient/Customer
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Megha Ghadge<megha.ghadge@Perficient.com>
 * @keywords: Module Perficient_Customer
 */
declare(strict_types=1);

namespace Perficient\Customer\Plugin\Model;

use Magento\Customer\Model\AttributeMetadataResolver;

class AttributeMetadataResolverPlugin
{
    /**
     * @const string
     */
    final const ATTRIBUTE_TYPE_M1 = 'M1';

    /**
     * Earlier we used source identifier but later some parametes are marked as Both
     * but still needs to be disabled (eg: sq_ft_per_store WENDM2-2091)
     */
    protected $disableFields = [
        'mark_pos',
        'designer_type',
        'des_comm',
        'no_of_designers',
        'percent_of_design',
        'price_switch',
        'customer_activated',
        'tax_id',
        'sales_rep',
        'website',
        'annual_revenue',
        'is_customer_of',
        'uuid',
        'is_vip',
        'source_id',
        'syspro_customer_id',
        'business_info',
        'sq_ft_per_store',
        'no_of_stores'
    ];

    /**
     * After plugin to disable M1 attributes for admin
     *
     * @param AttributeMetadataResolver $subject
     * @param $result
     * @param $attribute
     * @return mixed
     */
    public function afterGetAttributesMeta(AttributeMetadataResolver $subject, $result, $attribute)
    {
        $attributeCode = $attribute->getAttributeCode();
        /*$sourceIdentifier = $attribute->getSourceIdentifier();

        if ($attributeCode && $sourceIdentifier === self::ATTRIBUTE_TYPE_M1) {
            $result['arguments']['data']['config']['disabled'] = 1;
        }*/

        foreach ($this->disableFields as $val) {
            if (trim((string)$attributeCode) == trim((string)$val)) {
                $result['arguments']['data']['config']['disabled'] = 1;
            }
        }

        return $result;
    }
}
