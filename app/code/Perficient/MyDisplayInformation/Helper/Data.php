<?php
/**
 * This module is used by employee who can add/update his personal information which needs to display his customers
 * @category: Magento
 * @package: Perficient/MyDisplayInformation
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyDisplayInformation
 */
declare(strict_types=1);

namespace Perficient\MyDisplayInformation\Helper;

use Magento\Company\Block\Company\Account\Dashboard\RoleInfo;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Escaper;
use Magento\Directory\Model\CountryFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Perficient\Company\Helper\Data as CompanyHelper;
use Perficient\MyDisplayInformation\Model\ResourceModel\MyDisplayInformation\CollectionFactory;


/**
 * Class Data
 * @package Perficient\MyDisplayInformation\Helper
 */
class Data extends AbstractHelper
{
    /**
     * constant for preview
     */
    final public const PREVIEW = 'self';

    /**
     * @var
     */
    private $parentUserDisplayInformation;

    /**
     * @var
     */
    private $parentUserDefaultBillingAddress;

    /**
     * Data constructor.
     */
    public function __construct(
        Context $context,
        private readonly Session $customerSession,
        private readonly RoleInfo $roleInfo,
        private readonly Escaper $escaper,
        private readonly Http $request,
        private readonly CountryFactory $countryFactory,
        private readonly CustomerRepositoryInterface $customerRepositoryInterface,
        private readonly AddressRepositoryInterface $addressRepository,
        private readonly  CollectionFactory $myDisplayInformation
    ) {
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getCurrentUserParentHeaderMsg()
    {
        if (!isset($this->parentUserDisplayInformation)) {
            $this->parentUserDisplayInformation = $this->getParentUserDisplayInformationForCurrentUser();
        }
        if (!empty($this->parentUserDisplayInformation) && !empty($this->parentUserDisplayInformation['company_name'])) {
            return $this->parentUserDisplayInformation['company_name'];
        }

        if (!isset($this->parentUserDefaultBillingAddress)) {
            $this->parentUserDefaultBillingAddress = $this->getParentUserBillingAddressInformationForCurrentUser();
        }
        if (!empty($this->parentUserDefaultBillingAddress) && !empty($this->parentUserDefaultBillingAddress['company'])) {
            return $this->parentUserDefaultBillingAddress['company'];
        }
        return '';
    }

    /**
     * @param null $myself
     * @return mixed
     */
    public function getParentUserDisplayInformationForCurrentUser($myself = null)
    {
        $customerSession = $this->customerSession;
        if ($customerSession->isLoggedIn()) {
            if (!empty($myself)) {
                $getUserActualParentId = $myself;
            } else {
                $getUserActualParentId = $this->customerSession->getCustomer()->getUserActualParentId();
            }

            if ($getUserActualParentId != null) {
                $collection = $this->myDisplayInformation->create()
                    ->addFieldToSelect ('*')
                    ->addFieldToFilter('user_id',$getUserActualParentId)->getFirstItem();

                $result = is_countable($collection->getData()) ? count($collection->getData()) : 0;
                if ($result > 0) {
                    return $collection->getData();
                } else {
                    return '';
                }
            }
        }
    }

    /**
     * @param null $myself
     * @return mixed
     */
    public function getParentUserBillingAddressInformationForCurrentUser($myself = null)
    {
        if (!empty($myself)) {
            $getUserActualParentId = $myself;
        } else {
            $getUserActualParentId = $this->customerSession->getCustomer()->getUserActualParentId();
        }
        if ($getUserActualParentId != null) {
            $customer = $this->customerRepositoryInterface->getById($getUserActualParentId);
            $billingAddressId = $customer->getDefaultBilling();
            if($billingAddressId) {
                $billingAddress = $this->addressRepository->getById($billingAddressId);
                $requiredData = [
                    'company' => $billingAddress->getCompany(),
                    'firstname' => $billingAddress->getFirstname(),
                    'lastname' => $billingAddress->getLastname(),
                    'telephone' => $billingAddress->getTelephone(),
                    'fax' => $billingAddress->getFax(),
                    'street' => implode(' ', $billingAddress->getStreet()),
                    'city' => $billingAddress->getCity(),
                    'region' => $billingAddress->getRegion()->getRegion(),
                    'country_id' => $billingAddress->getCountryId(),
                    'postcode' => $billingAddress->getPostcode(),
                ];
                return $requiredData;
            }
            return '';
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getCurrentUserParentFooterMsg()
    {
        $contact_name = '';
        $address = '';
        $city = '';
        $state = '';
        $country = '';
        $zip = '';
        $email = '';
        $phone = '';
        $fax = '';
        $mobile = '';

        if (!isset($this->parentUserDisplayInformation)) {
            $this->parentUserDisplayInformation = $this->getParentUserDisplayInformationForCurrentUser();
        }
        if (!empty($this->parentUserDisplayInformation) && is_array($this->parentUserDisplayInformation)) {
            $contact_name = $this->parentUserDisplayInformation['contact_name'];
            $address = $this->parentUserDisplayInformation['street_line_1'];
            if (!isset($this->parentUserDisplayInformation['street_line_1']) &&
                isset($this->parentUserDisplayInformation['street_line_2']) &&
                !empty($this->parentUserDisplayInformation['street_line_2'])) {
                $address .= $this->parentUserDisplayInformation['addr_two'];
            }elseif(isset($this->parentUserDisplayInformation['street_line_2']) &&
                !empty($this->parentUserDisplayInformation['street_line_2'])){
                $address .= ", " . $this->parentUserDisplayInformation['street_line_2'];
            }
            $city = $this->parentUserDisplayInformation['city'];
            $state = $this->parentUserDisplayInformation['state'];
            $country = $this->parentUserDisplayInformation['country'];
            $zip = $this->parentUserDisplayInformation['zip'];
            $email = $this->parentUserDisplayInformation['email_address'];
            $phone = $this->parentUserDisplayInformation['phone_number'];
            $fax = $this->parentUserDisplayInformation['fax_number'];
            $mobile = $this->parentUserDisplayInformation['mobile_number'];
        }


        if (!isset($this->parentUserDefaultBillingAddress)) {
            $this->parentUserDefaultBillingAddress = $this->getParentUserBillingAddressInformationForCurrentUser();
        }
        if (!empty($this->parentUserDefaultBillingAddress) && is_array($this->parentUserDefaultBillingAddress)) {
            if (empty($contact_name)) {
                $contact_name = $this->parentUserDefaultBillingAddress['firstname'] . ' ' . $this->parentUserDefaultBillingAddress['lastname'];
            }
            if (empty($phone)) {
                $phone = $this->parentUserDefaultBillingAddress['telephone'];
            }
            if (empty($fax)) {
                $fax = $this->parentUserDefaultBillingAddress['fax'];
            }
            if (empty($address)) {
                $address = $this->parentUserDefaultBillingAddress['street'];
                $city = $this->parentUserDefaultBillingAddress['city'];
                $state = $this->parentUserDefaultBillingAddress['region'];
                $countryData = $this->countryFactory->create()->loadByCode($this->parentUserDefaultBillingAddress['country_id']);
                $country = $countryData->getName();
                $zip = $this->parentUserDefaultBillingAddress['postcode'];
            }
        }
        $outputHTML = '';
        if (!empty($contact_name)) {
            $outputHTML .= '<div><span>CONTACT: </span><span>' . $contact_name . '</span></div>';
        }
        if (!empty($address) || !empty($city) || !empty($state) || !empty($country) || !empty($zip)) {
            $outputHTML .= '<div><span>ADDRESS: </span><span>';
            if (!empty($address)) {
                $outputHTML .= $address;
            }
            $outputHTML .= '</span></div>';
            $outputHTML .= '<div><span>';
            if (empty($address) && !empty($city)) {
                $outputHTML .= $city;
            } elseif (!empty($city)) {
                $outputHTML .= $city;
            }
            if (empty($address) && empty($city) && !empty($state)) {
                $outputHTML .= $state;
            } elseif (!empty($state)) {
                $outputHTML .= ', ' . $state;
            }
            if (empty($address) && empty($city) && empty($state) && !empty($country)) {
                $outputHTML .= $country;
            } elseif (!empty($country)) {
                $outputHTML .= ', ' . $country;
            }
            if (empty($address) && empty($city) && empty($state) && empty($country) && !empty($zip)) {
                $outputHTML .= $zip;
            } elseif (!empty($zip)) {
                $outputHTML .= ', ' . $zip;
            }
            $outputHTML .= '</span></div>';
        }
        if (!empty($email)) {
            $outputHTML .= '<div><span>EMAIL: </span><span>' . $email . '</span></div>';
        }
        if (!empty($phone)) {
            $outputHTML .= '<div><span>PHONE: </span><span>' . $phone . '</span></div>';
        }
        if (!empty($fax)) {
            $outputHTML .= '<div><span>FAX: </span><span>' . $fax . '</span></div>';
        }
        if (!empty($mobile)) {
            $outputHTML .= '<div><span>MOBILE: </span><span>' . $mobile . '</span></div>';
        }
        if (!empty($outputHTML)) {
            return $outputHTML;
        }
        return '';
    }

    /**
     * @return string
     */
    public function getCurrentUserWelcomeMsg()
    {
        if (!isset($this->parentUserDisplayInformation)) {
            $this->parentUserDisplayInformation = $this->getParentUserDisplayInformationForCurrentUser();
        }
        if (!empty($this->parentUserDisplayInformation) && !empty($this->parentUserDisplayInformation['welcome_message_on_the_homepage'])) {
            return $this->parentUserDisplayInformation['welcome_message_on_the_homepage'];
        }
        return '';
    }

    /**
     * @return string
     */
    public function previewForParent()
    {
        //return $redirectUrl = $this->redirect->getRefererUrl();
        return $this->request->getPost()->toArray();
    }

    /**
     * @return array|string
     */
    public function getCurrentUserRole()
    {
        $currentUserRole = $this->roleInfo->getCustomerRoles();
        return $this->escaper->escapeHtml($currentUserRole);
    }

    /**
     * @param $myself
     * @return array
     */
    public function emailData($myself)
    {
        $myUserDisplayInformationData = $this->getParentUserDisplayInformationForCurrentUser($myself);
        $myBillingAddressInformation = $this->getParentUserBillingAddressInformationForCurrentUser($myself);
        $data = [];
        $data['company'] = '';
        $data['welcome_message_on_the_homepage'] = '';
        $data['address'] = '';
        $data['email_address'] = '';
        $data['phone_number'] = '';
        $data['mobile_number'] = '';
        $data['fax_number'] = '';
        if (!empty($myUserDisplayInformationData) && !empty($myUserDisplayInformationData['company_name'])) {
            $data['company'] = $myUserDisplayInformationData['company_name'];
        } elseif (!empty($myBillingAddressInformation) && !empty($myBillingAddressInformation['company'])) {
            $data['company'] = $myBillingAddressInformation['company'];
        }
        if (!empty($myUserDisplayInformationData) && !empty($myUserDisplayInformationData['welcome_message_on_the_homepage'])) {
            $data['welcome_message_on_the_homepage'] = $myUserDisplayInformationData['welcome_message_on_the_homepage'];
        }
        if (!empty($myUserDisplayInformationData) && !empty($myUserDisplayInformationData['street_line_1'])) {
            $data['address'] = $myUserDisplayInformationData['street_line_1'] . ',' .
                $myUserDisplayInformationData['street_line_2'] . ',' .
                $myUserDisplayInformationData['city'] . ',' .
                $myUserDisplayInformationData['state'] . ',' .
                $myUserDisplayInformationData['zip'];
        } elseif (!empty($myBillingAddressInformation) && !empty($myBillingAddressInformation['company'])) {
            $data['address'] = $myBillingAddressInformation['street'] . ',' .
                $myBillingAddressInformation['city'] . ',' .
                $myBillingAddressInformation['region'] . ',' .
                $myBillingAddressInformation['postcode'];
        }
        if (!empty($myUserDisplayInformationData) && !empty($myUserDisplayInformationData['email_address'])) {
            $data['email_address'] = $myUserDisplayInformationData['email_address'];
        }
        if (!empty($myUserDisplayInformationData) && !empty($myUserDisplayInformationData['phone_number'])) {
            $data['phone_number'] = $myUserDisplayInformationData['phone_number'];
        } elseif (!empty($myBillingAddressInformation) && !empty($myBillingAddressInformation['telephone'])) {
            $data['phone_number'] = $myBillingAddressInformation['telephone'];
        }
        if (!empty($myUserDisplayInformationData) && !empty($myUserDisplayInformationData['mobile_number'])) {
            $data['mobile_number'] = $myUserDisplayInformationData['mobile_number'];
        }
        if (!empty($myUserDisplayInformationData) && !empty($myUserDisplayInformationData['fax_number'])) {
            $data['fax_number'] = $myUserDisplayInformationData['fax_number'];
        } elseif (!empty($myBillingAddressInformation) && !empty($myBillingAddressInformation['fax'])) {
            $data['fax_number'] = $myBillingAddressInformation['fax'];
        }
        return $data;
    }

    /**
     * @return mixed
     */
    public function getCurrentUserId()
    {
        return $this->customerSession->getCustomer()->getId();
    }

    /**
     * @return array
     */
    public function getParentMydisplayPreview()
    {
        $currentUserRole = $this->getCurrentUserRole();
        if (isset($currentUserRole[0])){
            if ($currentUserRole[0] == CompanyHelper::COMPANY_EMPLOYEE || $currentUserRole[0] == CompanyHelper::COMPANY_MASTER_ROLE) {
                $header_mydisplayinformation = '';
                $body_mydisplayinformation = '';
                $footer_mydisplayinformation = '';
                $params = $this->previewForParent();
                $params['preview'] = self::PREVIEW;
                if (isset($params)
                    && is_array($params)
                    && !empty($params)
                    && isset($params['preview'])
                    && $params['preview'] == self::PREVIEW) {
                    if (isset($params['company_name']) && !empty($params['company_name'])) {
                        $header_mydisplayinformation = $params['company_name'];
                    }
                    if (isset($params['welcome_message_on_the_homepage']) && !empty($params['welcome_message_on_the_homepage'])) {
                        $body_mydisplayinformation = $params['welcome_message_on_the_homepage'];
                    }
                    if (isset($params['contact_name']) && !empty($params['contact_name'])) {
                        $footer_mydisplayinformation .= "<div><span>".__('CONTACT:')." </span>
                    <span>".$params['contact_name']."</span></div>";
                    }

                    if (
                        (isset($params['street_line_1']) && !empty($params['street_line_1'])) ||
                        (isset($params['street_line_2']) && !empty($params['street_line_2'])) ||
                        (isset($params['city']) && !empty($params['city'])) ||
                        (isset($params['state']) && !empty($params['state'])) ||
                        (isset($params['country']) && !empty($params['country'])) ||
                        (isset($params['zip']) && !empty($params['zip']))
                    ) {
                        $footer_mydisplayinformation .= "<div><span>".__('ADDRESS:')." </span><span>";
                        if (isset($params['street_line_1']) && !empty($params['street_line_1'])) {
                            $footer_mydisplayinformation .= $params['street_line_1'];
                        }
                        if (!isset($params['street_line_1']) && isset($params['street_line_2']) && !empty($params['street_line_2'])) {
                            $footer_mydisplayinformation .= $params['street_line_2'];
                        }elseif(isset($params['street_line_2']) && !empty($params['street_line_2'])){
                            $footer_mydisplayinformation .= ", ".$params['street_line_2'];
                        }
                        $footer_mydisplayinformation .= "</span></div>";
                        $footer_mydisplayinformation .= "<div><span>";
                        if (!isset($params['street_line_1']) && !isset($params['street_line_2']) &&
                            isset($params['city']) && !empty($params['city'])) {
                            $footer_mydisplayinformation .= $params['city'];
                        }elseif(isset($params['city']) && !empty($params['city'])){
                            $footer_mydisplayinformation .= $params['city'];
                        }
                        if (!isset($params['street_line_1']) && !isset($params['street_line_2']) && !isset($params['city']) &&
                            isset($params['state']) && !empty($params['state'])) {
                            $footer_mydisplayinformation .= $params['state'];
                        }elseif(isset($params['state']) && !empty($params['state'])){
                            $footer_mydisplayinformation .= ", ".$params['state'];
                        }
                        if (!isset($params['street_line_1']) && !isset($params['street_line_2']) &&
                            !isset($params['city']) && !isset($params['state']) &&
                            isset($params['country']) && !empty($params['country'])) {
                            $footer_mydisplayinformation .= $params['country'];
                        }elseif(isset($params['country']) && !empty($params['country'])){
                            $footer_mydisplayinformation .= ", ".$params['country'];
                        }
                        if (!isset($params['street_line_1']) && !isset($params['street_line_2']) &&
                            !isset($params['city']) && !isset($params['state']) && !isset($params['country']) &&
                            isset($params['zip']) && !empty($params['zip'])) {
                            $footer_mydisplayinformation .= $params['zip'];
                        }elseif(isset($params['zip']) && !empty($params['zip'])){
                            $footer_mydisplayinformation .= ", ".$params['zip'];
                        }
                        $footer_mydisplayinformation .= "</span></div>";
                    }
                    if (isset($params['email_address']) && !empty($params['email_address'])) {
                        $footer_mydisplayinformation .= "<div><span>".__('EMAIL:')." </span>
                      <span>".$params['email_address']."</span></div>";
                    }
                    if (isset($params['phone_number']) && !empty($params['phone_number'])) {
                        $footer_mydisplayinformation .= "<div><span>".__('PHONE:')." </span>
                        <span>".$params['phone_number']."</span></div>";
                    }
                    if (isset($params['mobile_number']) && !empty($params['mobile_number'])) {
                        $footer_mydisplayinformation .= "<div><span>".__('MOBILE:')."</span>
                          <span>".$params['mobile_number'] ."</span></div>";
                    }
                    if (isset($params['fax_number']) && !empty($params['fax_number'])) {
                        $footer_mydisplayinformation .= "<div><span>".__('FAX:')."</span>
                          <span>".$params['fax_number'] ."</span></div>";
                    }
                    return [
                        'header_mydisplayinformation' => $header_mydisplayinformation,
                        'body_mydisplayinformation' => $body_mydisplayinformation,
                        'footer_mydisplayinformation' => $footer_mydisplayinformation,
                    ];
                }
            }
        }
        return [
            'header_mydisplayinformation' => '',
            'body_mydisplayinformation' => '',
            'footer_mydisplayinformation' => '',
        ];
    }
}
