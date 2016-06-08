<?php

namespace Tagcade\Bundle\AppBundle\Command;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Model\Core\AdTagInterface;

class AutoPauseAdTagCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:ad-tag:do-auto-pause')
            ->setDescription('Do pause all ad tags that have reached its impression cap and network opportunity cap per day');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $adTagManager = $container->get('tagcade.domain_manager.ad_tag');
        /**
         * @var EntityManagerInterface $em
         */
        $em = $container->get('doctrine.orm.entity_manager');
        $eventCounter = $container->get('tagcade.service.report.performance_report.display.counter.cache_event_counter');
        $adTags = $adTagManager->getAdTagsThatSetImpressionAndOpportunityCapByStatus(AdTagInterface::ACTIVE);
        $pausedAdTags = 0;

        /** @var AdTagInterface $adTag */
        foreach ($adTags as $adTag) {

            if ($adTag->getNetworkOpportunityCap() <= $eventCounter->getOpportunityCount($adTag->getId()) ||
                $adTag->getImpressionCap() <= $eventCounter->getImpressionCount($adTag->getId())
            ) {
                $adTag->setActive(AdTagInterface::AUTO_PAUSED);
                $em->merge($adTag);

                $pausedAdTags++;
            }
        }

        $em->flush();
        if ($pausedAdTags === 0) {
            $output->writeln('There is no ad tags reaching its impression cap or network opportunity cap');
            return;
        }

        $output->writeln(sprintf('There are %d ad tags get paused', $pausedAdTags));
    }
} 