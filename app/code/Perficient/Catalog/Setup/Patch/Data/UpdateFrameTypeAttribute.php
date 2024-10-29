<?php
/**
 * Update frame_type attribute for layered navigation
 * @category: Magento
 * @package: Perficient/Catalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Tahir Aziz<tahir.aziz@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Catalog
 */
declare(strict_types=1);

namespace Perficient\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

/**
 * Class UpdateFrameTypeAttribute
 * product frame type attribute update
 */
class UpdateFrameTypeAttribute implements DataPatchInterface
{
    /**
     * frame type attribute
     */
    const FRAME_TYPE_ATTRIBUTE = 'frame_type';

    /**
     * UpdateFrameTypeAttribute  constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        private readonly EavSetupFactory          $eavSetupFactory,
        private readonly ModuleDataSetupInterface $moduleDataSetup
    )
    {
    }

    /**
     * @inheritdoc
     */

    public function apply(): void
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $attributeId = $eavSetup->getAttributeId(Product::ENTITY, self::FRAME_TYPE_ATTRIBUTE);
        if ($attributeId) {
            $eavSetup->updateAttribute(Product::ENTITY, self::FRAME_TYPE_ATTRIBUTE, 'is_filterable', 1);
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
