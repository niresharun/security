<?php
/**
 * prepared to run cron through command line
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
use Symfony\Component\Console\Input\InputArgument;
use Magento\Framework\App\State;
use Magento\Framework\Console\Cli;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Magento\Framework\App\Area;

class Runjob extends Command
{
    const INPUT_KEY_JOB_CODE = 'job_code';

    /**
     * Runjob constructor.
     * @param State $state
     * @param ManagerFactory $managerFactory
     * @param DateTimeFactory $dateTimeFactory
     */
    public function __construct(
        private readonly State $state,
        private readonly ManagerFactory $managerFactory,
        private readonly DateTimeFactory $dateTimeFactory
    ) {
        parent::__construct();
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $arguments = [
            new InputArgument(
                self::INPUT_KEY_JOB_CODE,
                InputArgument::REQUIRED,
                'Job code input (ex. \'catalog_product_alert\')'
            )
        ];

        $this->setName("perficient_cron:run");
        $this->setDescription("Run a specific cron job by its job_code ");
        $this->setDefinition($arguments);
        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Perficient\CronScheduler\Model\Manager $manager */
        $manager = $this->managerFactory->create();
        /** @var \Magento\Framework\Stdlib\DateTime\DateTime $dateTime */
        $dateTime = $this->dateTimeFactory->create();

        try {
            $this->state->setAreaCode(Area::AREA_ADMINHTML);

            // create a new cron job and dispatch it
            $jobCode = $input->getArgument(self::INPUT_KEY_JOB_CODE);

            $now = $dateTime->gmtTimestamp();
            $formattedNow = $manager->getFormattedDateTime($now);

            $schedule = $manager->createCronJob($jobCode, $formattedNow);
            $manager->dispatchCron($jobCode, $schedule);
            $output->writeln("$jobCode successfully ran");
            return Cli::RETURN_SUCCESS;
        } catch (LocalizedException $e) {
            $output->writeln($e->getMessage());
            return Cli::RETURN_FAILURE;
        }
    }
}
