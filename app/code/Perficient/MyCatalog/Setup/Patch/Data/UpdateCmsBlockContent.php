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

/**
 * Class UpdateCmsBlockContent
 * @package Perficient\MyCatalog\Setup\Patch\Data
 */
class UpdateCmsBlockContent implements DataPatchInterface
{
    /**
     * UpdateCmsBlockContent constructor.
     *
     * @param SchemaSetupInterface $schemaSetup
     * @param Factory $configFactory
     */
    public function __construct(
        private readonly SchemaSetupInterface $schemaSetup,
        private readonly Factory $configFactory
    ){
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->schemaSetup->startSetup();
        //$this->updateMyCatalogBlock();
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

    /**
     * Method used to update an existing block with some catalog content.
     *
     * @return void
     */
    private function updateMyCatalogBlock()
    {
        /*$myCatalogBlock = $this->blockFactory->create()->load(
            'mycatalog_title',
            'identifier'
        );

		$contentValue = '<div class="my-catalog-title"><h2>Catalog Setup</h2>' .
            '<p>We\'ll start by creating the front and back covers. Make sure to give your catalog a title. ' .
            'You may also upload a custom logo and provide your name, phone, and company information on the back.' .
            'When you are done, click Continue to begin adding artwork to your catalog.</p></div>';

		if (!$myCatalogBlock->getId()) {
            $myCatalogBlock->setData($contentValue)->save();
        } else {
            $myCatalogBlock->setContent($contentValue)->save();
        }*/
    }
}
