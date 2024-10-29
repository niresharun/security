<?php
/**
 * Rabbitmq publish messages
 * @copyright: Copyright © 2020 Perficient, Inc. All rights reserved.
 * @license: Perficient, Inc.
 * @author: Sandeep Mude <sandeep.mude@perficient.com>
 * @project: Wendover
 * @keywords:  Module Perficient_Rabbitmq
 */
declare(strict_types=1);

namespace Perficient\Rabbitmq\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\MessageQueue\PublisherInterface;

class MessagePublishCommand extends Command
{
    const COMMAND_QUEUE_MESSAGE_PUBLISH = 'queue:message:publish';
    const MESSAGE_ARGUMENT = 'message';
    const TOPIC_ARGUMENT = 'topic';

    /**
     * MessagePublishCommand constructor.
     * @param PublisherInterface $publisher
     * @param null $name
     */
    public function __construct(
        private readonly PublisherInterface $publisher,
        $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * Configure command
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName(self::COMMAND_QUEUE_MESSAGE_PUBLISH);
        $this->setDescription('Publish a message to a topic');
        $this->setDefinition([
            new InputArgument(
                self::MESSAGE_ARGUMENT,
                InputArgument::REQUIRED,
                'Message'
            ),
            new InputArgument(
                self::TOPIC_ARGUMENT,
                InputArgument::REQUIRED,
                'Topic'
            ),
        ]);
        parent::configure();
    }

    /**
     * Publish message
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $message = $input->getArgument(self::MESSAGE_ARGUMENT);
        $topic = $input->getArgument(self::TOPIC_ARGUMENT);
        try {
            $this->publisher->publish($topic, $message);
            $output->writeln(sprintf('Published message "%s" to topic "%s"', $message, $topic));
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
        }
    }
}
