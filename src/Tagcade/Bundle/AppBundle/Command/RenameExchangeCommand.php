<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Core\Exchange\ExchangeCacheUpdaterInterface;

class RenameExchangeCommand extends ContainerAwareCommand
{
    /**
     * Configure the CLI task
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('tc:exchange:rename')
            ->addOption('preName', 'pn', InputOption::VALUE_REQUIRED, 'Previous abbreviation name in parameter configuration file')
            ->addOption('newName', 'nn', InputOption::VALUE_REQUIRED, 'New abbreviation name that will be set in parameter configuration file')
            ->setDescription('Update all existing caches when an exchange\'s name is updated in parameter')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var ExchangeCacheUpdaterInterface $cacheUpdater
         */
        $cacheUpdater = $this->getContainer()->get('tagcade_app.service.core.exchange.exchange_cache_updater');
        $preName = $input->getOption('preName');
        $newName = $input->getOption('newName');

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(
            sprintf('This will remove "%s" from all RTB transaction while enable "%s". Continue ? (y/n)', $preName, $newName),
            false
        );

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        try {
            $cacheUpdater->updateCacheAfterUpdateExchangeParameter($preName, $newName);
        } catch(\Exception $ex) {
            $output->writeln(sprintf('<error>%s</error>', $ex->getMessage()));
            return;
        }

        $output->writeln('<info>DONE!</info>');
    }
}