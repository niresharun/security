<?php
/**
 * Update the CMS block content
 *
 * @category: Magento
 * @package: Perficient/MyCatalog
 * @copyright: Copyright © 2021 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Suhas Dhoke <Suhas.Dhoke@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyCatalog
 */
declare(strict_types=1);

namespace Perficient\MyCatalog\Setup\Patch\Data;

use Magento\Config\Model\Config\Factory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Cms\Model\BlockFactory;

/**
 * Class UpdateCmsBlockContent
 * @package Perficient\MyCatalog\Setup\Patch\Data
 */
class createMyCatalogBlock implements DataPatchInterface
{
    /**
     * Constant for table name.
     */
    const CATALOG_TABLE_NAME  = 'perficient_customer_gallery_catalog';
    const CUSTOMER_TABLE_NAME = 'customer_entity';
    const WISHLIST_TABLE_NAME = 'wishlist';

    /**
     * UpdateCmsBlockContent constructor.
     *
     * @param SchemaSetupInterface $schemaSetup
     * @param Factory $configFactory
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        private readonly SchemaSetupInterface $schemaSetup,
        private readonly Factory $configFactory,
        private readonly BlockFactory $blockFactory
    ){
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->schemaSetup->startSetup();
        $this->enableMultipleWishlist();
        $this->createMyCatalogBlock();
        $this->schemaSetup->endSetup();
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

    private function enableMultipleWishlist()
    {
        try {
            $configData = [
                'section' => 'wishlist',
                'website' => null,
                'store'   => null,
                'groups'  => [
                    'general' => [
                        'fields' => [
                            'multiple_enabled' => [
                                'value' => 1,
                            ],
                            'multiple_wishlist_number' => [
                                'value' => 99,
                            ],
                        ],
                    ],
                ],
            ];

            $this->configFactory->create()->setData($configData)->save();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Method used to create a block with some catalog content.
     *
     * @return void
     */
    private function createMyCatalogBlock()
    {
        $content = '<div class="my-catalog-title"><h1>Catalog Setup</h1>' .
            '<p>We\'ll start by creating the front and back covers. Make sure to give your catalog a title. ' .
            'You may also upload a custom logo and provide your name, phone, and company information on the back.' .
            'When you are done, click Continue to begin adding artwork to your catalog.</p></div>';
        $myCatalogBlock = [
            'title' => 'My Catalog Title',
            'identifier' => 'mycatalog_title',
            'stores' => [0],
            'is_active' => 1,
            'content' => $content
        ];
        $cmsBlock = $this->blockFactory->create();
        $cmsBlock->setData($myCatalogBlock)->save();
    }
}
