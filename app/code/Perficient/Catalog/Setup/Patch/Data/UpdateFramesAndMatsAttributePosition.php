<?php
/**
 * Update attribute groups for frames and mats attribute Positions
 *
 * @category : PHP
 * @package  : Perficient_Catalog
 * @copyright: Copyright © 2020 Magento. All rights reserved.
 * @license  : Perficient, Inc.
 * @author   : Tahir Aziz <tahir.aziz@perficient.com>
 * @keywords : Perficient frames, mates, Category
 */
declare(strict_types=1);

namespace Perficient\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;

/**
 * Class UpdateFramesAndMatsAttributePosition
 * @package Perficient\Catalog\Setup\Patch\Data
 */
class UpdateFramesAndMatsAttributePosition implements DataPatchInterface
{
    /**
     * Position field
     */
    const FIELD_POSITION = 'position';

    private array $attributesAndPosition = [
        'color_family_frame' => 3,
        'color_family_mat' => 3,
        'color_frame' => 4,
        'color_mat' => 4,
        'frame_width_range' => 5,
        'mat_type' => 5,
        'frame_material' => 6
    ];

    /**
     * UpdateProductAttributePosition constructor.
     *
     * @param EavSetupFactory $eavSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param Attribute $eavAttribute
     * @param Config $eavConfig
     */
    public function __construct(
        private readonly EavSetupFactory          $eavSetupFactory,
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly Attribute                $eavAttribute,
        private readonly Config                   $eavConfig
    )
    {
    }

    /**
     * @inheritdoc
     */
    public function apply(): void
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        foreach ($this->attributesAndPosition as $attribute => $position) {
            // Check, if attribute is exists or not.
            $attr = $this->eavConfig->getAttribute(Product::ENTITY, $attribute);
            if ($attr && $attr->getId()) {
                $eavSetup->updateAttribute(Product::ENTITY, $attribute, self::FIELD_POSITION, $position);
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

    /**
     * @inheritdoc
     */
    public static function getDependencies(): array
    {
        return [];
    }
}
