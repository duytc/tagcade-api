<?php

namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\RonAdSlotInterface;

class VerifySlotSynchronizationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:verify-sync:ron-ad-slot')
            ->addOption('id', 'i', InputOption::VALUE_OPTIONAL, 'the ron slot id')
            ->setDescription('verify if the ron slot is in sync with its co-referenced');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $ronSlotManager = $container->get('tagcade.domain_manager.ron_ad_slot');
        $validator = $container->get('tagcade_api.service.tag_library.checksum_validator');
        $id = $input->getOption('id');
        $ronAdSlots = [];
        if ($id !== null) {
            $slot = $ronSlotManager->find($id);
            if (!$slot instanceof RonAdSlotInterface) {
                throw new InvalidArgumentException(sprintf('not found any RON ad slot with id %s', $id));
            }
            $ronAdSlots[] = $slot;
        } else {
            $ronAdSlots = $ronSlotManager->all();
        }

        $errorCount = 0;
        /**
         * @var RonAdSlotInterface $adSlot
         */
        foreach($ronAdSlots as $adSlot) {
            $libraryAdSlot = $adSlot->getLibraryAdSlot();
            $adSlots = $libraryAdSlot->getAdSlots()->toArray();
            try {
                $validator->validateAllAdSlotsSynchronized($adSlots);
            } catch (RuntimeException $ex) {
                $errorCount++;
                $output->writeln(sprintf('<error>RON ad slot %d is not in sync with its co-referenced</error>', $adSlot->getId()));
            }
        }

        if ($errorCount === 0) {
            $output->writeln('<info>All RON ad slot are in sync</info>');
        }
    }
}