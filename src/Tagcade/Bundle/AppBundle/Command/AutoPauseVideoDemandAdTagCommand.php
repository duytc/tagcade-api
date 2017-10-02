<?php


namespace Tagcade\Bundle\AppBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Entity\Core\VideoDemandAdTag;
use Tagcade\Model\Core\VideoDemandAdTagInterface;

class AutoPauseVideoDemandAdTagCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:video-demand-ad-tag:auto-pause')
            ->setDescription('Do pause all video demand ad tags that have reached its request cap or impression cap per day');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $logger = $container->get('logger');
        $videoDemandAdTagManager = $container->get('tagcade.domain_manager.video_demand_ad_tag');
        $eventCounter = $container->get('tagcade.service.report.video_report.counter.cache_event_counter');
        $demandAdTags = $videoDemandAdTagManager->getVideoDemandAdTagsHaveRequestCapByStatus(VideoDemandAdTag::ACTIVE);
        $em = $container->get('doctrine.orm.entity_manager');
        $pausedAdTags = 0;
        /**
         * @var VideoDemandAdTagInterface $adTag
         */
        foreach($demandAdTags as $adTag) {
            $requestCap = $adTag->getRequestCap();
            $impressionCap = $adTag->getImpressionCap();

            if (($requestCap == null || $requestCap < 1) && ($impressionCap == null || $impressionCap < 1)) {
                continue; // ignore video demand tags that do not set both request cap and impression cap
            }

            if (($requestCap > 0 && $eventCounter->getVideoDemandAdTagRequestsCount($adTag->getId()) >= $requestCap)
                || ($impressionCap > 0 && $eventCounter->getVideoDemandAdTagImpressionsCount($adTag->getId()) >= $impressionCap)
            ) {
                $adTag->setActive(VideoDemandAdTag::AUTO_PAUSED);
                $pausedAdTags++;
                $em->merge($adTag);
                continue;
            }
        }
        $em->flush();
        $logger->info(sprintf('There are %d ad tags get paused', $pausedAdTags));
    }
}