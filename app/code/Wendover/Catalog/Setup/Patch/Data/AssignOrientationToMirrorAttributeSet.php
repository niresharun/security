<?php
declare(strict_types=1);

namespace Wendover\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeManagementInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AssignOrientationToMirrorAttributeSet implements DataPatchInterface
{
    private $attributeCodes = ['orientation', 'color'];
    private $group = 'Mirror';

    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly EavSetupFactory $eavSetupFactory,
        private readonly AttributeManagementInterface $attributeManagement
    ) {
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [CreateCategoriesAttributeSets::class];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
        $attributeSet = $eavSetup->getAttributeSet(
            Product::ENTITY,
            CreateCategoriesAttributeSets::MIRROR_ATTRIBUTESET_NAME
        );
        if (!empty($attributeSet)) {
            $attributeSetId = $attributeSet['attribute_set_id'];
            $group_id = $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, $this->group);
            $sortOrder = 31;
            foreach ($this->attributeCodes as $attributeCode) {
                $this->attributeManagement->assign(
                    Product::ENTITY,
                    $attributeSetId,
                    $group_id,
                    $attributeCode,
                    $sortOrder++
                );
            }
        }
    }
}
