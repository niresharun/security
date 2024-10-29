<?php

Namespace Wendover\ReportImages\Cron;

use Psr\Log\LoggerInterface;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Wendover\ReportImages\Model\ResourceModel\MissingImages;
use Wendover\ReportImages\Model\MissingImagesFactory as ModelImages;
use Magento\Eav\Api\AttributeSetRepositoryInterface;

class MissingImagesCron {

    protected $collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory,
        protected MissingImages $missingImages,
        protected ModelImages $imagesModel,
        protected AttributeSetRepositoryInterface $eavAttribute
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Write to system.log
     *
     * @return void
     */
    public function execute() {
        $connection = $this->imagesModel->create()->getCollection()->getConnection();
        $tableName = $this->imagesModel->create()->getCollection()->getMainTable();
        $connection->truncateTable($tableName);

        $query = "SELECT `e`.*, cpev.value as name, status.value as status,  eav_set.attribute_set_name as attribute_set_name,  at_image_default.value AS `image`, at_small_image_default.value AS `small_image`, at_thumbnail_default.value AS `thumbnail`, at_cropped_default.value AS `cropped`, at_single_corner_default.value AS `single_corner`, at_spec_details_default.value AS `spec_details`, at_double_corner_default.value AS `double_corner`,  at_renderer_length_default.value AS `renderer_length`, at_renderer_corner_default.value AS `renderer_corner` FROM `catalog_product_entity` AS `e` LEFT JOIN eav_attribute_set AS `eav_set` ON `e`.attribute_set_id = `eav_set`.attribute_set_id  LEFT JOIN `catalog_product_entity_varchar` AS `cpev` ON (`cpev`.`row_id` = `e`.`entity_id`) AND (`cpev`.`attribute_id` = '71') AND (`cpev`.`store_id` = 0) LEFT JOIN catalog_product_entity_int AS `status` ON e.entity_id = status.row_id AND status.attribute_id = 96 LEFT JOIN `catalog_product_entity_varchar` AS `at_image_default` ON (`at_image_default`.`row_id` = `e`.`row_id`) AND (`at_image_default`.`attribute_id` = '85') AND `at_image_default`.`store_id` = 0 LEFT JOIN `catalog_product_entity_varchar` AS `at_small_image_default` ON (`at_small_image_default`.`row_id` = `e`.`row_id`) AND (`at_small_image_default`.`attribute_id` = '86') AND `at_small_image_default`.`store_id` = 0 LEFT JOIN `catalog_product_entity_varchar` AS `at_thumbnail_default` ON (`at_thumbnail_default`.`row_id` = `e`.`row_id`) AND (`at_thumbnail_default`.`attribute_id` = '87') AND `at_thumbnail_default`.`store_id` = 0 LEFT JOIN `catalog_product_entity_varchar` AS `at_cropped_default` ON (`at_cropped_default`.`row_id` = `e`.`row_id`) AND (`at_cropped_default`.`attribute_id` = '310') AND `at_cropped_default`.`store_id` = 0 LEFT JOIN `catalog_product_entity_varchar` AS `at_single_corner_default` ON (`at_single_corner_default`.`row_id` = `e`.`row_id`) AND (`at_single_corner_default`.`attribute_id` = '620') AND `at_single_corner_default`.`store_id` = 0 LEFT JOIN `catalog_product_entity_varchar` AS `at_spec_details_default` ON (`at_spec_details_default`.`row_id` = `e`.`row_id`) AND (`at_spec_details_default`.`attribute_id` = '624') AND `at_spec_details_default`.`store_id` = 0 LEFT JOIN `catalog_product_entity_varchar` AS `at_double_corner_default` ON (`at_double_corner_default`.`row_id` = `e`.`row_id`) AND (`at_double_corner_default`.`attribute_id` = '621') AND `at_double_corner_default`.`store_id` = 0 LEFT JOIN `catalog_product_entity_varchar` AS `at_renderer_length_default` ON (`at_renderer_length_default`.`row_id` = `e`.`row_id`) AND (`at_renderer_length_default`.`attribute_id` = '623') AND `at_renderer_length_default`.`store_id` = 0 LEFT JOIN `catalog_product_entity_varchar` AS `at_renderer_corner_default` ON (`at_renderer_corner_default`.`row_id` = `e`.`row_id`) AND (`at_renderer_corner_default`.`attribute_id` = '622') AND `at_renderer_corner_default`.`store_id` = 0";
        $products = $connection->fetchAll($query);


        foreach ($products as $product) {
            $websiteId = 1;            
            $attributeSetName = $product['attribute_set_name'];

            $model = $this->imagesModel->create()->load($product['row_id'], 'product_id');

            $image = ($product['image']=='no_selection' || !$product['image']) ? 0 : 1;
            $small_image = ($product['small_image']=='no_selection' || !$product['small_image']) ? 0 : 1;
            $thumbnail = ($product['thumbnail']=='no_selection' || !$product['thumbnail']) ? 0 : 1;
            $cropped = ($product['cropped']=='no_selection' || !$product['cropped']) ? 0 : 1;

            $single_corner = ($product['single_corner']=='no_selection' || !$product['single_corner']) ? 0 : 1;
            $spec_details = ($product['spec_details']=='no_selection' || !$product['spec_details']) ? 0 : 1;

            $double_corner = ($product['double_corner']=='no_selection' || !$product['double_corner']) ? 0 : 1;
            $renderer_length = ($product['renderer_length']=='no_selection' || !$product['renderer_length']) ? 0 : 1;
            $renderer_corner = ($product['renderer_corner']=='no_selection' || !$product['renderer_corner']) ? 0 : 1; 

            $flag = 0;

            if (empty($model->getId())) {
                if($attributeSetName == 'Art' && (!$image || !$small_image || (!$thumbnail && !$cropped))) {
                    $flag = 1;                  
                }
                else if($attributeSetName == 'Frame' || $attributeSetName == 'Liner') {
                    if( !$image || !$small_image || !$thumbnail || !$single_corner || !$spec_details || !$double_corner || !$renderer_length || !$renderer_corner) {
                       $flag = 1;
                    }
                }
                else if($attributeSetName == 'Mat' || $attributeSetName == 'Mirror') {
                    if(!$image || !$small_image || !$thumbnail) {
                        $flag = 1;                    
                    }
                }

                if($flag) {
                    $model = $this->imagesModel->create();
                    $model->setData(['product_id' => $product['row_id'],
                        'sku' => $product['sku'],
                        'attribute_set_id' => $product['attribute_set_id'],
                        'name' => $product['name'],
                        'status' => $product['status'],
                        'base' => $image,
                        'small' => $small_image,
                        'thumbnail' => $thumbnail,
                        'cropped_art' => $cropped,
                        'single_corner_image' => $single_corner,
                        'spec_detail_image' => $spec_details,
                        'double_corner_image' => $double_corner,
                        'renderer_length' => $renderer_length,
                        'renderer_corner' => $renderer_corner,
                        'store_id' => $websiteId
                    ]);
                    $model->save();                    
                }
                
            }
        }
    }

}

