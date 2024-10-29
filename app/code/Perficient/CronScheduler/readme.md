Perficient Cron Scheduler
=========================

Last tested on Magento2 version 2.3.0

This extension is not compatible with lower version than 2.2.5

For compatibility with lower version than 2.2.5:
----------

Replace below constructor from `Perficient\CronScheduler\Model\Cron\Observer\ProcessCronQueueObserver` file if your magento version is between `2.2.0` to `2.2.4`

	/**
     * ProcessCronQueueObserver constructor.
     * @param ObjectManagerInterface $objectManager
     * @param ScheduleFactory $scheduleFactory
     * @param CacheInterface $cache
     * @param ConfigInterface $config
     * @param ScopeConfigInterface $scopeConfig
     * @param Request $request
     * @param ShellInterface $shell
     * @param DateTime $dateTime
     * @param PhpExecutableFinderFactory $phpExecutableFinderFactory
     * @param LoggerInterface $logger
     * @param State $state
     * @param Job $jobHelper
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ScheduleFactory $scheduleFactory,
        CacheInterface $cache,
        ConfigInterface $config,
        ScopeConfigInterface $scopeConfig,
        Request $request,
        ShellInterface $shell,
        DateTime $dateTime,
        PhpExecutableFinderFactory $phpExecutableFinderFactory,
        LoggerInterface $logger,
        State $state,        
        Job $jobHelper
    ) {
        parent::__construct(
            $objectManager,
            $scheduleFactory,
            $cache,
            $config,
            $scopeConfig,
            $request,
            $shell,
            $dateTime,
            $phpExecutableFinderFactory,
            $logger,
            $state
        );
        $this->jobHelper = $jobHelper;
    }


What I got so far:
----------

 - Gives the ability to schedule any task immediately and in the background
 - Gives the ability to run any task immediately and in the background.
 - Gives the ability to enable or disable any task immediately and in the background.
 - Gives Timeline feature, you can see all scheduled tasks registered by Magento's scheduler queue, and quickly analyize important details pertaining to all your tasks.
 - Provide list of scheduled task which are used to find out status of scheduled task with sechuled, executed and finished time.
 - Added configuration setting for saving disable crons value in core_config_data table which is hide from admin area

The purpose
----------

   You will be able to manage all scheduled cron jobs, which means you have complete control over what tasks fire behind the scenes. An administrator will have the ability of scheduling, removing, enable, disable, analyzing, and running any, and all cron jobs in the cron_schedule table.

Command Line Tools
------------------

   Use the command line tools to run any cron job and view all cron jobs in the system:

   `For view all cron jobs:` `bin/magento perficient_cron:list`
   
   `For run specific cron job:` `bin/magento perficient_cron:run <job_code>` 
   
   `Example:` `bin/magento perficient_cron:run catalog_product_alert` 

Install
-----

Manually:
To install this module copy the code from this repo to `app/code/Perficient` folder of your Magento 2 instance,
If you do this after installing Magento 2 you need to run `php bin/magento setup:upgrade`

Uninstall
--------

 - remove config setting.  `DELETE FROM core_config_data WHERE path = 'system/cron_scheduler/disabled_crons'`
 - remove the folder `app/code/Perficient/CronScheduler`
 - remove the module `Perficient_CronScheduler` from `app/etc/config.php`
 - remove the module `Perficient_CronScheduler` from table `setup_module`: `DELETE FROM setup_module WHERE module='Perficient_CronScheduler'`
 - you need to run `php bin/magento setup:upgrade`
