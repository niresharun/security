<?php
/**
 * Override block for collaboration
 * @category: Magento
 * @package: Perficient/Wishlist
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sandeep Mude<Sandeep.mude@Perficient.com>
 * @keywords: Module Perficient_Wishlist
 */

declare(strict_types=1);

namespace Perficient\Wishlist\Block\Customer;

use Magento\Captcha\Block\Captcha;

class Collaboration extends \Magento\Wishlist\Block\Customer\Sharing
{
    /**
     * @var mixed
     */
    protected $wishlistId;

    /**
     * Collaboration constructor.
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context                $context,
        \Magento\Wishlist\Model\Config                                  $wishlistConfig,
        \Magento\Framework\Session\Generic                              $wishlistSession,
        protected \Magento\Framework\App\Request\Http                   $request,
        protected \Magento\Framework\App\Config\ScopeConfigInterface    $scopeConfig,
        protected \Magento\Framework\Module\Manager                     $_moduleManager,
        array                                            $data = []
    )
    {
        $this->wishlistId = $this->request->getParam('wishlist_id');
        parent::__construct($context, $wishlistConfig, $wishlistSession, $data);
    }

    /**
     * Prepare Global Layout
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        if (!$this->getChildBlock('captcha')) {
            $this->addChild(
                'captcha',
                Captcha::class,
                [
                    'cacheable' => false,
                    'after' => '-',
                    'form_id' => 'share_wishlist_form',
                    'image_width' => 230,
                    'image_height' => 230
                ]
            );
        }

        $this->pageConfig->getTitle()->set(__('Wish List Collaboration'));
    }

    /**
     * Retrieve send form action URL
     *
     * @return string
     */
    public function getSendUrl()
    {
        return $this->getUrl('*/*/sendCollaboration', ['wishlist_id' => $this->wishlistId]);
    }

    /**
     * Retrieve back button url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/index', ['wishlist_id' => $this->wishlistId]);
    }

    /**
     * Is allow RSS
     *
     * @return bool
     */
    public function isRssAllow()
    {
        return $this->_moduleManager->isEnabled('Magento_Rss')
            && $this->scopeConfig->isSetFlag(
                'rss/wishlist/active',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
    }

    /**
     * Check is allow wishlist module
     *
     * @return bool
     */
    public function isAllow()
    {
        $isWishlistActive = $this->scopeConfig->getValue(
            'wishlist/general/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        return $isWishlistActive;
    }
}
