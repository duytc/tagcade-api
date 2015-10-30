<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Service\Cdn\CDNUpdaterInterface;

/**
 * Provides a command-line interface for generating and assigning uuid for all publisher
 */
class CdnUpdateCommand extends ContainerAwareCommand
{
    const RON_AD_SLOT = 'ron';
    const REGULAR_AD_SLOT = 'reg';


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
            ->addOption(
                'type',
                't',
                InputOption::VALUE_OPTIONAL,
                'Specify the ad slot type: ron or reg'
            )
            ->addArgument(
                'ids',
                InputArgument::IS_ARRAY,
                'The ids of list ad slots to be pushed'
            )
            ->addOption(
                'all',
                'a',
                InputOption::VALUE_NONE,
                'If set, all ad slots (even ron ad slot) will get pushed to FTP server'
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
        $type = $input->getOption('type');
        $forAll = $input->getOption('all');
        $ids = $input->getArgument('ids');

        $ronAdSlots = array();
        $regularAdSlots = array();

        if (is_array($ids) && !empty($ids)) {
            if ($type != self::RON_AD_SLOT && $type != self::REGULAR_AD_SLOT) {
                throw new RuntimeException('expect type of ad slot');
            }

            switch($type) {
                case self::RON_AD_SLOT:
                    $ronAdSlots = $ids;
                    break;
                case self::REGULAR_AD_SLOT:
                    $regularAdSlots = $ids;
                    break;
            }
        }

        if (true === $forAll) {

            if (null === $type || $type === self::RON_AD_SLOT) {
                $ronAdSlots = $this->getContainer()->get('tagcade.domain_manager.ron_ad_slot')->all();
                $ronAdSlots = array_map(function(RonAdSlotInterface $ronAdSlot) {
                    return $ronAdSlot->getId();
                }, $ronAdSlots);
            }

            if (null === $type || $type === self::REGULAR_AD_SLOT) {
                $regularAdSlots = $this->getContainer()->get('tagcade.domain_manager.ad_slot')->all();
                $regularAdSlots = array_map(function(BaseAdSlotInterface $adSlot) {
                    return $adSlot->getId();
                }, $regularAdSlots);

            }

        }

        try {
            
            $count = $this->doPushCdn($type, $ronAdSlots, $regularAdSlots);
            $output->writeln(sprintf('%d items get pushed to cdn.', $count));
        }
        catch(\RuntimeException $ex) {
            $output->writeln('Could not push data to cdn. ' . $ex);
        }
    }

    protected function doPushCdn($type, array $ronAdSlots, array $adSlots)
    {
        /**  @var CDNUpdaterInterface $cdnUpdater */
        $cdnUpdater = $this->getContainer()->get('tagcade.service.cdn.cdn_updater');
        $count = 0;
        switch ($type) {
            case self::RON_AD_SLOT:
                $count = $cdnUpdater->pushMultipleRonSlots($ronAdSlots);
                break;
            case self::REGULAR_AD_SLOT:
                $count = $cdnUpdater->pushMultipleAdSlots($adSlots);
                break;
            default:
                $count = $cdnUpdater->pushMultipleRonSlots($ronAdSlots);
                $count += $cdnUpdater->pushMultipleAdSlots($adSlots);
                break;
        }

        return $count;
    }
}
