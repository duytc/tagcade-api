<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\DomainManager\VideoDemandAdTagManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\VideoDemandAdTag;
use Tagcade\Model\Core\VideoDemandPartnerInterface;

class UpdateVideoDemandAdTagStatusCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:video-demand-ad-tag:status:update')
            ->addArgument('video-demand-partner', InputArgument::REQUIRED, 'The video demand partner whose video demand tags is being update')
            ->addOption('status', null, InputOption::VALUE_REQUIRED, 'status of video demand tags')
            ->setDescription('update all video demand tags status of given video demand partner by the given status');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $videoDemandPartnerId = $input->getArgument('video-demand-partner');

        $videoDemandPartnerManager = $this->getContainer()->get('tagcade.domain_manager.video_demand_partner');
        $videoDemandPartner = $videoDemandPartnerManager->find($videoDemandPartnerId);
        if (!$videoDemandPartner instanceof VideoDemandPartnerInterface) {
            throw new InvalidArgumentException(sprintf('the video demand partner "%d" does not exist'));
        }

        $status = filter_var($input->getOption('status'), FILTER_VALIDATE_INT);
        if (false === $status || !in_array($status, VideoDemandAdTag::$SUPPORTED_STATUS)) {
            throw new InvalidArgumentException(sprintf('invalid status "%d"'));
        }

        /** @var VideoDemandAdTagManagerInterface $videoDemandAdTagManager */
        $videoDemandAdTagManager = $this->getContainer()->get('tagcade.domain_manager.video_demand_ad_tag');
        $videoDemandAdTagManager->updateVideoDemandAdTagForDemandPartner($videoDemandPartner, $status);
    }
}