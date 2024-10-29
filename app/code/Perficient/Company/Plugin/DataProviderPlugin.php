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

namespace Perficient\Company\Plugin;

use Magento\Company\Api\Data\CompanyInterface;
use Magento\Company\Model\Company\DataProvider;

/**
 * Class DataProviderPlugin
 * @package Perficient\Company\Plugin\Company
 */
class DataProviderPlugin
{
    const NEWSLETTER = 'newsletter';
    const IS_DBA = 'is_dba';
    const FIRST_NAME = 'first_name';
    const LAST_NAME = 'last_name';
    const DBA_NAME = 'dba_name';
    const RESALE_CERTIFICATE_NUMBER = 'resale_certificate_number';
    const WEBSITE_ADDRESS = 'website_address';
    const SOCIAL_MEDIA_SITE = 'social_media_site';
    const BUSINESS_TYPE = 'business_type';
    const NO_OF_STORES = 'no_of_stores';
    const SQ_FT_PER_STORE = 'sq_ft_per_store';
    const TYPE_OF_PROJECTS = 'type_of_projects';
    const NO_OF_JOBS_PER_YEAR = 'no_of_jobs_per_year';
    const DISCOUNT_RATE = 'discount_rate';
    const MARKETING_POSITION = 'mark_pos';
    const DESIGNER_TYPE = 'designer_type';
    const DES_COMM = 'des_comm';
    const NO_OF_DESIGNERS = 'no_of_designers';
    const PERCENTAGE_OF_DESIGN = 'percent_of_design';
    const ANNUAL_REVENUE = 'annual_revenue';
    const DISCOUNT_MARKUP = 'discount_markup';
    const DISCOUNT_APPLICATION_TYPE = 'discount_application_type';
    const DISCOUNT_VALUE = 'discount_value';
    const SYSPRO_CUSTOMER_ID = 'syspro_customer_id';

    /**
     * Get company information data.
     *
     * @param DataProvider $subject
     * @param CompanyInterface $company
     */
    public function afterGetInformationData(
        DataProvider     $subject,
        array            $result,
        CompanyInterface $company
    ): array
    {
        if (!$subject) {
            return $result;
        }
        $informationData = [];
        $informationData[self::NEWSLETTER] = $company->getNewsletter();
        $informationData[self::IS_DBA] = $company->getIsDba();
        $informationData[self::FIRST_NAME] = $company->getFirstName();
        $informationData[self::LAST_NAME] = $company->getLastName();
        $informationData[self::DBA_NAME] = $company->getDbaName();
        $informationData[self::RESALE_CERTIFICATE_NUMBER] = $company->getResaleCertificateNumber();
        $informationData[self::WEBSITE_ADDRESS] = $company->getWebsiteAddress();
        $informationData[self::SOCIAL_MEDIA_SITE] = $company->getSocialMediaSite();
        $informationData[self::BUSINESS_TYPE] = $company->getBusinessType();
        $informationData[self::NO_OF_STORES] = $company->getNoOfStores();
        $informationData[self::SQ_FT_PER_STORE] = $company->getSqFtPerStore();
        $informationData[self::TYPE_OF_PROJECTS] = $company->getTypeOfProjects();
        $informationData[self::NO_OF_JOBS_PER_YEAR] = $company->getNoOfJobsPerYear();
        $informationData[self::DISCOUNT_RATE] = $company->getDiscountRate();
        $informationData[self::MARKETING_POSITION] = $company->getMarkPos();
        $informationData[self::DESIGNER_TYPE] = $company->getDesignerType();
        $informationData[self::DES_COMM] = $company->getDesComm();
        $informationData[self::NO_OF_DESIGNERS] = $company->getNoOfDesigners();
        $informationData[self::PERCENTAGE_OF_DESIGN] = $company->getPercentOfDesign();
        $informationData[self::ANNUAL_REVENUE] = $company->getAnnualRevenue();
        $informationData[self::DISCOUNT_MARKUP] = $company->getDiscountMarkup();
        $informationData[self::DISCOUNT_APPLICATION_TYPE] = $company->getDiscountApplicationType();
        $informationData[self::DISCOUNT_VALUE] = $company->getDiscountValue();
        $informationData[self::SYSPRO_CUSTOMER_ID] = $company->getSysproCustomerId();
        return array_merge($result, $informationData);
    }
}
