<?php
/**
 * Magento Productimize module to make API request/response.
 *
 * @category: Magento
 * @package: Perficient/Productimize
 * @copyright: Copyright © 2020 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 * @license: Magento Enterprise Edition (MEE) license
 * @author: Trupti Bobde <trupti.bobde@perficient.com>
 * @project: Wendover
 * @keywords: Module Perficient_Productimize
 */
declare(strict_types=1);

namespace Perficient\Productimize\Model;

use Magento\Framework\Api\SearchCriteriaInterface;
use Perficient\Productimize\Api\ArtrulesInterface;
use Perficient\Rabbitmq\Api\Data\TreatmentInterfaceFactory;
use Perficient\Rabbitmq\Api\Data\MediaInterfaceFactory;
use Perficient\Rabbitmq\Api\Data\MediaTreatmentInterfaceFactory;
use Perficient\Rabbitmq\Api\Data\FrameTreatmentInterfaceFactory;
/**
 * Class Artrules
 * @package Perficient\Productimize\Model
 */
class Artrules implements ArtrulesInterface
{
    const TABLE_MEDIA = 'media';

    const TABLE_TREATMENT = 'treatment';

    const TABLE_MEDIA_TREATMENT = 'media_treatment';

    const TABLE_FRAME_TREATMENT = 'frame_treatment';

    /**
     * @var TreatmentInterfaceFactory
     */
    private $treatmentFactory;

    /**
     * @var MediaInterfaceFactory
     */
    private $mediaFactory;

    /**
     * @var MediaTreatmentInterfaceFactory
     */
    private $mediaTreatmentFactory;

    /**
     * @var FrameTreatmentInterfaceFactory
     */
    private $frameTreatmentFactory;

    /**
     * Artrules constructor.
     * @param TreatmentInterfaceFactory $treatmentFactory
     * @param MediaInterfaceFactory $mediaFactory
     * @param MediaTreatmentInterfaceFactory $mediaTreatmentFactory
     * @param FrameTreatmentInterfaceFactory $frameTreatmentFactory
     */
    public function __construct(
        TreatmentInterfaceFactory  $treatmentFactory,
        MediaInterfaceFactory $mediaFactory,
        MediaTreatmentInterfaceFactory $mediaTreatmentFactory,
        FrameTreatmentInterfaceFactory $frameTreatmentFactory
    ) {
        $this->treatmentFactory = $treatmentFactory;
        $this->mediaFactory = $mediaFactory;
        $this->mediaTreatmentFactory = $mediaTreatmentFactory;
        $this->frameTreatmentFactory = $frameTreatmentFactory;
     }

    /**
     * @inheritdoc
     */
    public function getList()
    {
        $artrulesData = [];
        $mediaData = $this->getMedia();
        $treatmentData = $this->getTreatment();
        $mediaTreatmentData = $this->getMediaTreatment();
        $frameTreatmentData = $this->getFrameTreatment();

        if(!empty($mediaData)) {
            $artrulesData[] = $mediaData;
        }
        if(!empty($treatmentData)) {
            $artrulesData[] = $treatmentData;
        }
        if(!empty($mediaTreatmentData)) {
            $artrulesData[] = $mediaTreatmentData;
        }
        if(!empty($frameTreatmentData)) {
            $artrulesData[] = $frameTreatmentData;
        }

        $artrules[] = [
           'items' =>  $artrulesData
        ];

       return $artrules;
    }

    /**
     * Get media collection.
     * @param null
     * @return array
     */
    private function getMedia()
    {
        $mediaData = [];
        $media = [];
        $mediaCollection = $this->mediaFactory->create()->getCollection();

        if ($mediaCollection->count()) {
            foreach ($mediaCollection as $media) {
                // Get the media data.
                $mediaData[] = $media->getData();
            }
            $media = ['table' => self::TABLE_MEDIA, 'data' => $mediaData];
        }

        return $media;
    }

    /**
     * Get media treatment collection.
     * @param null
     * @return array
     */
    private function getMediaTreatment()
    {
        $mediaTreatmentData = [];
        $mediaTreatment = [];
        $mediaTreatmentCollection = $this->mediaTreatmentFactory->create()->getCollection();

        if ($mediaTreatmentCollection->count()) {
            foreach ($mediaTreatmentCollection as $mediaTreatment) {
                // Get the media treatment data.
                $mediaTreatmentData[] = $mediaTreatment->getData();
            }
            $mediaTreatment = ['table' => self::TABLE_MEDIA_TREATMENT, 'data' => $mediaTreatmentData];
        }

        return $mediaTreatment;
    }

    /**
     * Get treatment collection.
     * @param null
     * @return array
     */
    private function getTreatment()
    {
        $treatmentData = [];
        $treatment = [];
        $treatmentCollection = $this->treatmentFactory->create()->getCollection();

        if ($treatmentCollection->count()) {
            foreach ($treatmentCollection as $treatment) {
                // Get the treatment data.
                $treatmentData[] = $treatment->getData();
            }
            $treatment = ['table' => self::TABLE_TREATMENT, 'data' => $treatmentData];
        }

        return $treatment;
    }

    /**
     * Get frame treatment collection.
     * @param null
     * @return array
     */
    private function getFrameTreatment()
    {

        $frameTreatmentData = [];
        $frameTreatment = [];
        $frameTreatmentCollection = $this->frameTreatmentFactory->create()->getCollection();

        if ($frameTreatmentCollection->count()) {
            foreach ($frameTreatmentCollection as $frameTreatment) {
                // Get the frame treatment data.
                $frameTreatmentData[] = $frameTreatment->getData();
            }
            $frameTreatment = ['table' => self::TABLE_FRAME_TREATMENT, 'data' => $frameTreatmentData];
        }

        return $frameTreatment;
    }

}
