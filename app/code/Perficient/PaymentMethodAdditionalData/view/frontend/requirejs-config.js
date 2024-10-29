/**
 * Custom Module to store Additional Payment Data to Quote and Order in Payment Tables
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sachin Badase <sachin.badase@perficient.com>
 * @keywords: Payment Data to Quote and Order in Payment Tables
 */
var config = {
    map: {
        '*': {
            'Magento_OfflinePayments/js/view/payment/offline-payments': 'Perficient_PaymentMethodAdditionalData/js/view/payment/offline-payments',
            'ParadoxLabs_Authnetcim/js/view/payment/authnetcim-ach': 'Perficient_PaymentMethodAdditionalData/js/view/payment/authnetcim-ach',
            'ParadoxLabs_Authnetcim/js/view/payment/authnetcim': 'Perficient_PaymentMethodAdditionalData/js/view/payment/authnetcim',
            'Magento_Payment/js/view/payment/payments': 'Perficient_PaymentMethodAdditionalData/js/view/payment/payments',
        }
    }
}
