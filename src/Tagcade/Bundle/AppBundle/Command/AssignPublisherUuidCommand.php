<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->setDescription('Generate then assign uuid for those publishers which does not have one.')
            ->addArgument(
                'id',
                InputArgument::OPTIONAL,
                'The publisher\'s id to be updated'
            )
            ->addOption(
                'all',
                null,
                InputOption::VALUE_NONE,
                'If set, all publishers which have uuid unspecified will get updated'
            )
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
        if ($input->getOption('all')) {
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
            $output->writeln(sprintf('<info>%d publisher(s) get updated !</info>', $count) );
        }
        else {
            $id = $input->getArgument('id');
            if ($id) {
                $publisher = $publisherManager->find(filter_var($id, FILTER_VALIDATE_INT));
                if (!$publisher instanceof PublisherInterface) {
                    $output->writeln('<error>The publisher does not exist</error>');
                }
                else {
                    if ($publisher->getUuid() === null) {
                        $count++;
                        $publisher->setUuid($publisherManager->generateUuid($publisher));
                        $publisherManager->save($publisher);
                    }
                    $output->writeln(sprintf('<info>%d publisher(s) get updated !</info>', $count) );
                }
            }
            else {
                $output->writeln('<question>Are you missing {id} or --all option ?"</question>');
                $output->writeln('<question>Try "php app/console tc:publisher:assign-uuid {id}"</question>');
                $output->writeln('<question>Or "php app/console tc:publisher:assign-uuid --all"</question>');
            }
        }
    }
}
