<?php

namespace Tagcade\Bundle\AppBundle\Command;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Model\Core\RonAdSlotInterface;

class VerifySlotSynchronizationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:ron-slot-sync:verify')
            ->addOption('id', 'i', InputOption::VALUE_OPTIONAL, 'the ron slot id')
            ->addOption('timeout', 't', InputOption::VALUE_OPTIONAL, 'Timeout (in seconds) to process for each publisher or ad network. Set to -1 to disable timeout', -1)
            ->setDescription('verify if the ron slot is in sync with its co-referenced');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $ronSlotManager = $container->get('tagcade.domain_manager.ron_ad_slot');
        /** @var \Psr\Log\LoggerInterface $logger */
        $logger = $container->get('logger');
        $timeout = $input->getOption('timeout');
        if ($timeout == -1) {
            $timeout = null;
        }

        $id = $input->getOption('id');

        if ($id !== null) {
            $ronSlot = $ronSlotManager->find($id);
            if (!$ronSlot instanceof RonAdSlotInterface) {
                throw new InvalidArgumentException(sprintf('not found any RON ad slot with id %s', $id));
            }
            $this->verifySingleRonSlot($em, $ronSlot, $output);
            return;
        }

        $ronAdSlots = $ronSlotManager->all();

        /**
         * @var RonAdSlotInterface $ronSlot
         */
        foreach($ronAdSlots as $ronSlot) {
            $cmd = sprintf('%s tc:ron-slot-sync:verify --id %d', $this->getAppConsoleCommand(), $ronSlot->getId());
            $this->executeProcess($process = new Process($cmd), ['timeout' => $timeout], $logger);
        }
    }

    protected function verifySingleRonSlot(EntityManagerInterface $em, RonAdSlotInterface $ronSlot, OutputInterface $output)
    {
        $adSlotRepository = $em->getRepository(AdSlotAbstract::class);

        $libraryAdSlot = $ronSlot->getLibraryAdSlot();
        $coReferencedAdSlots = $adSlotRepository->getCoReferencedAdSlots($libraryAdSlot);
        if ($coReferencedAdSlots instanceof PersistentCollection) {
            $coReferencedAdSlots = $coReferencedAdSlots->toArray();
        }

        $count = 0;
        /**
         * @var BaseAdSlotInterface $slot
         */
        foreach($coReferencedAdSlots as $slot) {
            if ($libraryAdSlot->checkSum() != $slot->checkSum()) {
                $output->writeln(sprintf('<error>RON slot %d has its child, ad slot %d, out of sync</error>', $ronSlot->getId(), $slot->getId()));

                $output->writeln(sprintf('<info>start fixing the bad ad slot %d</info>', $slot->getId()));
                $this->fixSingleAdSlot($em, $slot);
                $output->writeln(sprintf('<info>finish fixing the bad ad slot %d</info>', $slot->getId()));
                $count++;
            }
        }

        if ($count === 0) {
            $output->writeln(sprintf('<info>RON ad slot %d are in sync with its children</info>', $ronSlot->getId()));
        }
    }

    protected function fixSingleAdSlot(EntityManagerInterface $em, BaseAdSlotInterface $adSlot)
    {
        $em->getConnection()->beginTransaction();
        try {
            $libAdSlot = $adSlot->getLibraryAdSlot();
            $librarySlotTags = $libAdSlot->getLibSlotTags();

            // remove old ad tags
            $adTags = $adSlot->getAdTags();
            foreach ($adTags as $t) {
                $em->remove($t);
            }
            $adSlot->getAdTags()->clear();

            // add new ad tags
            /** @var LibrarySlotTagInterface $librarySlotTag */
            foreach ($librarySlotTags as $librarySlotTag) {
                $newAdTag = new AdTag();
                $newAdTag->setAdSlot($adSlot)
                    ->setRefId($librarySlotTag->getRefId())
                    ->setLibraryAdTag($librarySlotTag->getLibraryAdTag())
                    ->setFrequencyCap($librarySlotTag->getFrequencyCap())
                    ->setPosition($librarySlotTag->getPosition())
                    ->setRotation($librarySlotTag->getRotation())
                    ->setActive($librarySlotTag->isActive())
                    ->setImpressionCap($librarySlotTag->getImpressionCap())
                    ->setNetworkOpportunityCap($librarySlotTag->getNetworkOpportunityCap())
                ;
                $adSlot->getAdTags()->add($newAdTag);
            }

            $em->persist($adSlot);
            $em->flush();

            $em->getConnection()->commit();
        } catch (\Exception $ex) {
            $em->getConnection()->rollback();
            throw new RuntimeException($ex);
        }
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

        try {
            $process->mustRun(function($type, $buffer) use($logger) {
                if (Process::ERR === $type) {
                    $logger->error($buffer);
                } else {
                    $logger->info($buffer);
                }
            }
            );
        } catch (ProcessFailedException $ex) {
            throw $ex;
        }
    }
}