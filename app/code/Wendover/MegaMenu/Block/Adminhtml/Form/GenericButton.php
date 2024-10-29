<?php


namespace Wendover\MegaMenu\Block\Adminhtml\Form;

use Magento\Backend\Block\Widget\Context;

class GenericButton
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;
    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Widget\Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
        $this->urlBuilder = $context->getUrlBuilder();
    }

    /**
     * Return the synonyms group Id.
     *
     * @return int|null
     */
    public function getMenuId()
    {
        return $this->context->getRequest()->getParam('menu_id');
    }

    /**
     * Return the synonyms group Id.
     *
     * @return int|null
     */
    public function getSubMenuId()
    {
        return $this->context->getRequest()->getParam('submenu_id');
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
