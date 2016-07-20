<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Provides a command-line interface for renewing cache using cli
 */
class RefreshCacheCommand extends ContainerAwareCommand
{
    /**
     * Configure the CLI task
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('tc:cache:refresh-all')
            ->setDescription('Refresh ad slot cache and configuration cache if needed to avoid slams');;
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
        $refreshConfigCommand = $this->getApplication()->find('tc:cache:refresh-config');
        $environment = $this->getContainer()->getParameter('kernel.environment');
        $debug = $this->getContainer()->getParameter('kernel.debug');

        $refreshConfigArguments = array(
            'command'       => 'tc:cache:refresh-config',
            '--env'         => $environment,
            '--no-debug'    => $debug,
        );
        $refreshConfigInput = new ArrayInput($refreshConfigArguments);
        $refreshConfigCommand->run($refreshConfigInput, $output);

        $refreshAdSlotsCommand = $this->getApplication()->find('tc:cache:refresh-adslots');
        $refreshAdSlotsArgument = array (
            'command'       => 'tc:cache:refresh-adslots',
            '--env'         => $environment,
            '--no-debug'    => $debug,
        );

        $refreshAdSlotInput = new ArrayInput($refreshAdSlotsArgument);
        $refreshAdSlotsCommand->run($refreshAdSlotInput, $output);

        $output->writeln('all cache is now refreshed');

    }
}
