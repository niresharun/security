<?php

namespace Wendover\FindYourRep\Model\Import;

use Magento\Customer\Model\GroupFactory;
use Magento\Framework\Stdlib\StringUtils;
use Magento\ImportExport\Model\ResourceModel\Helper;
use Magento\ImportExport\Model\ResourceModel\Import\Data;
use Wendover\FindYourRep\Model\Import\RowValidatorInterface as ValidatorInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface;
use Wendover\FindYourRep\Model\ResourceModel\Rep\CollectionFactory as RepCollection;
use Wendover\FindYourRep\Model\RepFactory;

class RepImport extends \Magento\ImportExport\Model\Import\Entity\AbstractEntity
{
    const FIRESTNAME = 'firstname';
    const LASTNAME = 'lastname';
    const EMAIL = 'email';
    const PHONE1 = 'phone1';
    const PHONE2 = 'phone2';
    const NOTES = 'notes';
    const POSTALCODE = 'postal_code';
    const TYPE = 'type';
    const TABLE_Entity = 'find_your_rep_main';
    /**
     * Validation failure message template definitions
     *
     * @var array
     */
    protected $_messageTemplates = [ValidatorInterface::ERROR_INVALID_TITLE => 'Message is empty',];
    protected $needColumnCheck = true;
    protected $validColumnNames = [self::FIRESTNAME, self::LASTNAME, self::EMAIL, self::PHONE1, self::PHONE2, self::NOTES, self::POSTALCODE, self::TYPE];
    protected $logInHistory = true;
    protected $_connection;

    /**
     * @param Data $importData
     * @param ResourceConnection $_resource
     * @param Helper $resourceHelper
     * @param StringUtils $string
     * @param ProcessingErrorAggregatorInterface $errorAggregator
     * @param GroupFactory $groupFactory
     * @param RepFactory $repFactory
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\ImportExport\Helper\Data   $importExportData,
        Data                                $importData,
        protected ResourceConnection        $_resource,
        Helper                              $resourceHelper,
        StringUtils                         $string,
        ProcessingErrorAggregatorInterface  $errorAggregator,
        protected GroupFactory              $groupFactory,
        private readonly RepCollection      $repCollection,
        private readonly RepFactory         $repFactory
    )
    {
        $this->jsonHelper = $jsonHelper;
        $this->_importExportData = $importExportData;
        $this->_resourceHelper = $resourceHelper;
        $this->_dataSourceModel = $importData;
        $this->_connection = $_resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $this->errorAggregator = $errorAggregator;
    }

    /**
     * @return mixed|string[]
     */
    public function getValidColumnNames()
    {
        return $this->validColumnNames;
    }

    /**
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'find_your_rep_import';
    }

    /**
     * @param $rowNum
     * @return bool
     */
    public function validateRow(array $rowData, $rowNum)
    {
        if (isset($this->_validatedRows[$rowNum])) {
            return !$this->getErrorAggregator()->isRowInvalid($rowNum);
        }
        $this->_validatedRows[$rowNum] = true;
        return !$this->getErrorAggregator()->isRowInvalid($rowNum);
    }

    /**
     * @return true
     */
    protected function _importData()
    {
        $this->saveEntity();
        return true;
    }

    /**
     * @return $this
     */
    public function saveEntity()
    {
        $this->saveAndReplaceEntity();
        return $this;
    }

    /**
     * @return $this
     */
    protected function saveAndReplaceEntity()
    {
        $behavior = $this->getBehavior();
        $listTitle = [];
        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $entityList = [];
            foreach ($bunch as $rowNum => $rowData) {
                if (!$this->validateRow($rowData, $rowNum)) {
                    $this->addRowError(ValidatorInterface::ERROR_INVALID_TITLE, $rowNum);
                    continue;
                }
                if ($this->getErrorAggregator()->hasToBeTerminated()) {
                    $this->getErrorAggregator()->addRowToSkip($rowNum);
                    continue;
                }

                $rowTtile = $rowData[self::POSTALCODE];
                $listTitle[] = $rowTtile;
                $entityList[$rowTtile][] = [
                    self::FIRESTNAME => $rowData[self::FIRESTNAME],
                    self::LASTNAME => $rowData[self::LASTNAME],
                    self::EMAIL => $rowData[self::EMAIL],
                    self::PHONE1 => $rowData[self::PHONE1],
                    self::PHONE2 => $rowData[self::PHONE2],
                    self::NOTES => $rowData[self::NOTES],
                    self::POSTALCODE => $rowData[self::POSTALCODE],
                    self::TYPE => $rowData[self::TYPE],];
            }
            if (\Magento\ImportExport\Model\Import::BEHAVIOR_REPLACE == $behavior) {
                if ($listTitle) {
                    if ($this->deleteEntityFinish(array_unique($listTitle), self::TABLE_Entity)) {
                        $this->saveEntityFinish($entityList, self::TABLE_Entity);
                    }
                }
            } elseif (\Magento\ImportExport\Model\Import::BEHAVIOR_APPEND == $behavior) {
                $this->saveEntityFinish($entityList, self::TABLE_Entity);
            }
        }
        return $this;
    }

    /**
     * @param $table
     * @return $this
     */
    protected function saveEntityFinish(array $entityData, $table)
    {
        if ($entityData) {
            $entityIn = [];
            foreach ($entityData as $id => $entityRows) {
                foreach ($entityRows as $row) {
                    $entityIn[] = $row;
                }
            }

            if ($entityIn) {
                foreach ($entityIn as $data) {
                    $collection = $this->repCollection->create()->addFieldToSelect('*')
                        ->addFieldToFilter(self::EMAIL, ['eq' => $data['email']])
                        ->addFieldToFilter(self::TYPE, ['eq' => $data['type']])
                        ->addFieldToFilter(self::POSTALCODE, ['eq' => $data['postal_code']]);
                    $collection->getSelect()->limit(1);
                    if ($collection->getSize() > 0) {
                        foreach ($collection as $item) {
                            $item->setFirstname($data['firstname']);
                            $item->setLastname($data['lastname']);
                            $item->setEmail($data['email']);
                            $item->setPhone1($data['phone1']);
                            $item->setPhone2($data['phone2']);
                            $item->setNotes($data['notes']);
                            $item->setPostalCode($data['postal_code']);
                            $item->setType($data['type']);
                        }
                        $collection->save();
                    } else {
                        $model = $this->repFactory->create();
                        try {
                            $model->setData($data)->save();
                        } catch (\Exception $e) {
                            $this->messageManager->addErrorMessage($e, __("We can\'t submit your request, Please try again."));
                        }
                    }
                }
            }
        }
        return $this;
    }
}
