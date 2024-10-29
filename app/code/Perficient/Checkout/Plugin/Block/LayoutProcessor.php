<?php
/**
 * Checkout Addresses Custom Attribute Installer.
 * @category: Magento
 * @package: Perficient/Checkout
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Checkout
 */
declare(strict_types=1);

namespace Perficient\Checkout\Plugin\Block;

use Magento\Checkout\Block\Checkout\AttributeMerger;
use Magento\Checkout\Block\Checkout\LayoutProcessor as MagentoLayoutProcessor;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Model\AttributeMetadataDataProvider;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Ui\Component\Form\AttributeMapper;

class LayoutProcessor
{
    /**
     * @var null
     */
    private $quote = null;

    final const STREET_LIMIT = 40;

    final const COMPANY_LIMIT = 50;

    final const NAME_LIMIT = 24;

    /**
     * LayoutProcessor constructor.
     * @param AttributeMetadataDataProvider $attributeMetadataDataProvider
     * @param AttributeMapper $attributeMapper
     * @param AttributeMerger $merger
     */
    public function __construct(
        private readonly AttributeMetadataDataProvider $attributeMetadataDataProvider,
        private readonly AttributeMapper               $attributeMapper,
        private readonly AttributeMerger               $merger,
        private readonly CheckoutSession               $checkoutSession,
        private readonly CustomerSession               $customerSession
    )
    {
    }

    public function afterProcess(
        MagentoLayoutProcessor $subject,
        array                  $jsLayout
    ): array
    {

        if (isset($jsLayout['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset'])
        ) {
            $shippingPostcodeFields = $this->getFields('shippingAddress.custom_attributes', 'shipping');
            $shippingFields = $jsLayout['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children'];
            if (isset($shippingFields['street'])) {
                unset($shippingFields['street']['children'][1]['validation']);
                unset($shippingFields['street']['children'][2]['validation']);
            }
            $shippingFields = array_replace_recursive($shippingFields, $shippingPostcodeFields);
            $jsLayout['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children'] = $shippingFields;

            //Street Address Validation WENDOVER-501
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']
            ['children']['street']['children'][0]['validation']['max_text_length'] = self::STREET_LIMIT;

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']
            ['children']['street']['children'][1]['validation']['max_text_length'] = self::STREET_LIMIT;

            //Company Name Validation WENDOVER-501
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']
            ['children']['company']['validation']['max_text_length'] = self::COMPANY_LIMIT;

            //First Name and Last Name Validation WENDOVER-501
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']
            ['children']['firstname']['validation']['max_text_length'] = self::NAME_LIMIT;

            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
            ['shippingAddress']['children']['shipping-address-fieldset']
            ['children']['lastname']['validation']['max_text_length'] = self::NAME_LIMIT;

        }
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']
            ['payments-list'])) {
            $paymentForms = $jsLayout['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']
            ['payments-list']['children'];
            foreach ($paymentForms as $paymentMethodForm => $paymentMethodValue) {
                $paymentMethodCode = str_replace('-form', '', (string)$paymentMethodForm);
                if (!isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$paymentMethodCode . '-form'])) {
                    continue;
                }
                $billingFields = $jsLayout['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']
                ['payments-list']['children'][$paymentMethodCode . '-form']['children']['form-fields']['children'];
                $billingPostcodeFields = $this->getFields('billingAddress' . $paymentMethodCode . '.custom_attributes', 'billing');
                $billingFields = array_replace_recursive($billingFields, $billingPostcodeFields);
                $jsLayout['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']
                ['payments-list']['children'][$paymentMethodCode . '-form']['children']['form-fields']['children'] = $billingFields;

                // Company Name Validation WENDOVER-501
                $jsLayout['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']
                ['payments-list']['children'][$paymentMethodCode . '-form']['children']['form-fields']
                ['children']['company']['validation']['max_text_length'] = self::COMPANY_LIMIT;

                // Street 0 Validation WENDOVER-501
                $jsLayout['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']
                ['payments-list']['children'][$paymentMethodCode . '-form']['children']['form-fields']
                ['children']['street']['children'][0]['validation']['max_text_length'] = self::STREET_LIMIT;

                // Street 1 Validation WENDOVER-501
                $jsLayout['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']
                ['payments-list']['children'][$paymentMethodCode . '-form']['children']['form-fields']
                ['children']['street']['children'][1]['validation']['max_text_length'] = self::STREET_LIMIT;

                // FirstName Validation WENDOVER-501
                $jsLayout['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']
                ['payments-list']['children'][$paymentMethodCode . '-form']['children']['form-fields']
                ['children']['firstname']['validation']['max_text_length'] = self::NAME_LIMIT;

                // LastName Validation WENDOVER-501
                $jsLayout['components']['checkout']['children']['steps']['children']
                ['billing-step']['children']['payment']['children']
                ['payments-list']['children'][$paymentMethodCode . '-form']['children']['form-fields']
                ['children']['lastname']['validation']['max_text_length'] = self::NAME_LIMIT;
            }
        }
        return $jsLayout;
    }

    /**
     * @param $scope
     * @param $addressType
     * @return array
     */
    public function getFields($scope, $addressType)
    {
        $fields = [];
        foreach ($this->getAdditionalFields($addressType) as $field) {
            $fields[$field] = $this->getField($field, $scope);
        }
        return $fields;
    }

    /**
     * @param string $addressType
     * @return array
     */
    public function getAdditionalFields($addressType = 'shipping')
    {
        $customAddressFields = ['order_shipping_notes',
            'location',
            'delivery_appointment',
            'loading_dock_available'
        ];
        return $customAddressFields;
    }

    /**
     * @param $attributeCode
     * @param $scope
     * @return array
     */
    public function getField($attributeCode, $scope)
    {
        $field = [
            'config' => [
                'customScope' => $scope,
            ],
            'dataScope' => $scope . '.' . $attributeCode,
        ];
        return $field;
    }
}
