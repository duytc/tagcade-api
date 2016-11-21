<?php


namespace Tagcade\Bundle\AppBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Entity\Core\VideoDemandAdTag;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class AutoPauseVideoDemandAdTagCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:video-demand-ad-tag:auto-pause')
            ->setDescription('Do pause all video demand ad tags that have reached its request cap per day');
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
            if ($eventCounter->getVideoDemandAdTagRequestsCount($adTag->getId()) >= $adTag->getRequestCap()) {
                $adTag->setActive(VideoDemandAdTag::AUTO_PAUSED);
                $pausedAdTags++;
                $em->merge($adTag);
            }
        }
        $em->flush();
        $logger->info(sprintf('There are %d ad tags get paused', $pausedAdTags));
    }
}