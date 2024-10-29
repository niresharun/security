<?php

namespace Wendover\Checkout\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\LayoutInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /** @var LayoutInterface  */
    protected $_layout;

    public function __construct(LayoutInterface $layout)
    {
        $this->_layout = $layout;
    }

    public function getConfig()
    {
        $myBlockId = "fedex_notice"; // CMS Block Identifier

        return [
            'fedex_notice' => $this->_layout->createBlock('Magento\Cms\Block\Block')->setBlockId($myBlockId)->toHtml()
        ];
    }
}
