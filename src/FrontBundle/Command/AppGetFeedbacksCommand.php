<?php

namespace FrontBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppGetFeedbacksCommand extends ContainerAwareCommand
{
    /**
     * @var ContainerInterface $container
     */
    private $container;

    protected function configure()
    {
        $this
            // the name of the command (the part after "bin/console")
            ->setName('app:get-feedbacks');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'feedBack saver',
            '============'
        ]);
        $this->container->get('app.services.queue_manager')->getFromQueue();
    }

    /**
     * Sets the container.
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

}