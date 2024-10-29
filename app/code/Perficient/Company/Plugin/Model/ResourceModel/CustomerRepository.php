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

namespace Perficient\Company\Plugin\Model\ResourceModel;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository as ParentCustomerRepository;
use Magento\Framework\App\ResourceConnection;
use Magento\Company\Api\CompanyManagementInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\AddressRepositoryInterface;
use Perficient\Company\Helper\Data as CompanyHelper;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Request\Http as Request;

/**
 * Class CustomerRepository
 * @package Perficient\Company\Plugin\Model\ResourceModel
 */
class CustomerRepository
{
    const CUSTOMER_EMPLOYEE = "Customer Employee";

    /**
     * CustomerRepository constructor.
     * @param ResourceConnection $resourceConnection
     * @param CompanyManagementInterface $companyManagement
     * @param AddressInterfaceFactory $dataAddressFactory
     * @param AddressRepositoryInterface $addressRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly ResourceConnection         $resourceConnection,
        private readonly CompanyManagementInterface $companyManagement,
        private readonly AddressInterfaceFactory    $dataAddressFactory,
        private readonly AddressRepositoryInterface $addressRepository,
        protected LoggerInterface                   $logger,
        private readonly CompanyHelper              $companyHelper,
        protected Request                           $request
    )
    {
    }

    /**
     * @param CustomerInterface $result
     */
    function afterSave(ParentCustomerRepository $subject, CustomerInterface $result): CustomerInterface
    {
        $processedUserId = $result->getId();
        try {
            if ((is_countable($result->getAddresses()) ? count($result->getAddresses()) : 0) == 0 || empty($result->getDefaultBilling())
                || empty($result->getDefaultShipping())) {
                $company = $this->companyManagement->getByCustomerId($result->getId());
                if ($company) {
                    $address = $this->dataAddressFactory->create();
                    $address->setCompany($company->getCompanyName());
                    $address->setCity($company->getCity());
                    $address->setCountryId($company->getCountryId());
                    $address->setPostcode($company->getPostcode());
                    $address->setRegionId($company->getRegionId());
                    $address->setStreet($company->getStreet());
                    $address->setTelephone($company->getTelephone());
                    $address->setFirstname($result->getFirstname());
                    $address->setLastname($result->getLastname());
                    if (empty($result->getDefaultBilling())) {
                        $address->setIsDefaultBilling('1');
                    }
                    if (empty($result->getDefaultShipping())) {
                        $address->setIsDefaultShipping('1');
                    }

                    $location = $this->request->getPost('location');
                    $deliveryAppointment = $this->request->getPost('delivery_appointment');
                    $loadingDock = $this->request->getPost('loading_dock_available');
                    if (!empty($location)) {
                        $address->setCustomAttribute('location', $location);
                    } else {
                        $locationOptionId = $this->companyHelper->getOptionIdByLabel(
                            'customer_address',
                            CompanyHelper::LOCATION_ATTRIBUTE,
                            CompanyHelper::DEFAULT_VALUE_FOR_LOCATION_ATTRIBUTE
                        );
                        $address->setCustomAttribute('location', $locationOptionId);
                    }
                    if (!empty($deliveryAppointment)) {
                        $address->setCustomAttribute('delivery_appointment', $deliveryAppointment);
                    } else {
                        $appointmentOptionId = $this->companyHelper->getOptionIdByLabel(
                            'customer_address',
                            CompanyHelper::APPOINTMENT_ATTRIBUTE,
                            CompanyHelper::DEFAULT_VALUE_FOR_APPOINTMENT
                        );
                        $address->setCustomAttribute('delivery_appointment', $appointmentOptionId);
                    }
                    if (!empty($loadingDock)) {
                        $address->setCustomAttribute('loading_dock_available', $loadingDock);
                    } else {
                        $loadingOptionId = $this->companyHelper->getOptionIdByLabel(
                            'customer_address',
                            CompanyHelper::LOADING_DOCK_ATTRIBUTE,
                            CompanyHelper::DEFAULT_VALUE_FOR_LOADING_DOCK
                        );
                        $address->setCustomAttribute('loading_dock_available', $loadingOptionId);
                    }

                    $address->setCustomerId($result->getId());

                    $addressArr = [$address];
                    $result->setAddresses($addressArr);
                    $this->addressRepository->save($address);
                }
            }

            $connection = $this->resourceConnection->getConnection();
            $checkIfEmployee = "SELECT role_name,company_id FROM `company_user_roles`
            INNER JOIN  `company_roles` ON company_roles.role_id = company_user_roles.role_id
            WHERE user_id = " . $processedUserId;
            $checkIfEmployeeData = $connection->fetchAll($checkIfEmployee);
            if (isset($checkIfEmployeeData[0]) &&
                isset($checkIfEmployeeData[0]['role_name'])
                && $checkIfEmployeeData[0]['role_name'] == self::CUSTOMER_EMPLOYEE) {
                $getCustomerGroup = "SELECT customer_group.customer_group_id FROM `company`
            INNER JOIN `catalogrule` ON catalogrule.name = company.discount_rate
            INNER JOIN customer_group ON customer_group.customer_group_code = company.discount_rate
            WHERE entity_id = " . $checkIfEmployeeData[0]['company_id'];
                $getCustomerGroupData = $connection->fetchAll($getCustomerGroup);
                if (isset($getCustomerGroupData[0]) &&
                    isset($getCustomerGroupData[0]['customer_group_id'])
                ) {
                    $table = 'customer_entity';
                    $connection = $this->resourceConnection->getConnection();
                    $query = "UPDATE " . $table . " SET  group_id =" . $getCustomerGroupData[0]['customer_group_id'] . " WHERE entity_id =" . $processedUserId;
                    $connection->query($query);
                }
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        return $result;
    }
}
