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
use Tagcade\Exception\InvalidArgumentException;
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
            ->setName('tc:ron-slot:remove-blocking')
            ->setDescription('Remove blocking ron slots for a certain domain or all domains for a certain ron slot or unblock everything')
            ->addOption('ronSlot', 'r', InputOption::VALUE_OPTIONAL, 'Id of the ron slot to be unblocked')
            ->addOption('domain', 'd', InputOption::VALUE_OPTIONAL, 'The domain that ron slot(s) should be unblocked')
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
        $ronSlotId = $input->getOption('ronSlot');
        $domain = $input->getOption('domain');

        /**
         * @var \Redis $redis
         */
        $redis = $this->getContainer()->get('redis_array.performance_report_data');

        if (null === $ronSlotId && null === $domain) {
            $this->removeAllBlockingRonSlots($redis);
            $output->writeln('All ron slots are unblocked');

            return;
        }


        if (null !== $ronSlotId && null !== $domain) {
            $this->removeBlockingRonSlotOnDomain($redis, $ronSlotId, $domain);
            $output->writeln('The ron slot is now unblocked on that domain');

            return;
        }

        if (null !== $ronSlotId) {
            $this->removeBlockingRonSlot($redis, $ronSlotId);
            $output->writeln('The ron slot is now unblocked on all domains');

            return;
        }

        $this->removeBlockingDomain($redis, $domain);

        $output->writeln('All ron slots are unblocked on that domain');
    }

    protected function removeBlockingRonSlot(\RedisArray $redis, $id)
    {
        if (null === $id) {
            throw new InvalidArgumentException('Expect a valid id for ron slot');
        }
        // remove blocking for ron slot id
        $members = $redis->sMembers(self::REDIS_SET_BLOCKING_DOMAIN_RON);
        foreach ($members as $ronSlotDomain) {
            $searchKey = sprintf('ron_slot_%d', $id);
            if (strpos($ronSlotDomain, $searchKey) === 0) {
                $redis->sRemove(self::REDIS_SET_BLOCKING_DOMAIN_RON, $ronSlotDomain);
            }
        }

        return true;
    }

    protected function removeBlockingDomain(\RedisArray $redis, $domain)
    {
        if (null === $domain) {
            throw new InvalidArgumentException('expect a valid domain');
        }

        // remove blocking for ron slot id
        $members = $redis->sMembers(self::REDIS_SET_BLOCKING_DOMAIN_RON);
        foreach ($members as $ronSlotDomain) {
            $searchKey = sprintf('domain_%s', $domain);
            if (strpos($ronSlotDomain, $searchKey) >= 0) {
                $redis->sRemove(self::REDIS_SET_BLOCKING_DOMAIN_RON, $ronSlotDomain);
            }
        }

        return true;

    }

    protected function removeBlockingRonSlotOnDomain(\RedisArray $redis, $id, $domain)
    {
        if (null === $id || null === $domain) {
            throw new InvalidArgumentException(sprintf('Expect valid id and domain. Input was id=%d and domain=%s', $id, $domain));
        }

        $domainRonSlotKey = sprintf(self::FIELD_RON_SLOT_DOMAIN, $id, $domain);
        if (!$redis->sIsMember(self::REDIS_SET_BLOCKING_DOMAIN_RON, $domainRonSlotKey) ) {
            return false;
        }

        $redis->sRemove(self::REDIS_SET_BLOCKING_DOMAIN_RON, $domainRonSlotKey);

        return true;
    }

    protected function removeAllBlockingRonSlots(\RedisArray $redis)
    {
        $redis->delete(self::REDIS_SET_BLOCKING_DOMAIN_RON);
    }
}
