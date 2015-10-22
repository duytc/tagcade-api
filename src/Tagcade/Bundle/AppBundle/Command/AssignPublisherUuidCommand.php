<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Tagcade\Model\User\Role\PublisherInterface;

/**
 * Provides a command-line interface for generating and assigning uuid for all publisher
 */
class AssignPublisherUuidCommand extends ContainerAwareCommand
{

    /**
     * Configure the CLI task
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('tc:publisher:assign-uuid')
            ->setDescription('Generate then assign uuid for those publishers which does not have one.');
        ;
    }

    /**
     * Execute the CLI task
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = $this->getContainer()->get('tagcade_app.service.core.publisher.publisher_service')->GenerateAndAssignUuid();

        $output->writeln(sprintf('%d publisher(s) get updated !', $count));
    }
}
