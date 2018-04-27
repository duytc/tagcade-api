<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;

class UpdateAdTagStatusCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:ad-tag-status:update')
            ->addArgument('ad-network', InputArgument::REQUIRED, 'The ad network whose ad tags is being update')
            ->addOption('site', null, InputOption::VALUE_OPTIONAL, 'only update ad tag belong to this site')
            ->addOption('status', null, InputOption::VALUE_REQUIRED, 'status of ad tags')
            ->setDescription('update all ad tags status of given ad network by the given status');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $adNetworkId = $input->getArgument('ad-network');
        $siteId = $input->getOption('site');

        $siteManager = $this->getContainer()->get('tagcade.domain_manager.site');
        $adNetworkManager = $this->getContainer()->get('tagcade.domain_manager.ad_network');
        /** @var Logger $logger */
        $logger = $this->getContainer()->get('logger');

        $adNetwork = $adNetworkManager->find($adNetworkId);
        if (!$adNetwork instanceof AdNetworkInterface) {
            throw new InvalidArgumentException(sprintf('the ad network "%d" does not exist'));
        }

        $status = filter_var($input->getOption('status'), FILTER_VALIDATE_INT);

        /** @var AdTagManagerInterface $adTagManager */
        $adTagManager = $this->getContainer()->get('tagcade.domain_manager.ad_tag');

        try {
            if (!empty($siteId)) {
                $site = $siteManager->find($siteId);
                if (!$site instanceof SiteInterface) {
                    throw new InvalidArgumentException(sprintf('site "%d" does not exist'));
                }

                $adTagManager->updateActiveStateBySingleSiteForAdNetwork($adNetwork, $site, $status);
            } else {
                $adTagManager->updateAdTagStatusForAdNetwork($adNetwork, $status);
            }
            $logger->info(sprintf("Successfully updating ad tags status for ad network '%s' (ID: %s)", $adNetwork->getName(), $adNetwork->getId()));
        } catch (\Exception $e) {
            $logger->error($e);

            throw  $e;
        }
    }
}