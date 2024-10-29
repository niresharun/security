<?php
/**
 * This module is used to create custom artwork catalogs.
 *
 * @category: Magento
 * @package: Perficient/MyCatalog
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Kartikey Pali <Kartikey.Pali@Perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_MyCatalog
 */
declare(strict_types=1);

namespace Perficient\MyCatalog\Api\Data;

/**
 * Interface TemplateInterface
 * @package Perficient\MyCatalog\Api\Data
 */
interface TemplateInterface
{
    /**
     * @return int|null
     */
    public function getTemplateId();

    /**
     * @param int $value
     * @return $this
     */
    public function setTemplateId($value);

    /**
     * @return int|null
     */
    public function getTemplateDropSpotsCount();

    /**
     * @param int $value
     * @return $this
     */
    public function setTemplateDropSpotsCount($value);

    /**
     * @return string|null
     */
    public function getTemplateFile();

    /**
     * @param string $value
     * @return $this
     */
    public function setTemplateFile($value);

    /**
     * @return string|null
     */
    public function getTemplateName();

    /**
     * @param string $value
     * @return $this
     */
    public function setTemplateName($value);
}
