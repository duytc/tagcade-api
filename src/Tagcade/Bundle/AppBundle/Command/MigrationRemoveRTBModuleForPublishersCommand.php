<?php

namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class MigrationRemoveRTBModuleForPublishersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:migration:publisher:module:rtb:remove')
            ->setDescription('Do activate for all ad tags that get paused by exceeding its impression or opportunity cap setting value');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $publisherManager = $container->get('tagcade_user.domain_manager.publisher');
        $em = $container->get('doctrine.orm.entity_manager');

        $publishers = $publisherManager->all();
        $output->writeln(sprintf('Start removing RTB module for %d publishers', count($publishers)));

        // do update
        $updatedCounts = 0;

        /** @var PublisherInterface $publisher */
        foreach ($publishers as $publisher) {
            $modules = $publisher->getEnabledModules();
            if (!is_array($modules)) {
                continue;
            }

            $foundOffset = array_search('MODULE_RTB', $modules);
            if (false === $foundOffset) {
                continue;
            }

            $updatedCounts++;

            array_splice($modules, $foundOffset, 1);
            $publisher->setEnabledModules($modules);

            $em->merge($publisher);
        }

        if ($updatedCounts > 0) {
            $em->flush();
        }

        $output->writeln(sprintf('Done. %d publishers got updated', $updatedCounts));
    }
}