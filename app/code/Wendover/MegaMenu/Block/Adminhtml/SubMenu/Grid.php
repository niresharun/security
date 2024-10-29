<?php
namespace Wendover\MegaMenu\Block\Adminhtml\SubMenu;

use Magento\Backend\Block\Widget\Grid\Extended;
use Wendover\MegaMenu\Model\ResourceModel\SubMenu\Collection;

class Grid extends Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */

    protected $subMenuFactory;


    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        Collection $subMenuCollection,
        array $data = []
    ) {
        $this->subMenuCollection = $subMenuCollection;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('display_submenu_grid');
        $this->setDefaultSort('submenu_id');
        $this->setUseAjax(true);
    }

    /**
     * @return string buttonHtml
     */
    public function getMainButtonsHtml()
    {
        if ($this->getRequest()->getParam('menu_id', false)) {
            $addUrl = $this->getUrl('menu/submenu/new', ['menu_id' => $this->getRequest()->getParam('menu_id', false)]);
            $addButton = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
                ->setData([
                    'label'     => __('Add SubMenu'),
                    'onclick'   => "setLocation('" . $addUrl . "')",
                    'class'   => 'primary'
                ])->toHtml();
            return $addButton;
        }
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        $collection = $this->subMenuCollection;
        $id = (int)$this->getRequest()->getParam('menu_id', false);

        if ($id) {
            $collection->addFieldToFilter(
                'menu_id',
                $id
            );
        } else {
            $collection->addFieldToFilter(
                'menu_id',
                '0'
            );
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'submenu_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'submenu_id',
                'filter' => false,
            ]
        );
        $this->addColumn('submenu_title', ['header' => __('Sub Menu Title'), 'index' => 'submenu_title','filter' => false]);
        $this->addColumn('submenu_class', ['header' => __('Sub Menu Class'), 'index' => 'submenu_class','filter' => false]);
        $this->addColumn('submenu_url', ['header' => __('Sub Menu URL'), 'index' => 'submenu_url','filter' => false]);
        $this->addColumn('submenu_sort_order', ['header' => __('Sort Order'), 'index' => 'submenu_sort_order','filter' => false]);

        $this->addColumn(
            'action',
            [
                'header' => __('Actions'),
                'renderer'  => \Wendover\MegaMenu\Block\Adminhtml\SubMenu\Tab\Renderer\Action::class,
                'type' => 'action',
                'getter' => 'getId',
                'sortable' => false,
                'filter' => false,
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'class' => '',
                        'url' => [
                            'base' => '*/submenu/edit',
                            'params' => [
                                'submenu_id' => $this->getRequest()->getParam('submenu_id', false),
                                'menu_id' => $this->getRequest()->getParam('menu_id', false)
                            ]
                        ],
                        'field' => 'submenu_id'
                    ],
                    [
                        'caption' => __('Delete'),
                        'url' => [
                            'base' => '*/submenu/delete',
                            'params' => ['submenu_id' => $this->getRequest()->getParam('submenu_id', false)]
                        ],
                        'field' => 'submenu_id'
                    ]
                ],
            ]
        );
        return parent::_prepareColumns();
    }

}
