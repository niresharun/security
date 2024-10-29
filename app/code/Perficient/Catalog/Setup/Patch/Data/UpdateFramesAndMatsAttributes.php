<?php
/**
 * Update Sort By For ViewAll Frames.
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

use Amasty\ShopbyBase\Model\FilterSettingRepository;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Amasty\ShopbyBase\Api\Data\FilterSettingRepositoryInterface;
use PHPUnit\Util\Exception;

/**
 * Class UpdateFramesAndMatsAttributes
 * @package Perficient\Catalo\Setup\Patch\Data
 */
class UpdateFramesAndMatsAttributes implements DataPatchInterface
{
    const FRAME_MATS_ATTRIBUTE = ['color_family_frame', 'color_family_mat', 'color_frame', 'color_mat'];

    /**
     * UpdateFramesAndMatsAttributes constructor.
     * @param FilterSettingRepositoryInterface $filterSettingRepository
     */
    public function __construct(
        private readonly FilterSettingRepositoryInterface $filterSettingRepository
    )
    {
    }

    /**
     * Update Attribute Filter Setting For Frames and Mats
     */
    public function apply(): void
    {
        /*        foreach(self::FRAME_MATS_ATTRIBUTE as $attributeCode) {
                    $filterCode = \Amasty\Shopby\Helper\FilterSetting::ATTR_PREFIX . $attributeCode;
                    try {
                        $attribFilterSettings = $this->filterSettingRepository->get($filterCode, 'filter_code');
                        if ($attribFilterSettings->getId()) {
                            $attribFilterSettings->setIsMultiselect(1);
                        }
                        $this->filterSettingRepository->save($attribFilterSettings);
                    } catch (Exception $e) {
                        //do nothing
                    }
                }
        */
    }

    /**
     * @return array
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @return array
     */
    public function getAliases()
    {
        return [];
    }
}
