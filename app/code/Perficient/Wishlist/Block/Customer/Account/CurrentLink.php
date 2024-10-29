<?php
/**
 * overide for making my projects link separate
 * @category: Magento
 * @package: Perficient/Wishlist
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<Sandeep.mude@Perficient.com>
 * @keywords: Module Perficient_Wishlist
 */

namespace Perficient\Wishlist\Block\Customer\Account;

use Magento\Framework\App\DefaultPathInterface;
use Magento\Framework\View\Element\Template\Context;

class CurrentLink extends \Magento\Customer\Block\Account\SortLink
{
    const DEFAULT_WISHLIST_LABEL = 'My Favorites';
    const DEFAULT_MYPROJECTS_LABEL = 'My Projects';

    /**
     * CurrentLink constructor.
     * @param Context $context
     * @param DefaultPathInterface $defaultPath
     */
    public function __construct(
        Context              $context,
        DefaultPathInterface $defaultPath,
        array                $data = []
    )
    {
        parent::__construct($context, $defaultPath, $data);
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        // if (false != $this->getTemplate()) {
        //     return parent::_toHtml();
        // }

        $highlight = '';

        if ($this->getIsHighlighted()) {
            $highlight = ' current';
        }

        if ($this->isCurrent()) {
            $html = '<li class="nav item current">';
            if (strtolower((string)$this->getLabel()) == strtolower(self::DEFAULT_WISHLIST_LABEL)) {
                $html = '<li class="nav item my-favorites current">';
            }
            if (strtolower((string)$this->getLabel()) == strtolower(self::DEFAULT_MYPROJECTS_LABEL)) {
                $html = '<li class="nav item my-projects current">';
            }

            $html .= '<strong>'
                . $this->_escaper->escapeHtml(__($this->getLabel()))
                . '</strong>';
            $html .= '</li>';
        } else {
            $html = '<li class="nav item' . $highlight . '"><a href="' . $this->_escaper->escapeHtml($this->getHref()) . '"';
            if (strtolower((string)$this->getLabel()) == strtolower(self::DEFAULT_WISHLIST_LABEL)) {
                $html = '<li class="nav item my-favorites' . $highlight . '"><a href="' . $this->_escaper->escapeHtml($this->getHref()) . '"';
            }
            if (strtolower((string)$this->getLabel()) == strtolower(self::DEFAULT_MYPROJECTS_LABEL)) {
                $html = '<li class="nav item my-projects' . $highlight . '"><a href="' . $this->_escaper->escapeHtml($this->getHref()) . '"';
            }
            $html .= $this->getTitle()
                ? ' title="' . $this->_escaper->escapeHtml(__($this->getTitle())) . '"'
                : '';
            $html .= $this->getAttributesHtml() . '>';

            if ($this->getIsHighlighted()) {
                $html .= '<strong>';
            }

            $html .= $this->_escaper->escapeHtml(__($this->getLabel()));

            if ($this->getIsHighlighted()) {
                $html .= '</strong>';
            }

            $html .= '</a></li>';
        }

        return $html;
    }

}
