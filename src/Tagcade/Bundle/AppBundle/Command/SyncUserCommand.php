<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Behaviors\UserUtilTrait;
use Tagcade\Model\User\Role\PublisherInterface;


class SyncUserCommand extends ContainerAwareCommand
{
    use UserUtilTrait;
    const COMMAND_NAME = 'tc:user:sync';
    const OPTION_PUBLISHER = 'publisher';

    protected function configure()
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->addOption(self::OPTION_PUBLISHER, 'p', InputOption::VALUE_OPTIONAL, 'Sync specific publisher')
            ->setDescription('Sync all publishers from Tagcade API to Unified Reports API');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $publisherManager = $container->get('tagcade_user.domain_manager.publisher');
        $workerManager = $container->get('tagcade.worker.manager');

        $publishers = [];

        if ($input->getOption(self::OPTION_PUBLISHER)) {
            $publisherId = $input->getOption(self::OPTION_PUBLISHER);
            $publisher = $publisherManager->find($publisherId);

            if (!$publisher instanceof PublisherInterface) {
                $output->writeln(sprintf('<error>Not found publisher by id %s<error>', $publisherId));
                return;
            }

            $publishers[] = $publisher;
        } else {
            $publishers = $publisherManager->all();
        }

        $progress = new ProgressBar($output, count($publishers));
        $progress->start();

        foreach ($publishers as $publisher) {
            if (!$publisher instanceof PublisherInterface) {
                continue;
            }
            if ($publisher->isEnabled() && $publisher->hasUnifiedReportModule()) {
                $entityArray = $this->generatePublisherData($publisher);
                $workerManager->synchronizeUser($entityArray);
            }
            
            $progress->advance();
        }

        $progress->finish();
        $output->writeln('');
        $output->writeln(sprintf('<info>Sync all %s publishers<info>', count($publishers)));
    }
} 