<?php

namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Model\Core\AdTagInterface;

class ActivateAdTagCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:ad-tags:activate')
            ->setDescription('Do activate for all ad tags that get paused by exceeding its impression or opportunity cap setting value');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $logger = $container->get('logger');
        $adTagManager = $container->get('tagcade.domain_manager.ad_tag');
        $em = $container->get('doctrine.orm.entity_manager');
        $adTags = $adTagManager->getAllAdTagsByStatus(AdTagInterface::AUTO_PAUSED);

        /** @var AdTagInterface $adTag */
        foreach ($adTags as $adTag) {
            $adTag->setActive(AdTagInterface::ACTIVE);
            $em->merge($adTag);
        }

        $em->flush();

        $logger->info(sprintf('There are %d ad tags get activated', count($adTags)));
    }
}