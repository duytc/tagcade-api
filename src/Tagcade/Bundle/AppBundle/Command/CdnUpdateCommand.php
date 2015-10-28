<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Cdn\CDNUpdaterInterface;

/**
 * Provides a command-line interface for generating and assigning uuid for all publisher
 */
class CdnUpdateCommand extends ContainerAwareCommand
{

    /**
     * Configure the CLI task
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('tc:cdn:update')
            ->setDescription('Push the given adslots\'s data to the specified FTP server')
            ->addArgument(
                'ids',
                InputArgument::IS_ARRAY,
                'The ids of list ad slots to be pushed'
            )
            ->addOption(
                'all',
                null,
                InputOption::VALUE_NONE,
                'If set, all ad slots (even ron ad slot) will get pushed to FTP server'
            )
            ->addOption(
                'ron',
                null,
                InputOption::VALUE_REQUIRED,
                'Specify the ad slot is ron or not',
                false
            )
        ;
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
        /**
         * @var CDNUpdaterInterface $cdnUpdater
         */
        $cdnUpdater = $this->getContainer()->get('tagcade.service.cdn.cdn_updater');
        $isRon = filter_var($input->getOption('ron'), FILTER_VALIDATE_BOOLEAN);

        if ($isRon) {
            if ($input->getOption('all')) {
                $ronAdSlots = $this->getContainer()->get('tagcade.domain_manager.ron_ad_slot')->all();
                $ronAdSlots = array_map(function(RonAdSlotInterface $ronAdSlot) {
                    return $ronAdSlot->getId();
                }, $ronAdSlots);
            }
            else {
                $ronAdSlots = $input->getArgument('ids');
                if (count($ronAdSlots) < 1) {
                    $output->writeln(sprintf('<question>Are you missing some ids ?</question>'));
                    $output->writeln(sprintf('<question>Try php app/console tc:cdn:update --ron=true {id1} {id2} ...</question>'));
                    return;
                }
            }

            $count = $cdnUpdater->pushMultipleRonSlots($ronAdSlots);
            $output->writeln(sprintf('<info>%d ron ad slot(s) get pushed !</info>', $count));
        }
        else {
            if ($input->getOption('all')) {
                $adSlots = $this->getContainer()->get('tagcade.domain_manager.ad_slot')->all();
                $adSlots = array_map(function(BaseAdSlotInterface $adSlot) {
                    return $adSlot->getId();
                }, $adSlots);
            }
            else {
                $adSlots = $input->getArgument('ids');
                if (count($adSlots) < 1) {
                    $output->writeln(sprintf('<question>Are you missing some ids ?</question>'));
                    $output->writeln(sprintf('<question>Try php app/console tc:cdn:update --ron=false {id1} {id2} ...</question>'));
                    return;
                }
            }

            $count = $cdnUpdater->pushMultipleAdSlots($adSlots);
            $output->writeln(sprintf('<info>%d ad slot(s) get pushed !</info>', $count));
        }
    }
}
