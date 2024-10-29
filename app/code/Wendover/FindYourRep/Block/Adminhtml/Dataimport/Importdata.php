<?php

namespace Wendover\FindYourRep\Block\Adminhtml\Dataimport;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;

class Importdata extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @param Context $context
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        protected \Magento\Framework\Registry $_coreRegistry,
        array                                 $data = []
    )
    {
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'row_id';
        $this->_blockGroup = 'Wendover_FindYourRep';
        $this->_controller = 'adminhtml_dataimport';
        parent::_construct();
        $this->buttonList->remove('back');
        $this->buttonList->update('save', 'label', __('Import'));
        $this->buttonList->remove('reset');

        $this->addButton(
            'backhome',
            [
                'label' => __('Back'),
                'on_click' => sprintf("location.href = '%s';", $this->getUrl('representative/rep/index')),
                'class' => 'back',
                'level' => -2
            ]
        );
    }

    /**
     * @return Phrase|string
     */
    public function getHeaderText()
    {
        return __('Import Rep Data');
    }

    /**
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * @return array|mixed|string|null
     */
    public function getFormActionUrl()
    {
        if ($this->hasFormActionUrl()) {
            return $this->getData('form_action_url');
        }
        return $this->getUrl('representative/dataimport/save');
    }
}
