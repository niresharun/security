<?php

/**
 * RequisitionList Converted to Market Scans with project specific configurations
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Tahir Aziz <tahir.aziz@perficient.com>
 * @project: Wendover
 * @keywords:  Module Perficient_ViewInRoom
 */
declare(strict_types=1);

namespace Perficient\Catalog\Setup\Patch\Data;

use Magento\Eav\Model\Config;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\Category;

class CreateCategoryImgAttribute implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * ADDITIONAL_IMG
     */
    const ADDITIONAL_IMG = 'additional_image';

    /**
     * CreateAttributes constructor.
     * @param ModuleDataSetupInterface $setup
     * @param Config $eavConfig
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        protected ModuleDataSetupInterface $setup,
        protected Config                   $eavConfig,
        private readonly EavSetupFactory   $eavSetupFactory
    )
    {
    }

    /**
     * @return DataPatchInterface|void
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->setup]);

        $categoryAttribute = [
            self::ADDITIONAL_IMG => [
                'type' => 'varchar',
                'label' => 'Additional Image',
                'input' => 'image',
                'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                'required' => false,
                'sort_order' => 100,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => '',
            ]
        ];


        foreach ($categoryAttribute as $attributeCode => $attributeDetails) {

            if (!$eavSetup->getAttributeId(Category::ENTITY, $attributeCode)) {
                $eavSetup->addAttribute(
                    Category::ENTITY,
                    $attributeCode,
                    $attributeDetails
                );
            }

        }
    }

    public function revert()
    {
        $eavSetup = $this->attributeSetFactory->create(['setup' => $this->setup]);
        foreach ([
                     self::ADDITIONAL_IMG
                 ] as $attribute) {
            try {
                $eavSetup->removeAttribute(Category::ENTITY, $attribute);
            } catch (\Exception) {
                // ignore
            }
        }
    }

    /**
     * The default magento OOB method used to get aliases.
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * The default magento OOB method used to get dependencies.
     */
    public static function getDependencies(): array
    {
        return [];
    }

}
