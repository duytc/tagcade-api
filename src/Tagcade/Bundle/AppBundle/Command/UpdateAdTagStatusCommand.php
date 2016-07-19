<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdNetworkInterface;

class UpdateAdTagStatusCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:ad-tag-status:update')
            ->addArgument('ad-network', InputArgument::REQUIRED, 'The ad network whose ad tags is being update')
            ->addOption('status', null, InputOption::VALUE_REQUIRED, 'status of ad tags')
            ->setDescription('update all ad tags status of given ad network by the given status');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $adNetworkId = $input->getArgument('ad-network');
        $adNetworkManager = $this->getContainer()->get('tagcade.domain_manager.ad_network');
        $adNetwork = $adNetworkManager->find($adNetworkId);
        if (!$adNetwork instanceof AdNetworkInterface) {
            throw new InvalidArgumentException(sprintf('the ad network "%d" does not exist'));
        }

        $status = filter_var($input->getOption('status'), FILTER_VALIDATE_INT);

        /** @var AdTagManagerInterface $adTagManager */
        $adTagManager = $this->getContainer()->get('tagcade.domain_manager.ad_tag');
        $adTagManager->updateAdTagStatusForAdNetwork($adNetwork, $status);
    }
}