<?php

namespace Wendover\FindYourRep\Model\Import;

interface RowValidatorInterface extends \Magento\Framework\Validator\ValidatorInterface
{
    const ERROR_INVALID_TITLE = 'InvalidValueTITLE';

    /**
     * Initialize validator
     *
     * @return $this
     */
    public function init($context);
}
