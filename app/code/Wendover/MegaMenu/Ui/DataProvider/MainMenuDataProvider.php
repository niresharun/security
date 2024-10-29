<?php

namespace Wendover\MegaMenu\Ui\DataProvider;

use Wendover\MegaMenu\Model\ResourceModel\Menu\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class MainMenuDataProvider extends AbstractDataProvider
{
    protected $loadedData;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
        $items = $this->collection->getItems();
        $this->loadedData=[];
        foreach ($items as $model) {
            $this->loadedData[$model->getMenuId()]['main_menu'] = $model->getData();
        }
        return $this->loadedData;

    }
}
