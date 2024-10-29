<?php

namespace Wendover\Company\Model\Email;

use Magento\Company\Api\CompanyRepositoryInterface;
use Magento\Company\Model\Config\EmailTemplate as EmailTemplateConfig;
use Magento\Company\Model\Email\CustomerData;
use Magento\Company\Model\Email\Transporter;
use Magento\Customer\Api\CustomerNameGenerationInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Company\Model\Email\Sender;
use Magento\Customer\Api\Data\CustomerInterface;

class SenderOverride extends Sender
{

    public function __construct(
        StoreManagerInterface           $storeManager,
        ScopeConfigInterface            $scopeConfig,
        Transporter                     $transporter,
        CustomerNameGenerationInterface $customerViewHelper,
        CustomerData                    $customerData,
        EmailTemplateConfig             $emailTemplateConfig,
        CompanyRepositoryInterface      $companyRepository
    ) {
        parent::__construct(
            $storeManager,
            $scopeConfig,
            $transporter,
            $customerViewHelper,
            $customerData,
            $emailTemplateConfig,
            $companyRepository
        );
    }

    public function sendSalesRepresentativeNotificationEmail($companyId, $salesRepresentativeId = 0)
    {
        return $this;
    }
}
