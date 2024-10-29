<?php
namespace DCKAP\Productimize\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use DCKAP\Productimize\Helper\Data as ProductimizeHelper;
use Magento\Msrp\Helper\Data;
use Magento\GiftMessage\Helper\Message;
use Magento\Catalog\Helper\Image;
use Magento\Customer\Model\Session;


/**
 * Check is available add to compare.
 */
class PrepareHelperData implements ArgumentInterface
{
    /**
     * @var ProductimizeHelper
     */
    private $productimizeHelper;
    private $msrpHelper;
    private $giftMessageHelper;
    private $catalogImage;
    private $session;
    
    public function __construct(
        ProductimizeHelper $productimizeHelper,
        Data $msrpHelper,
        Message $giftMessageHelper,
        Image $catalogImage,
        Session $session
    )
    {
        $this->productimizeHelper = $productimizeHelper;
        $this->msrpHelper = $msrpHelper;
        $this->giftMessageHelper = $giftMessageHelper;
        $this->catalogImage = $catalogImage;
        $this->session = $session;
    }

    
    public function getProductimizeHelper()
    {
        return $this->productimizeHelper;
    }

    public function getMsrpHelper()
    {
        return $this->msrpHelper;
    }

    public function getGiftMessageHelper()
    {
        return $this->giftMessageHelper;
    }

    public function getCatalogImage()
    {
        return $this->catalogImage;
    }
    
    public function isCustomerLoggedIn()
    {
        if ($this->session->isLoggedIn()) {
            return true;
        }
    }

}