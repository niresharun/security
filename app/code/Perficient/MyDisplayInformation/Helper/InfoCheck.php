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

use Magento\Framework\App\Helper\Context;
use Perficient\MyDisplayInformation\CustomerData\CustomSection;
use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Class InfoCheck
 * @package Perficient\MyDisplayInformation\Helper
 */
class InfoCheck extends AbstractHelper
{
    /**
     * @var CustomSection
     */
    protected $customSection;

    /**
     * InfoCheck constructor.
     * @param Context $context
     * @param CustomSection $customSection
     */

    public function __construct(
        Context $context,
        CustomSection $customSection

    ) {
        parent::__construct($context);
        $this->customSection = $customSection;
      }

   public function loadDataForLoggedInUser (){
       /*$myDisplayInformationData = $this->customSection->getSectionData();
       $dataCheck = false;
       if(!empty($myDisplayInformationData['header_mydisplayinformation'])){
           $dataCheck = true;
       }
       if(!empty($myDisplayInformationData['body_mydisplayinformation'])){
           $dataCheck = true;
       }

       if(!empty($myDisplayInformationData['footer_mydisplayinformation'])){
           $dataCheck = true;
       }

       return $dataCheck;*/


   }

}
