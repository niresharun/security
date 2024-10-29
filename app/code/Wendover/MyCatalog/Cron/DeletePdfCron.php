<?php
Namespace Wendover\MyCatalog\Cron;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
class DeletePdfCron {

    const CATALOG_LOGO_PATH = 'custom_catalog/logos';

    public function __construct(
        protected readonly LoggerInterface            $logger,
        private readonly DirectoryList                $directoryList,
        private readonly StoreManagerInterface        $storeManager,
        private readonly File                         $file,
        private readonly ScopeConfigInterface         $scopeConfig
    )
    {

    }
   /**
    * Write to system.log
    *
    * @return void
    */
    public function execute() {

            try {
                $expireDays = $this->scopeConfig->getValue('perficient_mycatalog/email/no_of_days_to_expire_link', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
                $this->logger->info('Delete Pdf Cron--- start');
                if ($expireDays){
                    $this->logger->info('Delete Pdf Cron--- if inside');
                    $path =  $this->directoryList->getPath('media').'/custom_catalog/logos/pdf';
                    $this->logger->info('Delete Pdf Cron--- path-'.$path);
                    $files = scandir($path);
                    $i=0;
                    foreach ($files as $filename) {
                        $replace_filename = str_replace(".","-",$filename);
                        $this->logger->info('Delete Pdf Cron--- replace_filename-'.$replace_filename);
                        if (file_exists($path.'/'.$replace_filename)) {
                            $this->logger->info('Delete Pdf Cron--- file check-'.$replace_filename);
                            $beforeDaysAgoTimestamp = strtotime('-'.$expireDays.' days');
                            $path_file = $path.'/'.$replace_filename;
                            $fileCreationTime = filemtime($path_file);
                            if ($fileCreationTime < $beforeDaysAgoTimestamp ) {
                                $fileCreationTime.' - '. $replace_filename;
                                $this->file->rmdir($path_file,true);
                                $i++;
                            }
                        }
                    }
                    $this->logger->info('Total folder deleted count-'. $i);

                } else {
                    $this->logger->info('No Expire days');
                }
                $this->logger->info('Delete Pdf Cron--- end');
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }

    }
}
