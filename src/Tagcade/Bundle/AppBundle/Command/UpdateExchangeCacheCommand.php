<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class UpdateExchangeCacheCommand extends ContainerAwareCommand
{
    const COMMAND_UPDATE = 'update';
    const COMMAND_DELETE = 'delete';
    /**
     * Configure the CLI task
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('tc:exchange-cache:update')
            ->setDescription('Update caches when exchange parameter is updated or deleted')
            ->addArgument(
                'abbr',
                InputArgument::REQUIRED,
                'The exchange that is being updated'
            )
            ->addOption(
                'type',
                'type',
                InputOption::VALUE_REQUIRED,
                'type of command :
                    - "update" : update all existing cache to the new exchange abbreviation
                    - "delete" : remove the specified exchange abbreviation from  all existing cache
                '
            )
            ->addOption(
                'newAbbr',
                'new',
                InputOption::VALUE_OPTIONAL,
                'the new abbreviation is being updated'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var PublisherManagerInterface $publisherManager */
        $publisherManager = $this->getContainer()->get('tagcade_user.domain_manager.publisher');
        $publishers = $publisherManager->allActivePublishers();

        $exchanges = $this->getContainer()->getParameter('rtb.exchanges');
        $exchanges = array_map(function(array $exchange) {
            return $exchange['abbreviation'];
        }, $exchanges);

        $currentExchange = $input->getArgument('abbr');
        $type = $input->getOption('type');
        $newExchange = $input->getOption('newAbbr');

        if (!in_array($currentExchange, $exchanges)) {
            $output->writeln(sprintf('<error>exchange "%s" is currently not supported</error>', $currentExchange));
            return false;
        }

        foreach($publishers as $publisher) {
            if (!$publisher instanceof PublisherInterface) {
                throw new \Exception('That publisher does not exist');
            }

            if ($publisher->hasRtbModule()) {
                /** @var array $publisherExchanges */
                $publisherExchanges = $publisher->getExchanges();

                $key = array_search($currentExchange, $publisherExchanges);

                if ($key !== FALSE) {
                    if ($type === self::COMMAND_DELETE) {
                        unset($publisherExchanges[$key]);
                        $publisherExchanges = array_values($publisherExchanges);
                    }
                    else if ($type === self::COMMAND_UPDATE) {
                        if ($newExchange === null) {
                            $output->writeln(sprintf('<question>Are you missing the new name of the exchange ?</question>'));
                            return false;
                        }

                        $publisherExchanges[$key] = $newExchange;
                    }
                    else {
                        $output->writeln(sprintf('<error>command type "%s" is not supported</error>', $type));
                        return false;
                    }

                    $publisher->setExchanges($publisherExchanges);
                    $publisherManager->save($publisher);
                }
            }
        }

        return true;
    }
}