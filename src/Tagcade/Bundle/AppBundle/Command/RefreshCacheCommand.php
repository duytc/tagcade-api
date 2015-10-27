<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Tagcade\Cache\ConfigurationCacheInterface;
use Tagcade\DomainManager\RonAdSlotManagerInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\RonAdTagInterface;

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
            ->setDescription('Refresh ad slot cache and configuration cache if needed to avoid slams');
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
        $command = $this->getApplication()->find('tc:cache:refresh-config');

        $arguments = array(
            'command' => 'tc:cache:refresh-config',
        );

        $greetInput = new ArrayInput($arguments);
        $command->run($greetInput, $output);

        $command2 = $this->getApplication()->find('tc:cache:refresh-adslots');
        $command2->run(new ArrayInput(array('command'=>'tc:cache:refresh-adslots')), $output);

        $output->writeln('all cache is now refreshed');

    }
}
