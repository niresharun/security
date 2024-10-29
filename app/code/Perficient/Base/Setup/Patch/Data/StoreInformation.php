<?php
/**
 * Set Session Configuration
 * @category: Magento
 * @package: Perficient/Customer
 * @copyright: Copyright � 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vijayashanthi<v.murugesan@Perficient.com>
 * @keywords: Module Perficient_Customer
 */

namespace Perficient\Base\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\ResourceModel\Store;
use Magento\Framework\Event\ManagerInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;


/**
 * Class StoreInformation
 */
class StoreInformation implements DataPatchInterface
{
    /**
     * Default store view code
     */
    final const DEFAULT_STORE_VIEW_NAME = 'Wendover Art Group';

    final const SCOPE_ID = 0;

    final const GENERAL_CONTACT = 'trans_email/ident_general/name';
    final const SALES_REPRESENTATIVE = 'trans_email/ident_sales/name';
    final const CUSTOMER_SUPPORT = 'trans_email/ident_support/name';
    final const CUSTOM_EMAIL_1 = 'trans_email/ident_custom1/name';
    final const CUSTOM_EMAIL_2 = 'trans_email/ident_custom2/name';

    final const EMAIL_SENDER_NAME = 'Wendover Art Group';

    private array $configData = [
        self::GENERAL_CONTACT => self::EMAIL_SENDER_NAME,
        self::SALES_REPRESENTATIVE => self::EMAIL_SENDER_NAME,
        self::CUSTOMER_SUPPORT => self::EMAIL_SENDER_NAME,
        self::CUSTOM_EMAIL_1 => self::EMAIL_SENDER_NAME,
        self::CUSTOM_EMAIL_2 => self::EMAIL_SENDER_NAME
    ];

    /**
     * StoreInformation constructor.
     * @param Store $storeResourceModel
     * @param ManagerInterface $eventManager
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param WriterInterface $configWriter
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        private readonly Store $storeResourceModel,
        private readonly ManagerInterface $eventManager,
        private readonly WebsiteRepositoryInterface $websiteRepository,
        protected WriterInterface $configWriter,
        protected ModuleDataSetupInterface $moduleDataSetup
    ) {
    }

    /**
     * @return $this|void
     * @throws \Exception
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $this->updateDefaultStoreViewName();
        $this->updateEmailSenderNames();
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Update name of default store
     *
     * @throws \Exception
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     */
    private function updateDefaultStoreViewName(): void
    {
        $website = $this->websiteRepository->getDefault();
        $store = $website->getDefaultStore();

        //Update Default Store View
        $store->setName(self::DEFAULT_STORE_VIEW_NAME);
		$this->storeResourceModel->save($store);

        //Event
        $this->eventManager->dispatch('store_edit', ['store' => $store]);
    }

    /**
     *
     */
    private function updateEmailSenderNames(): void
    {
        foreach($this->configData as $key=>$value){
            $this->configWriter->save($key, $value, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, self::SCOPE_ID);
        }
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
}
