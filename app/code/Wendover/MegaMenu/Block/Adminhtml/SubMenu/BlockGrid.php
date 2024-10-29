<?php

namespace Wendover\MegaMenu\Block\Adminhtml\SubMenu;

class BlockGrid extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'blockgrid.phtml';

    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tabs\Product
     */
    protected $blockGrid;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Hexamarvel\FlexibleForm\Model\FieldSetFactory $fieldSetFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array                                   $data = []
    )
    {
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                \Wendover\MegaMenu\Block\Adminhtml\SubMenu\Grid::class,
                'tab_submenu_grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getNotice()
    {
        if ($this->getRequest()->getParam('menu_id', false)) {
            return;
        } else {
            return __('Please Save The Menu.');
        }
    }
}

