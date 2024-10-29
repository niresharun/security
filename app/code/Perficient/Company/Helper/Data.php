<?php
/**
 * Company Custom Fields.
 * @category: Magento
 * @package: Perficient/Company
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Company
 */
declare(strict_types=1);

namespace Perficient\Company\Helper;

use Magento\Eav\Model\Config;
use Magento\Eav\Model\Entity;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory as AttributeCollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Perficient\Company\Model\Config\Source\BusinessType;
use Perficient\Company\Model\Config\Source\Newsletter;
use Perficient\Company\Model\Config\Source\NumberOfJobsPerYear;
use Perficient\Company\Model\Config\Source\Projects;
use Magento\Company\Block\Company\Account\Dashboard\RoleInfo;
use Magento\Framework\Escaper;
use Magento\Customer\Model\Session;
use Perficient\PriceMultiplier\Model\Config\Source\MultiplierOptions;
use Perficient\PriceMultiplier\Model\Config\Source\DiscountOptions;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Framework\App\Request\Http;
use Perficient\Company\Ui\Component\Listing\Column\CompanyUsersActions;
use Magento\Store\Model\ScopeInterface;
use \Magento\Company\Api\Data\CompanyInterface;
use Perficient\Company\Plugin\DataProviderPlugin;
use Magento\Company\Api\AuthorizationInterface;

/**
 * Class Data
 * @package Perficient\CrmConnector\Helper
 */
class Data extends AbstractHelper
{
    const COMPANY_MASTER_ROLE = "Company Administrator";
    const COMPANY_DEFAULT_USER = "Default User";
    const COMPANY_CUSTOMER_ADMIN = "Customer Admin";
    const COMPANY_EMPLOYEE = "Customer Employee";
    const CUSTOMER_CUSTOMER = "Customer's Customer";
    const TITLE_EMPLOYEE_LOGINS = "Employee Logins";
    const TITLE_MANAGE_CUSTOMERS = "Manage Customers";
    const LABEL_COMPANY_EMPLOYEE = "Company Employee";
    const LABEL_CUSTOMER_CUSTOMER = "Invited Customer";

    const XML_PATH_RESTRICT_CART_CHECKOUT = 'restrictcustomer/cartcheckout/is_enabled';
    const XML_PATH_RESTRICT_REDIRECT_PATH = 'restrictcustomer/cartcheckout/redirect_to';
    const XML_PATH_RESTRICT_LOG_ENABLED = 'restrictcustomer/cartcheckout/is_logger_enabled';

    const BUSINESS_TYPE_RETAILER = "Retailer";
    const BUSINESS_TYPE_RETAILER_INTERIOR_DESIGN = "Retailer + Interior Design";
    /**
     * Healthcare business type value
     */
    const BUSINESS_TYPE_HEALTHCARE = 'Healthcare Office';
    /**
     * price multiplier acl resource
     */
    const AUTH_MULTIPLIER = 'Perficient_PriceMultiplier::multiplier';
    const DEFAULT_VALUE_FOR_LOCATION_ATTRIBUTE = "Residential";
    const DEFAULT_VALUE_FOR_LOADING_DOCK = "No";
    const DEFAULT_VALUE_FOR_APPOINTMENT = "Yes";
    const LOCATION_ATTRIBUTE = "location";
    const LOADING_DOCK_ATTRIBUTE = "loading_dock_available";
    const APPOINTMENT_ATTRIBUTE = "delivery_appointment";
    const ORDER_NOTES_ATTRIBUTE = "order_shipping_notes";

    private array $requiredDefaultFields = [
        CompanyInterface::NAME,
        CompanyInterface::COMPANY_EMAIL,
        CompanyInterface::STREET,
        CompanyInterface::CITY,
        CompanyInterface::POSTCODE,
        CompanyInterface::TELEPHONE,
        CompanyInterface::COUNTRY_ID,
        CompanyInterface::REGION_ID
    ];

    private array $requiredCustomFields = [
        DataProviderPlugin::RESALE_CERTIFICATE_NUMBER,
        DataProviderPlugin::BUSINESS_TYPE
    ];

    /**
     * Data constructor.
     * @param Context $context
     * @param Config $eavConfig
     * @param Entity $entity
     * @param RoleInfo $roleInfo
     * @param Escaper $escaper
     * @param MultiplierOptions $priceMultiplier
     * @param DiscountOptions $discountOptions
     * @param Boolean $boolean
     * @param Session $currentCustomerSession
     * @param Http $httpRequest
     */
    public function __construct(
        private readonly Context                    $context,
        private readonly Newsletter                 $newsLatter,
        private readonly BusinessType               $businessType,
        private readonly Projects                   $projects,
        private readonly NumberOfJobsPerYear        $numberOfJobsPerYear,
        private readonly AttributeCollectionFactory $attributeFactory,
        private readonly Config                     $eavConfig,
        protected Entity                            $entity,
        private readonly RoleInfo                   $roleInfo,
        private readonly Escaper                    $escaper,
        private readonly MultiplierOptions          $priceMultiplier,
        private readonly DiscountOptions            $discountOptions,
        private readonly Boolean                    $boolean,
        private readonly Session                    $currentCustomerSession,
        private readonly Http                       $httpRequest,
        protected AuthorizationInterface            $authorization
    )
    {
        parent::__construct($context);
    }

    public function getNewsLetterOptions(): array
    {
        return $this->newsLatter->toOptionArray();
    }

    public function isDbaAvailable(): array
    {
        return $this->newsLatter->toOptionArray();
    }

    public function getBusinessType(): array
    {
        return $this->businessType->toOptionArray();
    }

    public function getProjects(): array
    {
        return $this->projects->toOptionArray();
    }

    public function getNumberOfJobsPerYear(): array
    {
        return $this->numberOfJobsPerYear->toOptionArray();
    }

    public function getCurrentUserRole(): mixed
    {
        $currentUserRole = $this->roleInfo->getCustomerRoles();
        $currentUserRole = $this->escaper->escapeHtml($currentUserRole);

        return $currentUserRole[0] ?? '';
    }

    public function getPriceMultiplierValues(): array
    {
        return $this->priceMultiplier->toOptionArray();
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCurrentLoggedInUserId(): ?int
    {
        return $currentLoggedInUserId = $this->currentCustomerSession->getCustomerData()->getId();
    }

    public function getDiscountTypeValues(): array
    {
        return $this->discountOptions->toOptionArray();
    }

    public function getDiscountValues(): array
    {
        return $this->boolean->toOptionArray();
    }

    public function isDiscoutAvailable(): mixed
    {
        return $this->currentCustomerSession->getDiscountAvailable();
    }

    public function getMultiplierValue(): mixed
    {
        return $this->currentCustomerSession->getMultiplier();
    }

    public function checkLink(): string
    {
        $resourceTypeParam = $this->httpRequest->getParam('resource_type');
        if (isset($resourceTypeParam)
            && !empty($resourceTypeParam)
            && $resourceTypeParam == CompanyUsersActions::RESOURCE_TYPE . '/') {
            return CompanyUsersActions::COMPANY_EMPLOYEE;
        }
        return CompanyUsersActions::CUSTOMER_CUSTOMER;
    }

    /**
     * Check configuration if is restricted add to cart, cart, checkout
     */
    public function isRestrictCartAndCheckout(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            Data::XML_PATH_RESTRICT_CART_CHECKOUT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check configuration to get restricted pages redirect to path
     */
    public function getRestrictedPageRedirectPath(): string
    {
        return (string)$this->scopeConfig->getValue(
            Data::XML_PATH_RESTRICT_REDIRECT_PATH,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param CompanyInterface $company
     * @return bool|void
     */
    public function validateCompanyData(CompanyInterface $company)
    {
        if ($company) {
            $extAttrs = $company->getExtensionAttributes();
            $companyName = $company->getCompanyName();
            if (empty($companyName)) {
                return false;
            }

            $companyEmail = $company->getCompanyEmail();
            if (empty($companyEmail)) {
                return false;
            }

            $street = $company->getStreet();
            if (empty($street)) {
                return false;
            }

            $city = $company->getCity();
            if (empty($city)) {
                return false;
            }

            $postcode = $company->getPostcode();
            if (empty($postcode)) {
                return false;
            }

            $telephone = $company->getTelephone();
            if (empty($telephone)) {
                return false;
            }

            $countryId = $company->getCountryId();
            if (empty($countryId)) {
                return false;
            }

            $businessType = $extAttrs->getBusinessType();
            if (empty($businessType)) {
                return false;
            } else {
                /**
                 * As business type - healthcare do not have no of stores and type of projects
                 * fields so we are skipping check for those fields.
                 */
                if ($businessType == self::BUSINESS_TYPE_HEALTHCARE) {
                    return true;
                }

                $typeOfProjects = $extAttrs->getTypeOfProjects();
                $numberOfStores = $extAttrs->getNoOfStores();

                if ($businessType == self::BUSINESS_TYPE_RETAILER) {
                    if (empty($numberOfStores)) {
                        return false;
                    }
                } elseif ($businessType == self::BUSINESS_TYPE_RETAILER_INTERIOR_DESIGN) {
                    if (empty($numberOfStores) || empty($typeOfProjects)) {
                        return false;
                    }
                } elseif (empty($typeOfProjects)) {
                    return false;
                }
            }

            return true;
        }
    }

    /**
     * Check if logger is enabled
     */
    public function isRestrictLoggerEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            Data::XML_PATH_RESTRICT_LOG_ENABLED,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * check current customer role
     */
    public function isCustomerCustomer(): bool
    {
        $userRole = $this->getCurrentUserRole();
        $currentUserRole = $userRole ? htmlspecialchars_decode((string)$userRole, ENT_QUOTES) : '';
        return (strcmp($currentUserRole, self::CUSTOMER_CUSTOMER) == 0) ? true : false;
    }

    public function isB2cCustomer(): int
    {
        return $this->currentCustomerSession->getIsBtocCustomer() ?: 0;
    }

    public function isAllowedMultiplier(): bool
    {
        $isb2cCustomer = $this->isB2cCustomer();
        if ($isb2cCustomer) {
            return false;
        }
        return $this->authorization->isAllowed(self::AUTH_MULTIPLIER);
    }

    /** To get attribute option id
     * @param $entityType
     * @param $attributeCode
     * @param $optionLabel
     * @throws LocalizedException
     */
    public function getOptionIdByLabel($entityType, $attributeCode, $optionLabel): ?string
    {
        $optionId = null;

        $attribute = $this->eavConfig->getAttribute($entityType, $attributeCode);
        if ($attribute && $attribute->usesSource()) {
            foreach ($attribute->getOptions() as $option) {
                $label = $option->getLabel();
                if (strtolower((string)$optionLabel) == strtolower((string)$label)) {
                    $optionId = $option->getValue();
                    break;
                }
            }
        }

        return $optionId;
    }
}
