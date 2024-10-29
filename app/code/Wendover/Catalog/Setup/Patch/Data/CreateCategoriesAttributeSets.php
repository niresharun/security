<?php
declare(strict_types=1);

namespace Wendover\Catalog\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class CreateCategoriesAttributeSets implements DataPatchInterface
{
    public const MIRROR_ATTRIBUTESET_NAME = 'Mirror';
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

      /**
     * @var CategoryFactory
     */
    private $categoryFactory;

      /**
     * @var AttributeSetFactory
     */
    private $attributeSetFactory;

      /**
     * @var CategorySetupFactory
     */
    private $categorySetupFactory;

    /**
     * CreateCategoriesAttributeSets constructor.
     *
     * @param ModuleDataSetupInterface   $moduleDataSetup
     * @param CategoryFactory            $categoryFactory
     * @param Category                   $category
     * @param AttributeSetFactory        $attributeSetFactory
     * @param CategorySetupFactory       $categorySetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface  $moduleDataSetup,
        CategoryFactory           $categoryFactory,
        AttributeSetFactory       $attributeSetFactory,
        CategorySetupFactory      $categorySetupFactory
    ) {
        $this->moduleDataSetup    = $moduleDataSetup;
        $this->categoryFactory    = $categoryFactory;

        $this->attributeSetFactory = $attributeSetFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }

     /**
     * {@inheritdoc}
     */
    public function apply()
    {

        $categorySetup = $this->categorySetupFactory->create(['setup' => $this->moduleDataSetup]);

        $attributeSet = $this->attributeSetFactory->create();
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);
        $data = [
            'attribute_set_name' => self::MIRROR_ATTRIBUTESET_NAME,
            'entity_type_id' => $entityTypeId,
            'sort_order' => 200,
        ];
        $attributeSet->setData($data);
        $attributeSet->validate();
        $attributeSet->save();
        $attributeSet->initFromSkeleton($attributeSetId);
        $attributeSet->save();


        $subMenu = [
            "Bamboo", "Bevel", "Floater", "Rustic", "Shadowbox", "Traditional", "Transitional", "View All Mirrors"
        ];


        $categoryTitle = 'browse';
        $collection = $this->categoryFactory->create()->getCollection()->addAttributeToFilter('url_path',$categoryTitle)->setPageSize(1);
        $parentCategory = $collection->getFirstItem();



        $mainCategory = $this->categoryFactory->create();
         $mainCategory->setPath($parentCategory->getPath());
        $mainCategory->setParentId($parentCategory->getId());
        $mainCategory->setName('Mirrors');
        $mainCategory->setDisplayMode(Category::DM_PAGE);
        $mainCategory->setIsActive(true);
        $mainCategory->save();

        foreach($subMenu as $menu){
            $subCategory = $this->categoryFactory->create();
            $subCategory->setPath($mainCategory->getPath());
            $subCategory->setParentId($mainCategory->getId());
            $subCategory->setName($menu);
            $subCategory->setIsActive(true);
            $subCategory->save();
        }

    }

    /**
     * {@inheritdoc}
     */
public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}
