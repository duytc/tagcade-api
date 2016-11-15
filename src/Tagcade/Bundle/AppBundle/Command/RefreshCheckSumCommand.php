<?php

namespace Tagcade\Bundle\AppBundle\Command;


use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;

class RefreshCheckSumCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:check-sum:refresh')
            ->addOption('id', 'i', InputOption::VALUE_REQUIRED, 'publisher id')
            ->setDescription('refresh check sum value for ad tag, ad slot of an specific publisher');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $logger = $container->get('logger');
        $id = $input->getOption('id');
        $publisherManager = $container->get('tagcade_user.domain_manager.publisher');
        $siteManager = $container->get('tagcade.domain_manager.site');

        $publisher = $publisherManager->find($id);

        if (!$publisher instanceof PublisherInterface) {
            throw new InvalidArgumentException(sprintf('not found any publisher with id %s', $id));
        }

        $logger->info(sprintf('Start refresh check sum for publisher %s', $publisher->getUsername()));
        $em = $container->get('doctrine.orm.entity_manager');
        $sites = $siteManager->getSitesForPublisher($publisher);
        foreach ($sites as $site) {
            $this->refreshForSite($site, $logger, $em);
        }
        $logger->info(sprintf('Finish refresh check sum for publisher %s', $publisher->getUsername()));
    }

    protected function refreshForSite(SiteInterface $site, LoggerInterface $logger, EntityManagerInterface $em)
    {
        $logger->info(sprintf('start refresh check sum for site %s', $site->getDomain()));

        $adSlots = $site->getAllAdSlots();
        /**
         * @var BaseAdSlotInterface $adSlot
         */
        foreach ($adSlots as $adSlot) {
            $adTags = $adSlot->getAdTags();
            /**
             * @var AdTagInterface $adTag
             */
            foreach ($adTags as $adTag) {
                $adTag->setCheckSum();
                $em->merge($adTag);
            }
        }

        $em->flush();
        $logger->info(sprintf('finish refresh check sum for site %s', $site->getDomain()));
    }
}