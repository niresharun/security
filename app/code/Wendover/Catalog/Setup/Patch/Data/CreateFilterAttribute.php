<?php
declare(strict_types=1);

namespace Wendover\Catalog\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Table;
use Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

class CreateFilterAttribute implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * CreateFilterableAttribute constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

     /**
     * {@inheritdoc}
     */
    public function apply()
    {
        // /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute(
            Product::ENTITY,
            'silhouette',
            [
                'group' => 'Mirror',
                'type' => 'text',
                'label' => 'Silhouette',
                'input' => 'multiselect',
                'class' => '',
                'source' => Table::class,
                'option' => ['values' => ['Bamboo', 'Bevel', 'Floater','Rustic', 'Shadowbox', 'Traditional', 'Transitional']], // Add your Silouette options here
                'backend' => ArrayBackend::class,
                'frontend' => '',
                'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
                'searchable' => true,
                'filterable' => true, // This attribute is filterable
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => ''
            ]
        );

        $attributeId = $eavSetup->getAttributeId(Product::ENTITY, 'silhouette');
        $eavSetup->updateAttribute(
            Product::ENTITY,
            $attributeId,
            ['is_filterable_in_search' => 1 , 'is_filterable' => 1 ]
        );

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
