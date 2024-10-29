<?php
/**
 * Extend ParadoxLabs Authnetcim to change eCheckType form WEB to PPD
 * for savings and checking.
 * @category: Magento
 * @package: Perficient/ParadoxLabsAuthnetcim
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Amin Akhtar
 * @project: Wendover
 * @keywords: Module Perficient_ParadoxLabsAuthnetcim
 */
namespace Perficient\ParadoxLabsAuthnetcim\Model\Ach;

use ParadoxLabs\Authnetcim\Model\Card as AuthnetcimModelCard;

class Card extends \ParadoxLabs\Authnetcim\Model\Ach\Card
{
    /**
     * Set card payment data from a quote or order payment instance.
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @return $this
     */
    public function importPaymentInfo(\Magento\Payment\Model\InfoInterface $payment)
    {
        AuthnetcimModelCard::importPaymentInfo($payment);

        if ($payment instanceof \Magento\Payment\Model\InfoInterface) {
            /** @var \Magento\Payment\Model\Info $payment */

            if (!empty($payment->getData('echeck_account_name'))) {
                $this->setAdditional('echeck_account_name', $payment->getData('echeck_account_name'));
            }

            if (!empty($payment->getData('echeck_bank_name'))) {
                $this->setAdditional('echeck_bank_name', $payment->getData('echeck_bank_name'));
            }

            if (!empty($payment->getData('echeck_account_type'))) {
                $this->setAdditional('echeck_account_type', $payment->getData('echeck_account_type'));
            }

            if (!empty($payment->getData('echeck_routing_no'))) {
                $this->setAdditional(
                    'echeck_routing_number_last4',
                    substr((string)$payment->getData('echeck_routing_no'), -4)
                );
            }

            if (!empty($payment->getData('echeck_account_no'))) {
                $this->setAdditional(
                    'echeck_account_number_last4',
                    substr((string)$payment->getData('echeck_account_no'), -4)
                );
            }
        }

        return $this;
    }

    /**
     * On card save, set payment data to the gateway. (Broken out for extensibility)
     *
     * @param \ParadoxLabs\TokenBase\Api\GatewayInterface $gateway
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function setPaymentInfoOnCreate(\ParadoxLabs\TokenBase\Api\GatewayInterface $gateway)
    {
        /** @var \Magento\Payment\Model\Info $info */
        $info = $this->getInfoInstance();

        if ($info->getData('echeck_account_type') != 'businessChecking') {
            $gateway->setParameter('echeckType', 'PPD');
        } else {
            $gateway->setParameter('echeckType', 'CCD');
        }

        $gateway->setParameter('nameOnAccount', $info->getData('echeck_account_name'));
        $gateway->setParameter('bankName', $info->getData('echeck_bank_name'));
        $gateway->setParameter('accountType', $info->getData('echeck_account_type'));
        $gateway->setParameter('routingNumber', $info->getData('echeck_routing_no'));
        $gateway->setParameter('accountNumber', $info->getData('echeck_account_no'));

        return $this;
    }

    /**
     * On card update, set payment data to the gateway. (Broken out for extensibility)
     *
     * @param \ParadoxLabs\TokenBase\Api\GatewayInterface $gateway
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function setPaymentInfoOnUpdate(\ParadoxLabs\TokenBase\Api\GatewayInterface $gateway)
    {
        /** @var \Magento\Payment\Model\Info $info */
        $info = $this->getInfoInstance();

        if ($info->getData('echeck_account_type') != 'businessChecking') {
            $gateway->setParameter('echeckType', 'PPD');
        } else {
            $gateway->setParameter('echeckType', 'CCD');
        }

        $gateway->setParameter('nameOnAccount', $info->getData('echeck_account_name'));
        $gateway->setParameter('bankName', $info->getData('echeck_bank_name'));
        $gateway->setParameter('accountType', $info->getData('echeck_account_type'));

        // Potentially masked routing number
        if (strlen((string)$info->getData('echeck_routing_no')) > 8) {
            $gateway->setParameter('routingNumber', $info->getData('echeck_routing_no'));
        } else {
            $gateway->setParameter(
                'routingNumber',
                'XXXX' . $this->getAdditional('echeck_routing_number_last4')
            );
        }

        // Potentially masked account number
        if (strlen((string)$info->getData('echeck_account_no')) > 8) {
            $gateway->setParameter('accountNumber', $info->getData('echeck_account_no'));
        } else {
            $gateway->setParameter(
                'accountNumber',
                'XXXX' . $this->getAdditional('echeck_account_number_last4')
            );
        }

        return $this;
    }
}
