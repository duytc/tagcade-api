<?php

namespace Tagcade\Bundle\AppBundle\Command;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Entity\Core\AdSlotAbstract;
use Tagcade\Entity\Core\AdTag;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;

class VerifySlotSynchronizationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:ad-slot-sync:verify')
            ->addOption('id', 'i', InputOption::VALUE_OPTIONAL, 'the ad slot id')
            ->setDescription('verify if the ad slot is in sync with its co-referenced');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $libSlotManager = $container->get('tagcade.domain_manager.library_ad_slot');
        $id = $input->getOption('id');
        $libSlots = [];
        if ($id !== null) {
            $libSlot = $libSlotManager->find($id);
            if (!$libSlot instanceof BaseLibraryAdSlotInterface) {
                throw new InvalidArgumentException(sprintf('not found any library ad slot with id %s', $id));
            }
            $libSlots[] = $libSlot;
        } else {
            $libSlots = $libSlotManager->all();
        }

        /**
         * @var BaseLibraryAdSlotInterface $libSlot
         */
        foreach($libSlots as $libSlot) {
            if ($libSlot instanceof LibraryDynamicAdSlotInterface) {
                continue;
            }

            $this->verifySingleRonSlot($em, $libSlot, $output);
        }
    }

    protected function verifySingleRonSlot(EntityManagerInterface $em, BaseLibraryAdSlotInterface $libraryAdSlot, OutputInterface $output)
    {
        $adSlotRepository = $em->getRepository(AdSlotAbstract::class);

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
                $output->writeln(sprintf('<error>library slot %d has its child, ad slot %d, out of sync</error>', $libraryAdSlot->getId(), $slot->getId()));

                $output->writeln(sprintf('<info>start fixing the bad ad slot %d</info>', $slot->getId()));
                $this->fixSingleAdSlot($em, $slot);
                $output->writeln(sprintf('<info>finish fixing the bad ad slot %d</info>', $slot->getId()));
                $count++;
            }
        }

        if ($count === 0) {
            $output->writeln(sprintf('<info>library ad slot %d are in sync with its children</info>', $libraryAdSlot->getId()));
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
}