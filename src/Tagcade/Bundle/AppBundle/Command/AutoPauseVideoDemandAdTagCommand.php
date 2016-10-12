<?php


namespace Tagcade\Bundle\AppBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class AutoPauseVideoDemandAdTagCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:video-demand-ad-tag:auto-pause')
            ->addOption('partner', 'p', InputOption::VALUE_OPTIONAL, 'id of video demand partner to pause')
            ->addOption('publisher', 'u', InputOption::VALUE_OPTIONAL, 'id of publisher to pause')
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'try to auto pause all video demand ad tags')
            ->setDescription('Pause all video demand ad tags that fails its placement rules');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $logger = $container->get('logger');
        $videoDemandPartnerManager = $container->get('tagcade.domain_manager.video_demand_partner');
        $publisherManager = $container->get('tagcade_user.domain_manager.publisher');
        $libraryVideoDemandAdTagManager = $container->get('tagcade.domain_manager.library_video_demand_ad_tag');
        $autoPauseService = $container->get('tagcade_app.service.core.video_demand_ad_tag.auto_pause_service');
        $partner = $input->getOption('partner');
        $pub = $input->getOption('publisher');
        $all = filter_var($input->getOption('all'), FILTER_VALIDATE_BOOLEAN);

        $demandAdTags = [];
        if ($all === true) {
            $demandAdTags = $libraryVideoDemandAdTagManager->all();
            $logger->info('try to auto pause all Video Demand Ad Tag');
        } else if ($pub !== null) {
            $publisher = $publisherManager->find($pub);
            if (!$publisher instanceof PublisherInterface) {
                throw new InvalidArgumentException(sprintf('publisher %d does not exist', $pub));
            }
            $demandAdTags = $libraryVideoDemandAdTagManager->getLibraryVideoDemandAdTagsForPublisher($publisher);
            $logger->info(sprintf('try to auto pause all Video Demand Ad Tag which belongs to publisher %d', $pub));
        } else if ($partner !== null) {
            $videoDemandPartner = $videoDemandPartnerManager->find($partner);
            if (!$videoDemandPartner instanceof VideoDemandPartnerInterface) {
                throw new InvalidArgumentException(sprintf('Video Demand Partner %d does not exist', $partner));
            }
            $demandAdTags = $libraryVideoDemandAdTagManager->getLibraryVideoDemandAdTagsForDemandPartner($videoDemandPartner);
            $logger->info(sprintf('try to auto pause all Video Demand Ad Tag which belongs to partner %d', $partner));
        } else {
            $output->writeln('<error>You must either use one of the following options : --all, --publisher, --partner</error>');
            return;
        }
        $count = $autoPauseService->autoPauseLibraryDemandAdTags($demandAdTags);
        $logger->info(sprintf('There are %d ad tags get paused', $count));
    }
}