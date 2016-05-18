<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Tagcade\Service\Core\Exchange\ExchangeCacheUpdaterInterface;

class RemoveExchangeCommand extends ContainerAwareCommand
{
    /**
     * Configure the CLI task
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('tc:exchange:remove')
            ->addArgument('name', InputArgument::REQUIRED, 'Abbreviation name in the parameter configuration')
            ->setDescription('Update all existing caches when an exchange is removed from parameter');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var ExchangeCacheUpdaterInterface $cacheUpdater
         */
        $cacheUpdater = $this->getContainer()->get('tagcade_app.service.core.exchange.exchange_cache_updater');
        $name = $input->getArgument('name');

        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(
            sprintf('This will remove "%s" from all RTB transaction. Continue ? (y/n)', $name),
            false
        );

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        try {
            $cacheUpdater->updateCacheAfterUpdateExchangeParameter($name);
        } catch (\Exception $ex) {
            $output->writeln(sprintf('<error>%s</error>', $ex->getMessage()));
            return;
        }

        $output->writeln('<info>DONE!</info>');
    }
}