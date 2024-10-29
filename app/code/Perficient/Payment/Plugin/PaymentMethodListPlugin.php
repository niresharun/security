<?php
/**
 * Customer Specific Payment Methods
 * @category: Magento
 * @package: Perficient/Payment
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Sachin Badase <Sachin.Badase@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Payment
 */
declare(strict_types=1);

namespace Perficient\Payment\Plugin;

use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Eav\Model\Config;
use Magento\Payment\Model\PaymentMethodList as MagentoPaymentMethodList;
use ParadoxLabs\Authnetcim\Model\ConfigProvider;
use ParadoxLabs\Authnetcim\Model\Ach\ConfigProvider as AchConfigProvider;

class PaymentMethodListPlugin
{
     const PREPAY = 'Prepay';
     const NOT_PREPAY = 'Net';
     const CREDIT_TERM_GROUP = 'credit_terms_group';

    /**
     * PaymentMethodListPlugin constructor.
     */
    public function __construct(
        private readonly CustomerSession $customerSession,
        private readonly Config $config
    ) {
    }

    /**
     * @param MagentoPaymentMethodList $subject
     * @param $methodList
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function afterGetActiveList(
        MagentoPaymentMethodList $subject,
        $methodList
    ) {
        if ($this->customerSession->isLoggedIn()) {
            $eavConfig = $this->config;
            $creditTermsGroupType = $this->customerSession->getCustomer()->getCreditTermsGroup();
            $attribute = $eavConfig->getAttribute(Customer::ENTITY, self::CREDIT_TERM_GROUP);
            $creditTermsGroupLabel = $attribute->getSource()->getOptionText($creditTermsGroupType);
            if ($creditTermsGroupLabel == self::NOT_PREPAY) {
                return array_values($methodList);
            }
            $authNetCimArray = [ConfigProvider::CODE,AchConfigProvider::CODE];
            $activeMethodList = array_values($methodList);
            foreach ($activeMethodList as $key => $value) {
                $value = (array)$value;
                $updatedValues = array_values($value);
                if (!in_array($updatedValues[0], $authNetCimArray)) {

                    unset($activeMethodList[$key]);
                }
            }
            return $activeMethodList;
        } else {
            /*future to-do for Guest User*/
            return array_values($methodList);
        }
    }
}
