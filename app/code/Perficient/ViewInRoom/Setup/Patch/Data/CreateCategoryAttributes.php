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

namespace Perficient\ViewInRoom\Setup\Patch\Data;

use Magento\Eav\Model\Config;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\Category;

class CreateCategoryAttributes implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * Wall Width
     */
    const WALL_WIDTH = 'vir_wall_width';

    /**
     * Wall Height
     */
    const WALL_HEIGHT = 'vir_wall_height';

    /**
     * Offset for art image
     */
    const CENTER_OFFSET = 'vir_center_offset';

    /**
     * Wall Image for background
     */
    const BACKROUND_IMAGE = 'vir_background_img';

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

        $categoryAttributes = [
            self::WALL_WIDTH => [
                'type' => 'varchar',
                'label' => 'Wall Width',
                'input' => 'text',
                'sort_order' => 110,
                'source' => '',
                'global' => 1,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => null,
                'group' => '',
                'backend' => ''
            ],
            self::WALL_HEIGHT => [
                'type' => 'varchar',
                'label' => 'Wall Height',
                'input' => 'text',
                'sort_order' => 120,
                'source' => '',
                'global' => 1,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => null,
                'group' => '',
                'backend' => ''
            ],
            self::CENTER_OFFSET => [
                'type' => 'varchar',
                'label' => 'Center Offset',
                'input' => 'text',
                'sort_order' => 130,
                'source' => '',
                'global' => 1,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => null,
                'group' => '',
                'backend' => ''
            ],
            self::BACKROUND_IMAGE => [
                'type' => 'varchar',
                'label' => 'Background Image',
                'input' => 'image',
                'backend' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
                'required' => false,
                'sort_order' => 100,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'group' => '',
            ]
        ];

        foreach ($categoryAttributes as $attributeCode => $attributeDetails) {
            $eavSetup->addAttribute(
                Category::ENTITY,
                $attributeCode,
                $attributeDetails
            );
        }
    }

    public function revert()
    {
        $eavSetup = $this->attributeSetFactory->create(['setup' => $this->setup]);
        foreach ([
                     self::WALL_WIDTH,
                     self::WALL_HEIGHT,
                     self::CENTER_OFFSET,
                     self::BACKROUND_IMAGE
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

    /**
     * {@inheritdoc}
     */
    public static function getVersion(): string
    {
        return '1.0.0';
    }

}
