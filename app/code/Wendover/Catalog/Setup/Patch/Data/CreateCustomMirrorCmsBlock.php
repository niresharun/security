<?php
declare(strict_types=1);

namespace Wendover\Catalog\Setup\Patch\Data;

use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\BlockRepository;
use Magento\Cms\Model\GetBlockByIdentifier;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;


class CreateCustomMirrorCmsBlock implements DataPatchInterface
{
    /**
     * CmsPages constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        private readonly ModuleDataSetupInterface $moduleDataSetup,
        private readonly BlockFactory             $blockFactory,
        private readonly GetBlockByIdentifier     $getBlockByIdentifier,
        private readonly BlockRepository          $blockRepository
    )
    {
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $this->createCmsBlocks();
        $this->moduleDataSetup->endSetup();
    }

    /**
     * Create CMS Bloges.
     */
    private function createCmsBlocks()
    {
        $cmsBlocks = [
            [
                'title' => 'Customise Mirror',
                'identifier' => 'custom_mirror_content',
                'stores' => [0],
                'is_active' => 1,
                'content' => "<div class='specialty test'>Need a unique, customized solution that is just right for you? Please contact  <a href='mailto:quotes.residential@wendoverart.com'>Quotes.residential@wendoverart.com</a></div>"
            ],
        ];
        foreach ($cmsBlocks as $cmsBlock) {
            try {
                $block = $this->getBlockByIdentifier->execute($cmsBlock['identifier'], 0);
                $block->setContent($cmsBlock['content']);
            } catch (NoSuchEntityException $e) {
                $block = $this->blockFactory->create();
                $block->setData($cmsBlock);
            }
            $this->blockRepository->save($block);
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
