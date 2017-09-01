<?php

namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class MigrationRemoveVRSettingForPublishersCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:migration:publisher:settings:video-report:remove')
            ->setDescription('Remove setting value of video report for all publishers');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $publisherManager = $container->get('tagcade_user.domain_manager.publisher');
        $em = $container->get('doctrine.orm.entity_manager');

        $publishers = $publisherManager->all();
        $output->writeln(sprintf('Start removing setting value of video report for %d publishers', count($publishers)));

        // do update
        $updatedCounts = 0;

        /** @var PublisherInterface $publisher */
        foreach ($publishers as $publisher) {
            $settings = $publisher->getSettings();

            if (!is_array($settings)) {
                continue;
            }

            foreach ($settings as &$view) {
                foreach ($view as &$viewItem) {
                    if (array_key_exists('videoReport', $viewItem) && !empty($viewItem['videoReport'])) {
                        unset($viewItem['videoReport']);
                    }
                }
            }

            $updatedCounts++;

            $publisher->setSettings($settings);

            $em->merge($publisher);
        }

        if ($updatedCounts > 0) {
            $em->flush();
        }

        $output->writeln(sprintf('Done. %d publishers got updated', $updatedCounts));
    }
}