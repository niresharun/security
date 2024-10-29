<?php
/**
 * Sorted the country Array
 * @category: Magento
 * @package: Perficient/Customform
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vijayashanthi M<v.murugesan@Perficient.com>
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);
namespace Perficient\Customform\Plugin\Widget\Form\Element;
use Amasty\Customform\Block\Widget\Form\Element\Country;

class CountryPlugin
{
    public function afterGetCountryOptions(Country $subject, $result)
    {
        $keys = array_column($result, 'label');
        array_multisort($keys, SORT_ASC, $result);
        usort($result, fn($a, $b) => strcasecmp(
            iconv('utf-8', 'ascii//TRANSLIT', (string) $a['label']),
            iconv('utf-8', 'ascii//TRANSLIT', (string) $b['label'])
        ));
        return $result;
    }
}
