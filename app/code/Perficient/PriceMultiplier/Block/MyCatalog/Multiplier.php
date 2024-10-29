<?php
/**
 * Block to get my catalg price multiplier
 *
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Daniel Wargolet <Daniel.Wargolet@perficient.com>
 * @keywords: price multiplier my catalog
 */
declare(strict_types=1);
namespace Perficient\PriceMultiplier\Block\MyCatalog;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Perficient\MyCatalog\Api\MyCatalogRepositoryInterface;
use Perficient\Company\Helper\Data;

/**
 * Class Multiplier
 * @package Perficient\PriceMultiplier\Block\MyCatalog
 */
class Multiplier extends Template
{
    /**
     * Multiplier constructor.
     * @param Context $context
     * @param MyCatalogRepositoryInterface $myCatalogRepository
     * @param Data $helperData
     */
    public function __construct(
        Context                                       $context,
        private readonly MyCatalogRepositoryInterface $myCatalogRepository,
        private readonly Data                         $helperData,
        array                                         $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Load the MyCatalog object by catalog_id, return the price modifier
     */
    public function getMultiplierPriceValue(): mixed
    {
        $catalogId     = $this->getMyCatalogId();
        $myCatalogRepo = $this->myCatalogRepository->getById($catalogId);
        //@todo: update schmema so price is defaulted to 1 instead of null
        return $myCatalogRepo->getPriceModifier() ?? 1;
    }

    /**
     * Get the catalog id
     */
    public function getMyCatalogId(): mixed
    {
       return $this->getRequest()->getParam('catalog_id');
    }

    public function isAllowedMultiplier(): bool
    {
        return $this->helperData->isAllowedMultiplier();
    }

    public function getPriceMultiplierValues(): array
    {
        return $this->helperData->getPriceMultiplierValues();
    }
}
