<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
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
        /**
         * @var PublisherManagerInterface $publisherManager
         */
        $publisherManager = $this->getContainer()->get('tagcade_user.domain_manager.publisher');
        $count = 0;
        $allPublisher = $publisherManager->allPublishers();
        /**
         * @var PublisherInterface $publisher
         */
        foreach($allPublisher as $publisher) {
            if ($publisher->getUuid() === null) {
                $count++;
                $publisher->setUuid($publisherManager->generateUuid($publisher));
                $publisherManager->save($publisher);
            }
        }

        $output->writeln(sprintf('%d publisher(s) get updated !', $count));
    }
}
