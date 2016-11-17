<?php

namespace Tagcade\Bundle\AppBundle\Command;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Entity\Core\LibrarySlotTag;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\LibrarySlotTagInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Repository\Core\AdSlotRepositoryInterface;
use Tagcade\Repository\Core\LibrarySlotTagRepositoryInterface;
use Tagcade\Service\TagLibrary\ReplicatorInterface;

class VerifySlotSynchronizationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:ron-slot-sync:verify')
            ->addOption('id', 'i', InputOption::VALUE_OPTIONAL, 'the ron slot id')
            ->setDescription('verify if the ron slot is in sync with its co-referenced');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $ronSlotManager = $container->get('tagcade.domain_manager.ron_ad_slot');
        $adSlotRepository = $container->get('tagcade.repository.ad_slot');
        $id = $input->getOption('id');
        $ronAdSlots = [];
        if ($id !== null) {
            $ronSlot = $ronSlotManager->find($id);
            if (!$ronSlot instanceof RonAdSlotInterface) {
                throw new InvalidArgumentException(sprintf('not found any RON ad slot with id %s', $id));
            }
            $ronAdSlots[] = $ronSlot;
        } else {
            $ronAdSlots = $ronSlotManager->all();
        }

        /**
         * @var RonAdSlotInterface $ronSlot
         */
        foreach($ronAdSlots as $ronSlot) {
            $this->verifySingleRonSlot($em, $ronSlot, $adSlotRepository, $output);
        }
    }

    protected function verifySingleRonSlot(EntityManagerInterface $em, RonAdSlotInterface $ronSlot, AdSlotRepositoryInterface $adSlotRepository, OutputInterface $output)
    {
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
                $output->writeln(sprintf('<error>RON slot %d has one of its children out of sync</error>', $ronSlot->getId()));

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
        $librarySlotTagRepository = $em->getRepository(LibrarySlotTag::class);
        $libSlotTags = $librarySlotTagRepository->getByLibraryAdSlot($adSlot->getLibraryAdSlot());
        usort($libSlotTags, function(LibrarySlotTagInterface $a, LibrarySlotTagInterface $b) {
            return strcmp($a->getRefId(), $b->getRefId());
        });

        $adTags = $adSlot->getAdTags()->toArray();
        usort($adTags, function(AdTagInterface $a, AdTagInterface $b) {
            return strcmp($a->getRefId(), $b->getRefId());
        });
        $count = 0;
        /**
         * @var AdTagInterface $tag
         */
        foreach($adTags as $index => $tag) {
            $librarySlotTag = $libSlotTags[$index];
            $tag->setLibraryAdTag($librarySlotTag->getLibraryAdTag());
            $tag->setFrequencyCap($librarySlotTag->getFrequencyCap());
            $tag->setPosition($librarySlotTag->getPosition());
            $tag->setRotation($librarySlotTag->getRotation());
            $tag->setActive($librarySlotTag->isActive());
            $tag->setImpressionCap($librarySlotTag->getImpressionCap());
            $tag->setNetworkOpportunityCap($librarySlotTag->getNetworkOpportunityCap());
            $em->merge($tag);
            $count++;
            if ($count % 100 == 0) {
                $em->flush();
            }
        }

        $em->flush();
    }
}