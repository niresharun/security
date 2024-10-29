<?php
/**
 * Company Custom Fields.
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */
declare(strict_types=1);

namespace Perficient\Company\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ResidentialProjects implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        $result = [];
        foreach ($this->getOptions() as $value => $label) {
            $result[] = [
                'value' => $value,
                'label' => $label,
            ];
        }
        return $result;
    }

    public function getOptions(): array
    {
        return [
            'Residential' => 'Residential',
            'Model Home / Staging' => 'Model Home / Staging',
            'Set Design' => 'Set Design',
        ];
    }
}
