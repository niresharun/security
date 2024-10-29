<?php
/**
 * Disable all selected cron jobs
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Controller\Adminhtml\Job;

use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Store\Model\Store;
use Perficient\CronScheduler\Helper\Job;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Cache\ManagerFactory;
use Perficient\CronScheduler\Helper\Url;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\RequestInterface;

class MassDisable extends Action
{
    /**
     * @var string
     */
    protected $redirectUrl = Url::JOB_LISTING;

    /**
     * MassDisable constructor.
     * @param Context $context
     * @param Config $config
     * @param ManagerFactory $cacheManagerFactory
     * @param RequestInterface $request
     */
    public function __construct(
        Context $context,
        public Job $jobHelper,
        protected Config $config,
        public ManagerFactory $cacheManagerFactory,
        protected RequestInterface $request,
    ) {
        parent::__construct($context);
    }

    /**
     * disabled cronjob
     */
    public function execute()
    {
        $params = $this->request->getParam('selected');

        if (!isset($params)) {
            $this->messageManager->addErrorMessage(__('Something went wrong when receiving the request'));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath($this->redirectUrl);
            return $resultRedirect;
        }
        $disabledCrons = $this->jobHelper->getDisableCrons();

        try {
            foreach ($params as $jobCode) {
                if (!in_array($jobCode, $disabledCrons)) {
                    $disabledCrons[] = $jobCode;
                    $this->messageManager->addSuccessMessage(__('The job "%1" has been disabled.', $jobCode));
                }
            }
            $this->config->saveConfig(
                Job::XML_PATH_DISABLED_CRONS,
                implode(',', $disabledCrons),
                ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
                Store::DEFAULT_STORE_ID
            );
            /** @var \Magento\Framework\App\Cache\Manager $cacheManager */
            $cacheManager = $this->cacheManagerFactory->create();
            $types = ['config'];
            $cacheManager->clean($types);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath($this->redirectUrl);
            return $resultRedirect;
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->redirectUrl);
        return $resultRedirect;
    }
}
