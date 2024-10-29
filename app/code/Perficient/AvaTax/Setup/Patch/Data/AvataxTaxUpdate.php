<?php
/**
 * AvaTax Configuration
 *
 * @category : PHP
 * @package  : Perficient_AvaTax
 * @copyright: Copyright © 2021 Magento. All rights reserved.
 * @license  : Perficient, Inc.
 * @author   : Sandeep Mude <sandeep.mude@perficient.com>
 * @keywords : Perficient AvaTax, Configuration
 */
declare(strict_types = 1);

namespace Perficient\AvaTax\Setup\Patch\Data;

use Exception;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Tax\Model\ClassModel;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Api\Data\GroupInterfaceFactory;
use Magento\Tax\Api\TaxClassRepositoryInterface;
use Magento\Tax\Api\Data\TaxClassInterfaceFactory;


/**
 * Class AvataxConfiguration
 * @package Perficient\AvaTax\Setup\Patch\Data
 */
class AvataxTaxUpdate implements DataPatchInterface
{
    /**
     *  Taxable Goods class ID constant
     */
    final public const CLASS_TAXABLE_GOODS_ID = 2;

    private array $customerTaxClass = [
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'MED1', 'MED2'
    ];

    /**
     * AvataxTaxUpdate constructor.
     */
    public function __construct(
        private readonly TaxClassRepositoryInterface $taxClassRepository,
        private readonly TaxClassInterfaceFactory $taxClassDataObjectFactory,
        private readonly GroupInterfaceFactory $groupFactory,
        private readonly GroupRepositoryInterface $groupRepository
    ) {
    }

    /**
     * Avatax configuration changes
     * @return $this|void
     * @throws Exception
     */
    public function apply()
    {
        $this->updateTaxableGoodsClass();
        $this->createCustomerTaxClassAndGroup();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @throws Exception
     */
    private function createCustomerTaxClassAndGroup()
    {
        try {
            foreach ($this->customerTaxClass as $taxClassText ) {
                $taxClass = $this->taxClassDataObjectFactory->create()
                    ->setClassType(ClassModel::TAX_CLASS_TYPE_CUSTOMER)
                    ->setClassName($taxClassText)
                    ->setAvataxCode((string)$taxClassText);
                $taxClassId = $this->taxClassRepository->save($taxClass);

                $group = $this->groupFactory->create();
                $group->setCode((string)$taxClassText)
                    ->setTaxClassId($taxClassId);
                $this->groupRepository->save($group);
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    private function updateTaxableGoodsClass()
    {
        try {
            $taxClass = $this->taxClassRepository->get(SELF::CLASS_TAXABLE_GOODS_ID);
            $taxClass->setAvataxCode('P0000000');
            $this->taxClassRepository->save($taxClass);
        } catch (Exception $e) {
            throw $e;
        }
    }
}
