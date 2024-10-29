<?php
/**
 * Perficient_Core
 *
 * @category: PHP
 * @copyright: Copyright © 2018 Magento. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Kartikey.Pali<kartikey.pali@perficient.com>
 * @project: Wendover
 * @keywords: Core Module
 */
declare(strict_types=1);

namespace Perficient\Core\Plugin\Framework\Data\Form\FormKey\Validator;

use Magento\Framework\Data\Form\FormKey\Validator;
use Perficient\Core\Helper\Data as BaseHelper;

/**
 * Class DisableFormKeyValidation
 * @package Perficient\Core\Plugin\Framework\Data\Form\FormKey\Validator
 */
class DisableFormKeyValidation
{
    /**
     * Disable Form Key Validation
     */
    final public const XML_PATH_DISABLE_FORM_KEY_VALIDATION = 'general/form_key_validation/disable';

    /**
     * DisableFormKeyValidation constructor.
     */
    public function __construct(
        private readonly BaseHelper $baseHelper
    ) {
    }

    /**
     * After Validate
     * @param $result
     * @return mixed
     */
    public function afterValidate(
        Validator $subject,
        $result
    ) {
        $configSetting = $this->baseHelper->getConfigFlag(
            self::XML_PATH_DISABLE_FORM_KEY_VALIDATION
        );

        if ($configSetting) {
            return true;
        }

        return $result;
    }
}
