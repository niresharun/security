<?php
/**
 * Displays list of crons through command line
 *
 * @category: Perficient's Extension
 * @package: Perficient\CronScheduler
 * @copyright: Copyright © 2018 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandip Titarmare<sandip.titarmare@perficient.com>
 * @keywords: cron list, enables and disables specific crons
 */
namespace Perficient\CronScheduler\Console\Command;

use Magento\Framework\Exception\LocalizedException;
use Perficient\CronScheduler\Model\ManagerFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Perficient\CronScheduler\Helper\Job;
use Symfony\Component\Console\Helper\Table;

class Showjobs extends Command
{
    private array $headers = ['Code', 'Group', 'Schedule', 'Status', 'Instance'];

    public function __construct(
        private readonly State $state,
        private readonly ManagerFactory $managerFactory,
        protected Job $jobHelper
    ) {
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName("perficient_cron:list");
        $this->setDescription("Show all cron job codes in Magento");
        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Perficient\CronScheduler\Model\Manager $manager */
        $manager = $this->managerFactory->create();

        try {
            $this->state->setAreaCode(Area::AREA_ADMINHTML);

            $jobs = $manager->getCronJobs();
            $table = new Table($output);
            $table = $table->setHeaders($this->headers);

            foreach ($jobs as $group => $crons) {
                foreach ($crons as $code => $job) {
                    $instance = ($job['instance'] ?? "");
                    $method = ($job['method'] ?? "");
                    $schedule = ($job['schedule'] ?? "");
                    $status = '';
                    if (!isset($job['status'])) {
                        $disabledCrons = $this->jobHelper->getDisableCrons();
                        $status = in_array($code, $disabledCrons) ? Job::STATUS_DISABLED : Job::STATUS_ENABLED;
                    }
                    $jobData = [
                        $code,
                        $group,
                        $schedule,
                        $status,
                        "$instance::$method"
                    ];
                    $table->addRow($jobData);
                }
            }

            $table->render($output);
            return Cli::RETURN_SUCCESS;
        } catch (LocalizedException $e) {
            $output->writeln($e->getMessage());
            return Cli::RETURN_FAILURE;
        }
    }
}
