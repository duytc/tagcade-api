<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
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
                'type',
                null,
                InputOption::VALUE_OPTIONAL,
                'Specify the ad slot type: ron or slot',
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
        if ($input->getOption('all')) {
            $ronAdSlots = $this->getContainer()->get('tagcade.domain_manager.ron_ad_slot')->all();
            $ronAdSlots = array_map(function(RonAdSlotInterface $ronAdSlot) {
                return $ronAdSlot->getId();
            }, $ronAdSlots);

            $adSlots = $this->getContainer()->get('tagcade.domain_manager.ad_slot')->all();
            $adSlots = array_map(function(BaseAdSlotInterface $adSlot) {
                return $adSlot->getId();
            }, $adSlots);

            $this->push($output, $input->getOption('type'), $ronAdSlots, $adSlots);
            return;
        }

        $ronAdSlots = $input->getArgument('ids');
        if (count($ronAdSlots) < 1) {
            $output->writeln(sprintf('<question>Are you missing some ids ?</question>'));
            $output->writeln(sprintf('<question>Try php app/console tc:cdn:update --type=ron {id1} {id2} ...</question>'));
            return;
        }
        $adSlots = $ronAdSlots;

        $this->push($output, $input->getOption('type'), $ronAdSlots, $adSlots);
    }

    /**
     * push slots to CDN and write result to output console
     *
     * @param OutputInterface $output
     * @param $type
     * @param array $ronAdSlots
     * @param array $adSlots
     */
    protected function push(OutputInterface $output, $type, array $ronAdSlots, array $adSlots)
    {
        /**  @var CDNUpdaterInterface $cdnUpdater */
        $cdnUpdater = $this->getContainer()->get('tagcade.service.cdn.cdn_updater');

        switch ($type) {
            case 'ron':
                $output->writeln(sprintf('<info>%d ron ad slot(s) get pushed !</info>', $cdnUpdater->pushMultipleRonSlots($ronAdSlots)));
                break;
            case 'slot':
                $output->writeln(sprintf('<info>%d ad slot(s) get pushed !</info>', $cdnUpdater->pushMultipleAdSlots($adSlots)));
                break;
            default:
                $output->writeln(sprintf('<info>%d ron ad slot(s) get pushed !</info>', $cdnUpdater->pushMultipleRonSlots($ronAdSlots)));
                $output->writeln(sprintf('<info>%d ad slot(s) get pushed !</info>', $cdnUpdater->pushMultipleAdSlots($adSlots)));
                break;
        }
    }
}
