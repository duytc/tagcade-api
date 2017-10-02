<?php

namespace Tagcade\Bundle\AppBundle\Command;


use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Tagcade\Model\Core\VideoDemandAdTag;
use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;

class AutoPauseVideoDemandPartnerCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:video-demand-partner:auto-pause')
            ->addOption('videoDemandPartner', 'd', InputOption::VALUE_OPTIONAL, 'video demand partner id to validate and do auto pause.')
            ->setDescription('Do pause for video demand partners that have reached request cap or impression cap per day');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $videoDemandPartner = $input->getOption('videoDemandPartner');
        $container = $this->getContainer();
        /** @var LoggerInterface $logger */
        $logger = $container->get('logger');
        $videoDemandPartnerManager = $container->get('tagcade.domain_manager.video_demand_partner');
        $videoDemandAdTagManager = $container->get('tagcade.domain_manager.video_demand_ad_tag');
        $videoCacheEventCounter = $container->get('tagcade.service.report.video_report.counter.cache_event_counter');
        $autoPauseForVideoDemandPartners = [];

        if ($videoDemandPartner != null) {
            $videoDemandPartner = $videoDemandPartnerManager->find($videoDemandPartner);
            if (!$videoDemandPartner instanceof VideoDemandPartnerInterface) {
                throw new \Exception(sprintf('Not found that video demand partner %s', $videoDemandPartner));
            }

            $autoPauseForVideoDemandPartners[] = $videoDemandPartner;
        }

        if (count($autoPauseForVideoDemandPartners) < 1) {
            $autoPauseForVideoDemandPartners = $videoDemandPartnerManager->allHasCap();
        }

        $pausedVideoDemandPartnersCount = 0;
        foreach ($autoPauseForVideoDemandPartners as $vdp) {
            /**
             * @var VideoDemandPartnerInterface $vdp
             */
            $videoDemandTags = $videoDemandAdTagManager->getVideoDemandAdTagsForDemandPartner($vdp);
            if (count($videoDemandTags) < 1) {
                continue;
            }

            $requestCap = $vdp->getRequestCap();
            $impressionCap = $vdp->getImpressionCap();

            if (($requestCap == null || $requestCap < 1) && ($impressionCap == null || $impressionCap < 1)) {
                continue; // ignore video demand partners that do not set both request cap and impression cap
            }

            $logger->info(sprintf('Checking request cap and impression cap for video demand partner %d', $vdp->getId()));

            $requestCount = 0;
            $impressionCount = 0;
            foreach ($videoDemandTags as $videoDemandTag) {
                /** @var VideoDemandAdTagInterface $videoDemandTag */
                $requestCount += $videoCacheEventCounter->getVideoDemandAdTagRequestsCount($videoDemandTag->getId());
                $impressionCount += $videoCacheEventCounter->getVideoDemandAdTagImpressionsCount($videoDemandTag->getId());
            }

            if (($requestCap > 0 && $requestCap <= $requestCount) || ($impressionCap > 0 && $impressionCap <= $impressionCount)) {
                $logger->info(sprintf('Video Demand Partner %d will be PAUSED shortly', $vdp->getId()));
                $cmd = sprintf('%s tc:video-demand-ad-tag:status:update %d --status=%d', $this->getAppConsoleCommand(), $vdp->getId(), VideoDemandAdTag::AUTO_PAUSED);
                $this->executeProcess($process = new Process($cmd), ['timeout' => 200], $logger);

                $pausedVideoDemandPartnersCount++;
            } else {
                $logger->info(sprintf('Video Demand Partner %d is still RUNNING with (request cap %d, current request %d) and (impression cap %d, current impressions %d)', $vdp->getId(), $requestCap, $requestCount, $impressionCap, $impressionCount));
            }
        }

        $logger->info(sprintf('There are %d video demand partners get paused', $pausedVideoDemandPartnersCount));
    }

    protected function getAppConsoleCommand()
    {
        $pathToSymfonyConsole = $this->getContainer()->getParameter('kernel.root_dir');
        $environment = $this->getContainer()->getParameter('kernel.environment');
        $debug = $this->getContainer()->getParameter('kernel.debug');

        $command = sprintf('php %s/console --env=%s', $pathToSymfonyConsole, $environment);

        if (!$debug) {
            $command .= ' --no-debug';
        }

        return $command;
    }

    protected function executeProcess(Process $process, array $options, LoggerInterface $logger)
    {
        if (array_key_exists('timeout', $options)) {
            $process->setTimeout($options['timeout']);
        }

        $process->mustRun(function ($type, $buffer) use ($logger) {
            if (Process::ERR === $type) {
                $logger->error($buffer);
            } else {
                $logger->info($buffer);
            }
        });
    }
}