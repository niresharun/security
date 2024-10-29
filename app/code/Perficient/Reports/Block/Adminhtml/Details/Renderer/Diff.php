<?php
/**
 * Log Company Change Information
 * @category: Magento
 * @package: Perficient/Reports
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<Vikramraj.Sahu@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Reports
 */

declare(strict_types=1);

namespace Perficient\Reports\Block\Adminhtml\Details\Renderer;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class Diff
 * @package Perficient\Reports\Block\Adminhtml\Details\Renderer
 */
class Diff extends AbstractRenderer
{
    /**
     * Serializer Instance
     *
     * @var Json $json
     */
    private $json;

    /**
     * Constructor Method
     *
     * @param Context $context
     * @param Json|null $json
     */
    public function __construct(
        Context $context,
        array $data = [],
        Json $json = null
    ) {
        $this->json = $json ?: ObjectManager::getInstance()->get(Json::class);
        parent::__construct($context, $data);
    }

    /**
     * Render the grid cell value
     *
     * @param DataObject $row
     * @return string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function render(DataObject $row)
    {
        $html = '-';
        $columnData = $row->getData($this->getColumn()->getIndex());
        try {
            $dataArray = $this->json->unserialize($columnData);

            if (is_array($dataArray)) {
                $dataArray = (array)$dataArray;
                $html = '<dl class="list-parameters">';
                foreach ($dataArray as $key => $value) {

                    if(str_contains($key, '_' )) {
                        $displayKey = explode('_', $key);
                        $displayKey = array_map(fn($val) => ucfirst((string) $val), $displayKey);
                        $displayKey = implode(' ', $displayKey);
                    } else {
                        $displayKey = $key;
                    }
                    $html .= '<dt class="parameter"><strong>'
                        . $this->escapeHtml($displayKey) . '</strong></dt>';
                    if (!is_array($value)) {
                        $html .= '<dd class="value">' . $this->escapeHtml(
                            $value
                        ) . '&nbsp;</dd>';
                    } elseif ($key == 'time') {
                        $html .= '<dd class="value">' . $this->escapeHtml(
                            implode(":", $value)
                        ) . '&nbsp;</dd>';
                    } else {
                        foreach ($value as $valueKey => $arrayValue) {
                            if(str_contains($valueKey,'_' )) {
                                $displayValueKey = explode('_', $valueKey);
                                $displayValueKey = array_map(fn($val) => ucfirst((string) $val), $displayValueKey);
                                $displayValueKey = implode(' ', $displayValueKey);
                            } else {
                                $displayValueKey = ucfirst($valueKey);
                            }

                            $html .= '<dt class="parameter">' . $this->escapeHtml($displayValueKey) . '</dt>';
                            if (is_array($arrayValue)) {
                                $html .= '<dd class="value">';
                                foreach ($arrayValue as  $arrayValueSub) {
                                    if (empty($arrayValueSub)) {
                                        continue;
                                    }
                                    $html .= $this->escapeHtml($arrayValueSub) . '<br/>';
                                }
                                $html .= '</dd>';
                            } else {
                                $html .= '<dd class="value">' . $this->escapeHtml($arrayValue) . ' &nbsp;</dd>';
                            }
                        }
                    }
                }

                $html .= '</dl>';
            } else {
                $html = $columnData;
            }
        } catch (\Exception $e) {
            $html = $e->getMessage();// $columnData;
        }
        return $html;
    }
}
