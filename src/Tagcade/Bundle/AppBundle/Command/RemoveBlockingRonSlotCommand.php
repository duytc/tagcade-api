<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Tagcade\Cache\ConfigurationCacheInterface;
use Tagcade\Cache\Legacy\Cache\RedisArrayCacheInterface;
use Tagcade\DomainManager\RonAdSlotManagerInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\RonAdTagInterface;

/**
 * Provides a command-line interface for renewing cache using cli
 */
class RemoveBlockingRonSlotCommand extends ContainerAwareCommand
{

    const REDIS_SET_BLOCKING_DOMAIN_RON = 'event_processor:domain_ron:blocking'; // for internal code processing only
    const FIELD_RON_SLOT_DOMAIN = 'ron_slot_%d:domain_%s';

    /**
     * Configure the CLI task
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('tc:blocking:remove')
            ->setDescription('remove blocking ron slot for a certain domain')
            ->addOption('ronSlot', 'r', InputOption::VALUE_OPTIONAL)
            ->addOption('domain', 'd', InputOption::VALUE_OPTIONAL)
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
        $ronSlotId = (int)$input->getOption('ronSlot');
        $domain = $input->getOption('domain');

        if (null === $ronSlotId) {
            $this->removeAllBlockingRonSlots();
            $output->writeln('All blocking ron slots are unblocked');

            return;
        }
        /**
         * @var RonAdSlotManagerInterface $ronAdSlotManager
         */
        $ronAdSlotManager = $this->getContainer()->get('tagcade.domain_manager.ron_ad_slot');
        $ronSlot = $ronAdSlotManager->find($ronSlotId);
        if (!$ronSlot instanceof RonAdSlotInterface) {
            $output->writeln('Not found that ron slot');
        }

        /**
         * @var \Redis $redis
         */
        $redis = $this->getContainer()->get('redis_array.performance_report_data');
        if (null !== $domain) {
            $domainRonSlotKey = sprintf(self::FIELD_RON_SLOT_DOMAIN, $ronSlotId, $domain);
            if (!$redis->sIsMember(self::REDIS_SET_BLOCKING_DOMAIN_RON, $domainRonSlotKey) ) {
                $output->writeln('the ron slot is not blocked on this domain');
            }

            $redis->sRemove(self::REDIS_SET_BLOCKING_DOMAIN_RON, $domainRonSlotKey);
            $output->writeln('the ron slot is now unblocked on that domain');

            return;
        }

        // remove blocking for ron slot id
        $members = $redis->sMembers(self::REDIS_SET_BLOCKING_DOMAIN_RON);
        foreach ($members as $ronSlotDomain) {
            $searchKey = sprintf('ron_slot_%d', $ronSlotId);
            if (strpos($ronSlotDomain, $searchKey) === 0) {
                $redis->sRemove(self::REDIS_SET_BLOCKING_DOMAIN_RON, $ronSlotDomain);
            }
        }


        $output->writeln('The ron slot is now unblocked');
    }

    protected function removeAllBlockingRonSlots()
    {
        /**
         * @var \Redis $redis
         */
        $redis = $this->getContainer()->get('tagcade.domain_manager.ron_ad_slot');
        $redis->delete(self::REDIS_SET_BLOCKING_DOMAIN_RON);

    }
}
