<?php
/**
 * Product Image Mapping
 * @category: Magento
 * @package: Perficient/CatalogPermissions
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase<Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\Config;
use Magento\Catalog\Model\Product;
use Magento\Eav\Api\AttributeManagementInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

/**
 * Class CreateNewImageTypes
 * @package Perficient\Catalog\Setup\Patch\Data
 */
class CreateNewImageTypes implements DataPatchInterface
{
    /**
     * CreateNewImageTypes constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Config $config
     * @param AttributeManagementInterface $attributeManagement
     */
    public function __construct(
        private readonly EavSetupFactory              $eavSetupFactory,
        private readonly ModuleDataSetupInterface     $moduleDataSetup,
        private readonly Config                       $config,
        private readonly AttributeManagementInterface $attributeManagement
    )
    {
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function apply(): void
    {
        $this->CreateNewImageTypes();
    }

    /**
     * Create custom product attribute
     */
    private function CreateNewImageTypes(): void
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $newImageTypeArray = [
            'single_corner' => 'Single Corner Image',
            'double_corner' => 'Double Corner Image',
            'renderer_corner' => 'Renderer Corner Image',
            'renderer_length' => 'Renderer Length Image',
            'spec_details' => 'Spec Details Image',
            'cropper_image' => 'Cropper Image',
            'default_option' => 'Default Option Image'
        ];
        foreach ($newImageTypeArray as $key => $value) {
            $this->processOperation($eavSetup, $key, $value);
        }
    }

    public function processOperation($eavSetup, $attribute_code, $attribute_label)
    {
        $entityTypeId = $eavSetup->getEntityTypeId(Product::ENTITY);
        $attributeSetIds = $eavSetup->getAllAttributeSetIds($entityTypeId);
        $eavSetup->addAttribute(
            Product::ENTITY,
            $attribute_code,
            [
                'type' => 'varchar',
                'label' => $attribute_label,
                'input' => 'media_image',
                'frontend' => 'Magento\Catalog\Model\Product\Attribute\Frontend\Image',
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'filterable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'sort_order' => 10,
                'required' => false,
            ]
        );
        foreach ($attributeSetIds as $attributeSetId) {
            if ($attributeSetId) {
                $group_id = $this->config->getAttributeGroupId($attributeSetId, 'Images');
                $this->attributeManagement->assign(
                    'catalog_product',
                    $attributeSetId,
                    $group_id,
                    $attribute_code,
                    999
                );
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function getAliases(): array
    {
        return [];
    }
}
