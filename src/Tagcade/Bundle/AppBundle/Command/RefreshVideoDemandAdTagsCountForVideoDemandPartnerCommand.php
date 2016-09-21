<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;

/**
 * Provides a command-line interface for generating and assigning uuid for all publisher
 */
class RefreshVideoDemandAdTagsCountForVideoDemandPartnerCommand extends ContainerAwareCommand
{
    /**
     * Configure the CLI task
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('tc:video-demand-partner:refresh-demand-ad-tag-count')
            ->setDescription('Recalculate active and paused ad tags for ad networks')
            ->addArgument(
                'id',
                InputArgument::OPTIONAL,
                'The video demand partner id to be updated'
            )
            ->addOption(
                'all',
                'a',
                InputOption::VALUE_NONE,
                'If set, all video demand partners will be updated'
            );
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
        $count = 0;
        $videoDemandPartnerManager = $this->getContainer()->get('tagcade.domain_manager.video_demand_partner');
        if ($input->getOption('all')) {
            $allDemandPartners = $videoDemandPartnerManager->all();
            /** @var VideoDemandPartnerInterface $demandPartner */
            foreach ($allDemandPartners as $demandPartner) {
                $this->recalculateAdTagCountForAdNetwork($demandPartner);
                $count++;
            }

            $output->writeln(sprintf('%d video demand partner(s) have been updated', $count));
        } else {
            $id = $input->getArgument('id');

            if ($id) {
                $demandPartner = $videoDemandPartnerManager->find(filter_var($id, FILTER_VALIDATE_INT));

                if ($demandPartner instanceof VideoDemandPartnerInterface) {
                    $this->recalculateAdTagCountForAdNetwork($demandPartner);
                    $count++;
                    $output->writeln(sprintf('%d video demand partner(s) have been updated', $count));
                } else {
                    $output->writeln('<error>The AdNetwork does not exist</error>');
                }
            } else {
                $output->writeln('<question>Are you missing {id} or --all option ?"</question>');
                $output->writeln('<question>Try "php app/console tc:video-demand-partner:refresh-demand-ad-tag-count {id}"</question>');
                $output->writeln('<question>Or "php app/console tc:video-demand-partner:refresh-demand-ad-tag-count --all"</question>');
            }
        }
    }

    private function recalculateAdTagCountForAdNetwork(VideoDemandPartnerInterface $demandPartner)
    {
        $videoDemandPartnerManager = $this->getContainer()->get('tagcade.domain_manager.video_demand_partner');
        $adTags = $demandPartner->getVideoDemandAdTags();
        $activeCount = count(array_filter($adTags, function (VideoDemandAdTagInterface $adTag) {
            return $adTag->getActive();
        }));

        $pausedCount = count(array_filter($adTags, function (VideoDemandAdTagInterface $adTag) {
            return !$adTag->getActive();
        }));

        $demandPartner->setActiveAdTagsCount($activeCount);
        $demandPartner->setPausedAdTagsCount($pausedCount);
        $videoDemandPartnerManager->save($demandPartner);
    }
}
