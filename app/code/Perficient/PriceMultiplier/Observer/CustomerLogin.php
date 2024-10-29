<?php
/**
 * Event Observer to set customer price multiplier, discount type, discount available attribute value in session
 *
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@perficient.com>
 * @keywords: price multiplier custom customer attribute values in session
 */
declare(strict_types=1);

namespace Perficient\PriceMultiplier\Observer;

use Magento\Checkout\Model\Cart;
use Magento\Company\Api\CompanyManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Eav\Model\Config;
use Magento\Customer\Model\Customer;

class CustomerLogin implements ObserverInterface
{
    /**
     * @var string
     */
    final const FREE_FREIGHT = 'qualifies_for_free_freight';
    /**
     * @var string
     */
    final const REQUIRE_INDIVIDUAL_BOXING = 'requires_individ_boxing';
    /**
     * @var string
     */
    final const REQUIRE_INDIVIDUAL_BOXING_ENABLE = 'Yes';
    /**
     * @var string
     */
    final const PRICE_MULTIPLIER = 'price_multiplier';
    /**
     * @var string
     */
    final const DISCOUNT_TYPE = 'discount_type';
    /**
     * @var string
     */
    final const DISCOUNT_AVAILABLE = 'discount_available';
    /**
     * @var string
     */
    final const IS_B2C_CUSTOMER = 'is_b2c_customer';

    final const COMPANY_BRANCH = 'company_branch';

    final const DISCOUNT_TYPE_STANDARD = 'standard';

    /**
     * CustomerLogin constructor.
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param CompanyManagementInterface $companyRepository
     * @param Cart $cart
     * @param Config $config
     */
    public function __construct(
        private readonly Session $customerSession,
        private readonly CustomerRepositoryInterface $customerRepository,
        private readonly CompanyManagementInterface $companyRepository,
        private readonly Cart $cart,
        private readonly Config $config
    ) {

    }

    /**
     * @param Observer $observer
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer): void
    {
        $customer = $observer->getEvent()->getCustomer();
        $customerObj = $this->customerRepository->getById($customer->getId());
        $multiplier = $customerObj->getCustomAttribute(self::PRICE_MULTIPLIER);
        $discountType = $customerObj->getCustomAttribute(self::DISCOUNT_TYPE);
        $discountAvailable = $customerObj->getCustomAttribute(self::DISCOUNT_AVAILABLE);
        $isB2cCustomer = $customerObj->getCustomAttribute(self::IS_B2C_CUSTOMER);
        $freeFreight = $customerObj->getCustomAttribute(self::FREE_FREIGHT);
        $requireIndividualBoxing = $customerObj->getCustomAttribute(self::REQUIRE_INDIVIDUAL_BOXING);
        $companyBranch = $customerObj->getCustomAttribute(self::COMPANY_BRANCH);

        $multiplierValue = 1;
        $discountTypeValue = self::DISCOUNT_TYPE_STANDARD;
        $discountAvailableValue = 0;
        $isB2cCustomerFlag = 0;
        $freeFreightFlag = 0;
        $requireIndividualBoxingFlag = 0;
        $companyBranchValue = 0;

        if (isset($multiplier)) {
            $multiplierValue = $multiplier->getValue();
            if ($multiplierValue == 0.00) {
                $quote = $this->cart->getQuote();
                $quote->removeAllItems()->save();
            }
        }

        if (isset($discountType)) {
            $discountTypeValue = $discountType->getValue();
        }
        if (isset($discountAvailable)) {
            $discountAvailableValue = $discountAvailable->getValue();
        }
        if (isset($isB2cCustomer)) {
            $isB2cCustomerFlag = $isB2cCustomer->getValue();
        }
        if (isset($freeFreight)) {
            $freeFreightValue = $freeFreight->getValue();
            if ($freeFreightValue == 1) {
                $freeFreightFlag = true;
            } else {
                $freeFreightFlag = false;
            }
        }
        if (isset($requireIndividualBoxing)) {
            $requireIndividualBoxingValue = $requireIndividualBoxing->getValue();
            if ($requireIndividualBoxingValue == 1) {
                $requireIndividualBoxingFlag = true;
            } else {
                $requireIndividualBoxingFlag = false;
            }
        }
        if (isset($companyBranch)) {
            $companyBranchValue = $companyBranch->getValue();
        }

        $this->customerSession->setMultiplier($multiplierValue);
        $this->customerSession->setDiscountType($discountTypeValue);
        $this->customerSession->setDiscountAvailable($discountAvailableValue);
        $this->customerSession->setIsBtocCustomer($isB2cCustomerFlag);
        $this->customerSession->setFreeFreight($freeFreightFlag);
        $this->customerSession->setRequireIndividualBoxing($requireIndividualBoxingFlag);
        $this->customerSession->setCompanyBranch($companyBranchValue);
        $company = $this->getCustomerCompany($customer->getId());
        if ($company) {
            $this->customerSession->setDiscountMarkup($company->getDiscountMarkup());
            $this->customerSession->setDiscountApplicationType($company->getDiscountApplicationType());
            $this->customerSession->setDiscountValue($company->getDiscountValue());

            //We need this for GTM tracking but to improve performance instead of reloading added business type here
            $this->customerSession->setBusinessType($company->getBusinessType());
        }
    }

    /**
     * Get customer's company details
     *
     * @param $customerId
     */
    private function getCustomerCompany($customerId): ?object
    {
        return $this->companyRepository->getByCustomerId($customerId);
    }

}
