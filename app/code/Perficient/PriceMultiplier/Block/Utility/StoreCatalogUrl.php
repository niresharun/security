<?php
/**
 * PriceMultiplier module for multiplier price .
 *
 * @category:  JS
 * @package:   Perficient/PriceMultiplier
 * See COPYING.txt for license details.
 * @author: Sandeep Mude <sandeep.mude@perficient.com>
 * @keywords:  Module Perficient_PriceMultiplier
 */
declare(strict_types=1);

namespace Perficient\PriceMultiplier\Block\Utility;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class StoreCatalogUrl
 * @package Perficient\PriceMultiplier\Block\Utility
 */
class StoreCatalogUrl extends Template
{
    /**
     * @var array
     */
    protected $jsLayout;

    /**
     * StoreCatalogUrl constructor.
     * @param Context $context
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout']) ? $data['jsLayout'] : [];
    }

    public function getJsLayout(): string
    {
        return \Zend_Json::encode($this->jsLayout);
    }

    /**
     * Returns popup config
     *
     * @throws NoSuchEntityException
     */
    public function getConfig(): array
    {
        return [
            'baseUrl' => $this->getBaseUrl()
        ];
    }

    /**
     * Return base url.
     *
     * @throws NoSuchEntityException
     */
    public function getBaseUrl(): string
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }
}
