<?php
namespace DCKAP\Productimize\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Wishlist\Model\ResourceModel\Item\CollectionFactory;
use Magento\Wishlist\Model\Item\OptionFactory;

class ImgRenderData extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;
    /**
     * @var OptionFactory
     */
    protected $optionFactory;


    public static $defaultConfLabel = [
        'liner_sku' => 'Liner',
        'frame_default_sku' => 'Frame',
        'top_mat_default_sku' => 'Top Mat',
        'bottom_mat_default_sku' => 'Bottom Mat',
        'side-mark' => 'Side Mark',
        'bottom_mat_sku' => 'Bottom Mat SKUs',
        'frame_width' => 'Frame Width',
        'item_height' => 'Item Height',
        'item_width' => 'Item Width',
        'medium' => 'Medium',
        'glass_width' => 'Glass Width',
        'glass_height' => 'Glass Height',
        'liner_width' => 'Liner Width',
        'bottom_mat_size_bottom' => 'Bottom Mat Size Bottom',
        'bottom_mat_size_left' => 'Bottom Mat Size Left',
        'bottom_mat_size_right' => 'Bottom Mat Size Right',
        'bottom_mat_size_top' => 'Bottom Mat Size Top',
        'image_height' => 'Image Height',
        'image_width' => 'Image Width',
        'top_mat_size_bottom' => 'Top Mat Size Bottom',
        'top_mat_size_left' => 'Top Mat Size Left',
        'top_mat_size_right' => 'Top Mat Size Right',
        'top_mat_size_top' => 'Top Mat Size Top',
        'treatment' => 'Treatment',
    ];

    public static $alternateDefaultConfKeys = [
        'medium' => 'medium_default_sku',
        'treatment' => 'treatment_default_sku',
        'Size' => 'size_default_sku',
        'frame_default_sku' => 'frame_default_sku',
        'top_mat_default_sku' => 'top_mat_default_sku',
        'bottom_mat_default_sku' => 'bottom_mat_default_sku',
        'liner_sku' => 'liner_default_sku',
    ];
    public static $specificationArray = [
        'Medium' => 'None',
        'Treatment' => 'None',
        'Size' => 'None',
        'Frame' => 'None',
        'Top Mat' => 'None',
        'Bottom Mat' => 'None',
        'Liner' => 'None',
    ];

    public static $editUrlLabelKeys = array(

        'liner_sku' => 'liner',
        'frame_default_sku' => 'frame',
        'top_mat_default_sku' => 'top mat',
        'top mat' => 'top mat',
        'topMat' => 'top mat',
        'topmat' => 'top mat',
        'bottom_mat_default_sku' => 'bottom mat',
        'bottom mat' => 'bottom mat',
        'bottomMat' => 'bottom mat',
        'bottommat' => 'bottom mat',
        'medium' => 'medium',
        'treatment' => 'treatment',
        'size' => 'size',
        'liner' => 'liner',
        'frame' => 'frame',
        'artwork color' => 'artwork color',
        'sidemark' => 'sidemark'

    );


    public function __construct(
        Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Checkout\Model\SessionFactory $_checkoutSession,
        OptionFactory $optionFactory
    )
    {
        parent::__construct($context);
        $this->request = $request;
        $this->_checkoutSession = $_checkoutSession;
        $this->optionFactory = $optionFactory;
    }


}
