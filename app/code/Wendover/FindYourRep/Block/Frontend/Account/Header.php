<?php

namespace Wendover\FindYourRep\Block\Frontend\Account;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;

class Header extends \Magento\Theme\Block\Html\Header
{
    /**
     * @param Context $context
     * @param UrlInterface $urlInterface
     */
    public function __construct(
        Context                       $context,
        private readonly UrlInterface $urlInterface
    )
    {
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getPathURL($path)
    {
        return $this->urlInterface->getUrl($path);
    }
}
