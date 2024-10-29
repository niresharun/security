<?php

namespace Wendover\MegaMenu\Ui\DataProvider;

use Wendover\MegaMenu\Model\ResourceModel\SubMenu\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Framework\Serialize\SerializerInterface;

class SubMenuDataProvider extends AbstractDataProvider
{
    protected $serializer;
    protected $loadedData;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        SerializerInterface $serializer,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $collectionFactory->create();
        $this->serializer = $serializer;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    public function getData()
    {
       $items = $this->collection->getItems();
        $this->loadedData=[];
        foreach ($items as $model) {
            $this->loadedData[$model->getSubmenuId()]['submenu_configuration'] = $model->getData();
            if ($model->getData('child_menu')) {
                $this->loadedData[$model->getSubmenuId()]['childmenu_configuration']['child_menu']['child_menu'] = $this->serializer->unserialize($model->getData('child_menu'));
            }
        }
        return $this->loadedData;

    }
}
