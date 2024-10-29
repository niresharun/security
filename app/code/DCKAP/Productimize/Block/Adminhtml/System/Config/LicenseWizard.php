<?php
/**
 * @author     DCKAP <extensions@dckap.com>
 * @package    DCKAP_Productimize
 * @copyright  Copyright (c) 2017 DCKAP Inc (http://www.dckap.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace DCKAP\Productimize\Block\Adminhtml\System\Config;

use Magento\Store\Model\StoreManagerInterface as StoreManager;

/**
 * Custom renderer for PayPal API credentials wizard popup
 */
class LicenseWizard extends \Magento\Config\Block\System\Config\Form\Field
{
     private $storeManager;
    /**
     * Path to block template
     */
    const WIZARD_TEMPLATE = 'system/config/license_wizard.phtml';

    public function __construct(
        StoreManager $storeManager){
        $this->storeManager = $storeManager;
    }

    /**
     * Set template to itself
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate(static::WIZARD_TEMPLATE);
        }
        return $this;
    }

    protected function getSiteUrl(){
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
    }
    /**
     * Unset some non-related element parameters
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
   /* protected function _getElementHtml(Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }*/ 
}
