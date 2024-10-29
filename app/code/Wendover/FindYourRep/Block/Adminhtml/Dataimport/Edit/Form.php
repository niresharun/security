<?php

namespace Wendover\FindYourRep\Block\Adminhtml\Dataimport\Edit;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Asset\Repository;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_assetRepo;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Repository $assetRepo
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context  $context,
        \Magento\Framework\Registry              $registry,
        \Magento\Framework\Data\FormFactory      $formFactory,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        array                                    $data = []
    )
    {
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return Form
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        $path = $this->_assetRepo->getUrl("Wendover_FindYourRep::sample/files/Rep_Upload_Template.csv");
        $model = $this->_coreRegistry->registry('row_data');
        $form = $this->_formFactory->create(
            ['data' => [
                'id' => 'edit_form',
                'enctype' => 'multipart/form-data',
                'action' => $this->getData('action'),
                'method' => 'post'
            ]
            ]
        );
        $form->setHtmlIdPrefix('datalocation_');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Import Location '), 'class' => 'fieldset-wide']
        );
        $importData_script = $fieldset->addField(
            'importdata',
            'file',
            [
                'label' => 'Upload File',
                'required' => true,
                'name' => 'importdata',
                'note' => '<b>Note:</b><br> <b>-</b> Upload a .csv / .xls file less than 2MB. <br> <b>-</b> Recommend to import a maximum of 10,000 data per file.'
            ]
        );

        $importData_script->setAfterElementHtml("
        <span id='sample-file-span' ><a id='sample-file-link' href='" . $path . "'  >Download Sample File</a></span>
            <script type=\"text/javascript\">
            document.getElementById('datalocation_importdata').onchange = function () {
                var fileInput = document.getElementById('datalocation_importdata');

                var filePath = fileInput.value;
                var allowedExtensions = /(\.csv|\.xls)$/i;
                if(!allowedExtensions.exec(filePath)) {
                    alert('Please upload a file with only .csv or .xls file format.');
                    fileInput.value = '';
                }

                var maxSizeInBytes = 2097152; // 2MB
                var fileSize = fileInput.files[0].size;
                if (fileSize > maxSizeInBytes) {
                    alert('Uploaded file size exceeds the maximum limit (2MB).');
                    fileInput.value = '';
                }
            };
            </script>"
        );

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}

