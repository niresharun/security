<?php
/**
 * Log Company Change Information
 * @category: Magento
 * @package: Perficient/Reports
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Vikramraj Sahu<Vikramraj.Sahu@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Reports
 */

declare(strict_types=1);

namespace Perficient\Reports\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Company\Model\Company;
use Magento\Framework\App\Helper\Context;
use Magento\Checkout\Model\Session;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Directory\Model\ResourceModel\Region as RegionResourceModel;
use Magento\Directory\Model\Region;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Company\Api\CompanyRepositoryInterface;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Perficient\Reports\Model\ResourceModel\PerficientLoggingEvent as PerficientLoggingEventResourceModel;
use Perficient\Reports\Model\PerficientLoggingEvent as PerficientLoggingEventModel;
use Perficient\Reports\Model\ResourceModel\PerficientLoggingEventChanges as PerficientLoggingEventChangesResourceModel;
use Perficient\Reports\Model\PerficientLoggingEventChanges as PerficientLoggingEventChangesModel;
use Perficient\Reports\Model\ResourceModel\PerficientLoggingEvent\Collection as PerficientLoggingEventCollection;
use Perficient\Reports\Model\ResourceModel\PerficientLoggingEventChanges\Collection as PerficientLoggingEventChangesCollection;

/**
 * Company Change Log Helper
 */
class SaveCompany extends AbstractHelper
{
    /**
     * SaveCompany constructor.
     * @param Context $context
     * @param SessionManagerInterface $coreSession
     * @param RequestInterface $request
     * @param CompanyRepositoryInterface $companyRepository
     * @param Region $region
     * @param RemoteAddress $remoteAddress
     * @param ManagerInterface $messageManager
     * @param Session $checkoutSession
     * @param State $state
     */
    public function __construct(
        Context $context,
        private readonly SessionManagerInterface $coreSession,
        private readonly RequestInterface $request,
        private readonly CompanyRepositoryInterface $companyRepository,
        private readonly PerficientLoggingEventResourceModel $perficientLoggingEventResourceModel,
        private readonly PerficientLoggingEventModel $perficientLoggingEventModel,
        private readonly PerficientLoggingEventChangesResourceModel $perficientLoggingEventChangesResourceModel,
        private readonly PerficientLoggingEventChangesModel $perficientLoggingEventChangesModel,
        private readonly AuthSession $authSession,
        private readonly RegionResourceModel $regionResourceModel,
        private readonly Region $region,
        private readonly RemoteAddress $remoteAddress,
        private readonly ManagerInterface $messageManager,
        Session $checkoutSession,
        private readonly State $state,
        private readonly PerficientLoggingEventCollection $perficientLoggingEventCollection,
        private readonly PerficientLoggingEventChangesCollection $perficientLoggingEventChangesCollection,
        private readonly CustomerSession $customerSession,
        array $data = []
    ) {
        parent::__construct($context);
    }

    /**
     * @param $companyId
     */
    public function logCompanyChangeInfo($companyId) {

        if(!$companyId) {
            return;
        }

        $company = $this->companyRepository->get((int)$companyId);

        $changedData = $this->getChangedCompanyData($company);
        if(empty($changedData)) {
            return;
        }

        $this->perficientLoggingEventResourceModel->beginTransaction();

        $logId = $this->saveLogEvent($company);
        if(!$logId) {
            $this->perficientLoggingEventResourceModel->rollBack();
            return;
        }

        $eventChangeId = $this->saveLogEventChanges($logId,$companyId, $changedData);
        if(!$eventChangeId) {
            $this->perficientLoggingEventResourceModel->rollBack();
            return;
        }

        $this->coreSession->setCompanyLogId($logId);
        $this->perficientLoggingEventResourceModel->commit();

    }

    /**
     * @return \Magento\User\Model\User|null
     */
    private function getCurrentAdminUser() {
        return $this->authSession->getUser();
    }

    /**
     * @param Company $company
     * @return mixed
     */
    private function saveLogEvent($company) {

        $username = null;
        $userId = null;
        if ($this->getArea() == 'adminhtml' && $this->authSession->isLoggedIn()) {
            $userId = $this->getCurrentAdminUser()->getId();
            $username = $this->getCurrentAdminUser()->getUserName();
        } else if($this->getArea() == 'frontend' && $this->customerSession->isLoggedIn()) {
            $userId = $this->customerSession->getCustomer()->getId();
            $username = $this->customerSession->getCustomer()->getName();
        }

        $ipAddress   = $this->remoteAddress->getRemoteAddress();
        $ipForwarded = $this->request->getServer('HTTP_X_FORWARDED_FOR');
        if (empty($ipForwarded) || null == $ipForwarded) {
            $ipForwarded = $ipAddress;
        }
        $perficientLoggingEventModel = $this->perficientLoggingEventModel->addData([
            "ip" => ip2long($ipAddress),
            "x_forwarded_ip" => ip2long($ipForwarded),
            "time" => time(),
            "entity_model" => 'Magento\Company\Model\Company',
            "entity_id" => $company->getId(),
            "entity_name" => $company->getCompanyName(),
            "status" => 'pending',
            "user" => $username,
            "user_id" => $userId,
            "event_code" => 'company_save',
            "fullaction" => $this->getArea() . '_company_save'
        ]);
        $this->perficientLoggingEventResourceModel->save($perficientLoggingEventModel);
        return $perficientLoggingEventModel->getId();
    }

    /**
     * @param $logId
     * @param $companyId
     * @param $changedData
     * @return mixed
     */
    private function saveLogEventChanges($logId, $companyId, $changedData) {

        $perficientLoggingEventChangesModel = $this->perficientLoggingEventChangesModel->addData([
            "source_name" => 'Magento\Company\Model\Company',
            "event_id" => $logId,
            "source_id" => $companyId,
            "original_data" => json_encode($changedData['originalData'], JSON_THROW_ON_ERROR),
            "result_data" => json_encode($changedData['updatedData'], JSON_THROW_ON_ERROR),
        ]);
        $this->perficientLoggingEventChangesResourceModel->save($perficientLoggingEventChangesModel);
        return $perficientLoggingEventChangesModel->getId();
    }

    /**
     * @param $company
     * @return array
     */
    private function getChangedCompanyData($company) {

        if(!$company) {
            return [];
        }

        if( 'frontend' == $this->getArea()) {
            $companyName = $this->request->getParam('company_name') ?: '';

            $companyData = $this->request->getParam('company') ?: '';
            $dbaName = $companyData['dba_name'] ?? '';
            $isDba = $companyData['is_dba'] ?? '';
            $resaleCertificateNumber = $companyData['resale_certificate_number'] ?? '';
            $businessType = $companyData['business_type'] ?? '';

            // Address
            $street =  $this->request->getParam('street') ?: '';
            $originalAddress = $street[0] ?? '';
            $originalAddress .= $street[1] ?? '';

            $city = $this->request->getParam('city') ?: '';
            $countryId = $this->request->getParam('country_id') ?: '';
            $regionId = $this->request->getParam('region_id') ?: '';
            $region = $this->request->getParam('region') ?: '';
            $postcode = $this->request->getParam('postcode') ?: '';
            $telephone = $this->request->getParam('telephone') ?: '';

            $address = [
                'street' => $street,
                'city' => $city,
                'country_id' => $countryId,
                'region_id' => $regionId,
                'region' => $region,
                'postcode' => $postcode,
                'telephone' => $telephone
            ];

        } else if('adminhtml' == $this->getArea() ){
            $general = $this->request->getParam('general') ?: null;
            $information = $this->request->getParam('information') ?: null;
            $address = $this->request->getParam('address') ?: null;

            $companyName = $general['company_name'] ?? '';
            $dbaName = $information['dba_name'] ?? '';
            $isDba = $information['is_dba'] ?? '';
            $resaleCertificateNumber = $information['resale_certificate_number'] ?? '';
            $businessType = $information['business_type'] ?? '';

            // Address
            $originalAddress = $address['street'][0] ?? '';
            $originalAddress .= $address['street'][1] ?? '';

            $city = $address['city'] ?? '';
            $countryId = $address['country_id'] ?? '';
            $regionId = $address['region_id'] ?? '';
            $region = $address['region'] ?? '';
            $postcode = $address['postcode'] ?? '';
            $telephone = $address['telephone'] ?? '';
        } else {
            return [];
        }

        $updatedAddress = $company->getStreet()[0] ?? '';
        $updatedAddress .= $company->getStreet()[1] ?? '';

        $originalData = [];
        $updatedData = [];

        /**
         * @param Company $company
         * @return array
         */
        $originalCompanyBillingAddress = function($company) {

            if($company->getRegionId()) {
                $this->regionResourceModel->load($this->region, $company->getRegionId());
                $regionName = !empty($this->region) ? $this->region->getName() : '';
            } else {
                $regionName = $company->getRegion();
            }

            return [
                'street_address' => $company->getStreet(),
                'city' => $company->getCity(),
                'country' => $company->getCountryId(),
                'region' => $regionName,
                'postcode' => $company->getPostcode(),
                'telephone' => $company->getTelephone()
            ];
        };

        /**
         * @param array $address
         * @return array
         */
        $updatedCompanyBillingAddress = function($address) {

            if($address['region_id']) {
                $this->regionResourceModel->load($this->region, $address['region_id']);
                $regionName = !empty($this->region) ? $this->region->getName() : '';
            } else {
                $regionName = $address['region'];
            }

            return [
                'street_address' => $address['street'],
                'city' => $address['city'],
                'country' => $address['country_id'],
                'region' =>  $regionName,
                'postcode' => $address['postcode'],
                'telephone' => $address['telephone']
            ];
        };

        if(0 !== strcmp((string) $companyName, ($company->getCompanyName() !== null) ? $company->getCompanyName():'')) {
            $originalData['company_name'] =  $company->getCompanyName();
            $updatedData['company_name'] =  $companyName;
        }
        if(0 !== strcmp((string) $dbaName, ($company->getExtensionAttributes()->getDbaName() !== null) ? $company->getExtensionAttributes()->getDbaName():'')) {
            $originalData['dba_name'] = $company->getExtensionAttributes()->getDbaName();
            $updatedData['dba_name'] = $dbaName;
        }
        if(0 !== strcmp((string) $isDba, ($company->getExtensionAttributes()->getIsDba() !== null)?$company->getExtensionAttributes()->getIsDba():'')) {
            $originalData['dba_name'] = $company->getExtensionAttributes()->getDbaName();
            $updatedData['dba_name'] = ($isDba == 'no')?'':$dbaName;
        }
        if(0 !== strcmp((string) $resaleCertificateNumber, ($company->getExtensionAttributes()->getResaleCertificateNumber() !== null)?$company->getExtensionAttributes()->getResaleCertificateNumber():'')) {
            $originalData['resale_certificate_number'] = $company->getExtensionAttributes()->getResaleCertificateNumber();
            $updatedData['resale_certificate_number'] = $resaleCertificateNumber;
        }
        if(0 !== strcmp((string) $businessType, ($company->getExtensionAttributes()->getBusinessType() !== null)?$company->getExtensionAttributes()->getBusinessType():'')) {
            $originalData['business_type'] = $company->getExtensionAttributes()->getBusinessType();
            $updatedData['business_type'] = $businessType;
        }
        if( 0 !== strcmp($originalAddress,$updatedAddress)) {
            $originalData['billing_address'] = $originalCompanyBillingAddress($company);
            $updatedData['billing_address'] = $updatedCompanyBillingAddress($address);
        }
        if(0 !== strcmp((string) $city, ($company->getCity() !== null)?$company->getCity():'')) {
            $originalData['billing_address'] = $originalCompanyBillingAddress($company);
            $updatedData['billing_address'] = $updatedCompanyBillingAddress($address);
        }
        if(0 !== strcmp((string) $countryId, ($company->getCountryId() !== null)?$company->getCountryId():'')) {
            $originalData['billing_address'] = $originalCompanyBillingAddress($company);
            $updatedData['billing_address'] = $updatedCompanyBillingAddress($address);
        }
        if(0 !== strcmp((string) $regionId, ($company->getRegionId() !== null)?$company->getRegionId():'')) {
            $originalData['billing_address'] = $originalCompanyBillingAddress($company);
            $updatedData['billing_address'] = $updatedCompanyBillingAddress($address);
        }
        if(0 !== strcmp((string) $region, ($company->getRegion() !== null)?$company->getRegion():'')) {
            $originalData['billing_address'] = $originalCompanyBillingAddress($company);
            $updatedData['billing_address'] = $updatedCompanyBillingAddress($address);
        }
        if(0 !== strcmp((string) $postcode, ($company->getPostcode() !== null)?$company->getPostcode():'')) {
            $originalData['billing_address'] = $originalCompanyBillingAddress($company);
            $updatedData['billing_address'] = $updatedCompanyBillingAddress($address);
        }
        if(0 !== strcmp((string) $telephone, ($company->getTelephone() !== null)?$company->getTelephone():'')) {
            $originalData['billing_address'] = $originalCompanyBillingAddress($company);
            $updatedData['billing_address'] = $updatedCompanyBillingAddress($address);
        }

        if(empty($originalData) || empty($updatedData)) {
            return [];
        }
        return ['originalData' => $originalData, 'updatedData' => $updatedData];
    }

    /**
     * @return string
     */
    private function getArea()
    {
        return $this->state->getAreaCode();
    }

    /**
     * Verify and change the status of log for any error
     */
    public function commitCompanySaveLog() {
        $logId = $this->coreSession->getCompanyLogId();
        if(!$logId) {
            $this->coreSession->setCompanyLogId(null);
            return;
        }

        $errors = $this->messageManager->getMessages()->getErrors();
        if(empty($errors)) {
            $this->perficientLoggingEventResourceModel->load($this->perficientLoggingEventModel, $logId);
            $this->perficientLoggingEventModel->setStatus('success');
            $this->perficientLoggingEventResourceModel->save($this->perficientLoggingEventModel);
            $this->coreSession->setCompanyLogId(null);
            return;
        }

        $this->perficientLoggingEventResourceModel->beginTransaction();
        $this->perficientLoggingEventModel->setId($logId);
        $this->perficientLoggingEventResourceModel->delete($this->perficientLoggingEventModel);

        $this->perficientLoggingEventChangesCollection->addFilter('event_id', $logId);
        $data = $this->perficientLoggingEventChangesCollection->load()->getFirstItem();

        /** @var PerficientLoggingEventModel $data */
        $eventChangeId = $data->getId();

        if(!$eventChangeId) {
            $this->coreSession->setCompanyLogId(null);
            $this->perficientLoggingEventResourceModel->rollBack();
            return;
        }

        $this->perficientLoggingEventChangesModel->setId($eventChangeId);
        $this->perficientLoggingEventChangesResourceModel->delete($this->perficientLoggingEventChangesModel);
        $this->coreSession->setCompanyLogId(null);

        $this->perficientLoggingEventResourceModel->commit();
    }
}
