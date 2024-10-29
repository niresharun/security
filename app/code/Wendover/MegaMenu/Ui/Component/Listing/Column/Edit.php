<?php

namespace Wendover\MegaMenu\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class Edit extends Column
{
    const EDIT = 'menu/index/edit';
    const DELETE = 'menu/index/delete';

    protected $urlBuilder;
    private $editUrl;
    private $deleteUrl;

    public function __construct(
        ContextInterface   $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface       $urlBuilder,
        array              $components = [],
        array              $data = [],
                           $editUrl = self::EDIT,
                           $deleteUrl = self::DELETE
    )
    {
        $this->urlBuilder = $urlBuilder;
        $this->editUrl = $editUrl;
        $this->deleteUrl = $deleteUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['menu_id'])) {
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl($this->editUrl, ['menu_id' => $item['menu_id']]),
                        'label' => __('Edit')
                    ];
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl($this->deleteUrl, ['menu_id' => $item['menu_id']]),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete'),
                            'message' => __('Are you sure you wan\'t to delete record?')
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}
