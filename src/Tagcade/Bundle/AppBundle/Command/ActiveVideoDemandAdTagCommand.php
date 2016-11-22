<?php


namespace Tagcade\Bundle\AppBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Entity\Core\VideoDemandAdTag;
use Tagcade\Model\Core\VideoDemandAdTagInterface;

class ActiveVideoDemandAdTagCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:video-demand-ad-tag:active')
            ->setDescription('active all video demand ad tags that have been auto paused when it had reached its request cap per day before');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $logger = $container->get('logger');
        $videoDemandAdTagManager = $container->get('tagcade.domain_manager.video_demand_ad_tag');
        $demandAdTags = $videoDemandAdTagManager->getVideoDemandAdTagsByStatus(VideoDemandAdTag::AUTO_PAUSED);
        $em = $container->get('doctrine.orm.entity_manager');
        $activatedAdTags = 0;
        /**
         * @var VideoDemandAdTagInterface $adTag
         */
        foreach($demandAdTags as $adTag) {
            $adTag->setActive(VideoDemandAdTag::ACTIVE);
            $activatedAdTags++;
            $em->merge($adTag);
        }
        $em->flush();
        $logger->info(sprintf('There are %d ad tags get active', $activatedAdTags));
    }
}