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

namespace Perficient\Company\Controller\Profile;

use Perficient\Rabbitmq\Model\CompanyUpdateMagentoToErp;
use Magento\Company\Controller\Profile\EditPost as CoreEditPost;

/**
 * Controller for saving company profile.
 */
class EditPost extends CoreEditPost
{
    const ATTRIBUTE_STATUS_INACTIVE = 'no';

    /**
     * EditPost constructor.
     * @param CompanyUpdateMagentoToErp $companyUpdateMagentoToErp
     */
    public function __construct(
        \Magento\Framework\App\Action\Context                            $context,
        \Magento\Company\Model\CompanyContext                            $companyContext,
        \Psr\Log\LoggerInterface                                         $logger,
        private readonly \Magento\Company\Api\CompanyManagementInterface $companyManagement,
        private readonly \Magento\Framework\Data\Form\FormKey\Validator  $formKeyValidator,
        private readonly \Magento\Company\Model\CompanyProfile           $companyProfile,
        private readonly \Magento\Company\Api\CompanyRepositoryInterface $companyRepository,
        private readonly CompanyUpdateMagentoToErp                       $companyUpdateMagentoToErp
    )
    {
        parent::__construct($context, $companyContext, $logger, $companyManagement, $formKeyValidator, $companyProfile, $companyRepository);
    }

    /**
     * Edit company profile form.
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $request = $this->getRequest();
        $resultRedirect = $this->resultRedirectFactory->create()->setPath('*/profile/');
        if ($request->isPost()) {
            if (!$this->formKeyValidator->validate($request)) {
                return $resultRedirect;
            }
            try {
                $customerId = $this->companyContext->getCustomerId();
                if ($customerId) {
                    $company = $this->companyManagement->getByCustomerId($customerId);
                    if ($company && $company->getId()) {
                        $postData = $request->getParams();
                        $this->addCustomData($postData['company'], $company);
                        $this->companyProfile->populate($company, $postData);
                        $this->companyRepository->save($company);

                        /**
                         * Send the company information from Magento to ERP.
                         */
                        $this->companyUpdateMagentoToErp->updateCompanyDataFromMagentoToERP($company);

                        $this->messageManager->addSuccess(
                            __('The changes you made on the company profile have been successfully saved.')
                        );
                        return $resultRedirect;
                    }
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError(__('You must fill in all required fields before you can continue.'));
                $this->logger->critical($e);
                return $resultRedirect->setPath('*/profile/edit');
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('An error occurred on the server. Your changes have not been saved.')
                );
                $this->logger->critical($e);
                return $resultRedirect->setPath('*/profile/edit');
            }
        }
        return $resultRedirect;
    }

    /**
     * @param $postData
     * @param $company
     */
    private function addCustomData($postData, $company)
    {
        $customFields = ['newsletter', 'is_dba', 'first_name',
            'last_name', 'dba_name', 'resale_certificate_number',
            'website_address', 'social_media_site', 'business_type', 'no_of_stores', 'sq_ft_per_store',
            'type_of_projects', 'no_of_jobs_per_year'];
        foreach ($customFields as $attribute) {
            if (array_key_exists($attribute, $postData)) {
                $value = $postData[$attribute];
                if (is_array($value)) {
                    $value = implode(',', $value);
                }
                $company->setData($attribute, $value);
            } else {
                $company->setData($attribute, self::ATTRIBUTE_STATUS_INACTIVE);

            }
        }
        return $company;
    }
}
