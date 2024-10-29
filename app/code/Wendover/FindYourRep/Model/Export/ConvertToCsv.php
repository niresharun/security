<?php

namespace Wendover\FindYourRep\Model\Export;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\MetadataProvider;
use Magento\Ui\Model\Export\ConvertToCsv as ConvertToCsvParent;

/**
 * Class ConvertToCsv
 */
class ConvertToCsv extends ConvertToCsvParent
{
    /**
     * @var DirectoryList
     */
    protected $directory;

    protected $pageSize = null;

    /**
     * @param Filesystem $filesystem
     * @param Filter $filter
     * @param MetadataProvider $metadataProvider
     * @param TimezoneInterface $timezone
     * @param int $pageSize
     * @throws FileSystemException
     */
    public function __construct(
        Filesystem                         $filesystem,
        Filter                             $filter,
        MetadataProvider                   $metadataProvider,
        private readonly TimezoneInterface $timezone,
        int                                $pageSize = 200
    )
    {
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->pageSize = $pageSize;
        parent::__construct($filesystem, $filter, $metadataProvider, $pageSize);
    }

    /**
     * Returns CSV file
     *
     * @throws LocalizedException
     * @throws \Exception
     */
    public function getCsvFile(): array
    {
        $component = $this->filter->getComponent();
        // phpcs:ignore Magento2.Security.InsecureFunction
        $name = md5(microtime());
        $file = 'export/' . $component->getName() . $name . '.csv';
        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();
        $dataProvider = $component->getContext()->getDataProvider();
        $fields = $this->metadataProvider->getFields($component);
        $options = $this->metadataProvider->getOptions();
        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();
        $stream->writeCsv($this->metadataProvider->getHeaders($component));
        $i = 1;
        $searchCriteria = $dataProvider->getSearchCriteria()
            ->setCurrentPage($i)
            ->setPageSize($this->pageSize);
        $totalCount = (int)$dataProvider->getSearchResult()->getTotalCount();
        while ($totalCount > 0) {
            $items = $dataProvider->getSearchResult()->getItems();
            foreach ($items as $item) {
                $this->metadataProvider->convertDate($item, $component->getName());
                $stream->writeCsv($this->metadataProvider->getRowData($item, $fields, $options));
            }
            $searchCriteria->setCurrentPage(++$i);
            $totalCount = $totalCount - $this->pageSize;
        }
        $stream->unlock();
        $stream->close();
        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true  // can delete file after use
        ];
    }
}
