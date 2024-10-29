<?php
/**
 * Company Custom Fields.
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu <vikramraj.sahu@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */
declare(strict_types=1);

namespace Perficient\Company\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Projects implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        $residential = [];
        $commercial = [];
        $result = [];
        $residential['label'] = 'Residential ';
        $residential['type'] = 'residential';
        foreach ($this->getResidentialOptions() as $value => $label) {
            $residential['value'][] = [
                'value' => $value,
                'label' => $label
            ];
        }
        $result[] = $residential;

        $commercial['label'] = 'Commercial ';
        $commercial['type'] = 'commercial';
        foreach ($this->getCommercialOptions() as $value => $label) {
            $commercial['value'][] = [
                'value' => $value,
                'label' => $label
            ];
        }
        $result[] = $commercial;
        return $result;
    }

    public function getResidentialOptions(): array
    {
        return [
            'Residential' => 'Residential',
            'Model Home / Staging' => 'Model Home / Staging',
            'Set Design' => 'Set Design'
        ];
    }

    public function getCommercialOptions(): array
    {
        return [
            'Hospitality' => 'Hospitality',
            'Healthcare' => 'Healthcare',
            'Senior Living' => 'Senior Living',
            'Clubhouses' => 'Clubhouses',
            'Multi-Family' => 'Multi-Family',
            'Restaurants' => 'Restaurants',
            'Corporate Spaces' => 'Corporate Spaces',
            'Government / Education' => 'Government / Education'
        ];
    }
}
