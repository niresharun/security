<?php
/**
 * This module is used by employee who can add/update his personal information which needs to display his customers
 * @category: Magento
 * @package: Perficient/MyDisplayInformation
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyDisplayInformation
 */
declare(strict_types=1);

namespace Perficient\MyDisplayInformation\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\Request\Http;
use Perficient\MyDisplayInformation\Helper\Data;


/**
 * Class Data
 * @package Perficient\MyDisplayInformation\Helper
 */
class MyDisplayInformation implements ArgumentInterface
{
    public function __construct(
        private readonly Http $request,
        private readonly Data $helper
    )
    {
    }

    /**
     * @return string
     */
    public function previewForParent()
    {
        return $this->request->getPost()->toArray();
    }

    /**
     * @return int|mixed
     */
    public function previewForParentUrlParam()
    {
        $params = $this->request->getParams();
        return $params['preview'] ?? null;
    }

    public function getParentMydisplayPreview()
    {
        return $this->helper->getParentMydisplayPreview();
    }
}
