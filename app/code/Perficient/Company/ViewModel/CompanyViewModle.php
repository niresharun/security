<?php
declare(strict_types=1);

namespace Perficient\Company\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Perficient\Company\Model\Config\Source\BusinessType;
use Perficient\Company\Model\Config\Source\Projects;
use Perficient\Company\Model\Config\Source\NumberOfJobsPerYear;
use Magento\Company\Block\Company\Account\Dashboard\RoleInfo;
use Magento\Framework\Escaper;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Request\Http;
use Perficient\PriceMultiplier\Model\Config\Source\MultiplierOptions;
use Perficient\PriceMultiplier\Model\Config\Source\DiscountOptions;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Company\Api\AuthorizationInterface;
use Perficient\Company\Ui\Component\Listing\Column\CompanyUsersActions;

class CompanyViewModle implements ArgumentInterface
{
    const COMPANY_MASTER_ROLE = "Company Administrator";
    const COMPANY_EMPLOYEE = "Customer Employee";
    const TITLE_EMPLOYEE_LOGINS = "Employee Logins";
    const TITLE_MANAGE_CUSTOMERS = "Manage Customers";
    const AUTH_MULTIPLIER = 'Perficient_PriceMultiplier::multiplier';
    const CUSTOMER_CUSTOMER = "Customer's Customer";

    public function __construct(
        private readonly BusinessType               $businessType,
        private readonly Projects                   $projects,
        private readonly NumberOfJobsPerYear        $numberOfJobsPerYear,
        private readonly RoleInfo                   $roleInfo,
        private readonly Escaper                    $escaper,
        private readonly Session                    $currentCustomerSession,
        private readonly Http                       $httpRequest,
        private readonly MultiplierOptions          $priceMultiplier,
        private readonly DiscountOptions            $discountOptions,
        protected AuthorizationInterface            $authorization
    )
    {

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

    public function isAllowedMultiplier(): bool
    {
        $isb2cCustomer = $this->isB2cCustomer();
        if ($isb2cCustomer) {
            return false;
        }
        return $this->authorization->isAllowed(self::AUTH_MULTIPLIER);
    }

    /**
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCurrentLoggedInUserId()
    {
        return $this->currentCustomerSession->getCustomerData()->getId();
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

    public function getPriceMultiplierValues(): array
    {
        return $this->priceMultiplier->toOptionArray();
    }

    public function isDiscoutAvailable(): mixed
    {
        return $this->currentCustomerSession->getDiscountAvailable();
    }

    public function getDiscountTypeValues(): array
    {
        return $this->discountOptions->toOptionArray();
    }

    public function isB2cCustomer(): int
    {
        return $this->currentCustomerSession->getIsBtocCustomer() ?: 0;
    }
}
