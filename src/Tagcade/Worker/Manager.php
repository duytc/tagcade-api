<?php

namespace Tagcade\Worker;

use DateTime;
use Pheanstalk_PheanstalkInterface;
use StdClass;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\RonAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Service\DateUtilInterface;
use Leezy\PheanstalkBundle\Proxy\PheanstalkProxyInterface;
use Tagcade\Model\Core\AdTagInterface;

// responsible for creating background tasks

class Manager
{
    const TUBE = 'tagcade-api-worker';
    const EXECUTION_TIME_THRESHOLD = 3600;

    protected $dateUtil;

    /**
     * @var PheanstalkProxyInterface
     */
    protected $queue;

    public function __construct(DateUtilInterface $dateUtil, PheanstalkProxyInterface $queue)
    {
        $this->dateUtil = $dateUtil;
        $this->queue = $queue;
    }

    public function updateRevenueForAdTag(AdTagInterface $adTag, $estCpm, DateTime $startDate, DateTime $endDate = null)
    {
        $params = new StdClass;
        $params->adTagId = $adTag->getId();
        $params->estCpm = $estCpm;
        $params->startDate = $this->dateUtil->formatDate($startDate);
        $params->endDate = $endDate ? $this->dateUtil->formatDate($endDate) : null;

        $this->queueTask('updateRevenueForAdTag', $params);
    }

    public function updateRevenueForAdNetwork(AdNetworkInterface $adNetwork, $estCpm, DateTime $startDate, DateTime $endDate = null)
    {
        $params = new StdClass;
        $params->adNetworkId = $adNetwork->getId();
        $params->estCpm = $estCpm;
        $params->startDate = $this->dateUtil->formatDate($startDate);
        $params->endDate = $endDate ? $this->dateUtil->formatDate($endDate) : null;

        $this->queueTask('updateRevenueForAdNetwork', $params);
    }

    public function updateRevenueForAdNetworkAndSite(AdNetworkInterface $adNetwork, SiteInterface $site, $estCpm, DateTime $startDate, DateTime $endDate = null)
    {
        $params = new StdClass;
        $params->adNetworkId = $adNetwork->getId();
        $params->siteId = $site->getId();
        $params->estCpm = $estCpm;
        $params->startDate = $this->dateUtil->formatDate($startDate);
        $params->endDate = $endDate ? $this->dateUtil->formatDate($endDate) : null;

        $this->queueTask('updateRevenueForAdNetworkAndSite', $params);
    }

    public function updateCdnForAdSlot($adSlotId)
    {
        $params = new StdClass;
        $params->adSlotId = $adSlotId;

        $this->queueTask('updateCdnForAdSlot', $params);
    }

    public function updateCdnForRonSlot($ronSlotId)
    {
        $params = new StdClass;
        $params->ronSlotId = $ronSlotId;

        $this->queueTask('updateCdnForRonSlot', $params);
    }

    public function updateCdnForEntity(ModelInterface $entity) {
        $id = $entity->getId();
        if (!is_int($id)) {
            throw new RuntimeException('Could not put entity without id to cdn');
        }

        if ($entity instanceof BaseAdSlotInterface) {
            $this->updateCdnForAdSlot($id);
        }
        else if ($entity instanceof RonAdSlotInterface) {
            $this->updateCdnForRonSlot($id);
        }
        else {

            throw new RuntimeException(sprintf('Not support putting entity of type %s', get_class($entity)));
        }
    }

    /**
     * update cache for multiple site ids
     *
     * @param array $siteIds
     */
    public function updateCacheForSites(array $siteIds)
    {
        $params = new StdClass;
        $params->siteIds = $siteIds;

        $this->queueTask('updateCacheForSites', $params);
    }

    /**
     * update cache for multiple channel ids
     *
     * @param array $channelIds
     */
    public function updateCacheForChannels(array $channelIds)
    {
        $params = new StdClass;
        $params->channelIds = $channelIds;

        $this->queueTask('updateCacheForChannels', $params);
    }

    /**
     * update cache for multiple publisher ids
     *
     * @param array $publisherIds
     */
    public function updateCacheForPublishers(array $publisherIds)
    {
        $params = new StdClass;
        $params->publisherIds = $publisherIds;

        $this->queueTask('updateCacheForPublishers', $params);
    }

    public function updateComparisonForPublisher($publisherId, $startDate, $endDate, $override)
    {
        $params = new StdClass();

        $params->publisherId = $publisherId;
        $params->startDate = $startDate;
        $params->endDate   = $endDate;
        $params->override  = $override;

        $this->queueTask('updateComparisonForPublisher', $params);
    }

    public function updateAdTagStatusForAdNetwork($adNetworkId, $status, $siteId = null)
    {
        $params = new StdClass();

        $params->adNetworkId = $adNetworkId;
        $params->status = $status;
        if ($siteId !== null) {
            $params->siteId   = $siteId;
        }

        $this->queueTask('updateAdTagStatusForAdNetwork', $params);
    }

    /**
     * set AdTag Position For AdNetwork And Sites (optional, one or array or null for all),
     * also, we support auto-Increase-Position(shift down) for all ad tags of other ad network
     *
     * @param AdNetworkInterface $adNetwork
     * @param int $position
     * @param null|SiteInterface|SiteInterface[] $sites optional
     * @param bool $autoIncreasePosition optional, true if need shift down
     * @return int
     */
    public function updateAdTagPositionForAdNetworkAndSites(AdNetworkInterface $adNetwork, $position, $sites = null, $autoIncreasePosition = false)
    {
        $params = new StdClass();

        $params->adNetworkId = $adNetwork->getId();
        $params->position = $position;
        $params->autoIncreasePosition = $autoIncreasePosition;
        $params->siteIds = null;

        if ($sites !== null) {
            $sites = is_array($sites) ? $sites : [$sites];

            $params->siteIds = array_map(function ($site) {
                /** @var SiteInterface $site */
                return $site->getId();
            }, $sites);
        }

        $this->queueTask('updateAdTagPositionForAdNetworkAndSites', $params);
    }

    /**
     * @param string $task
     * @param StdClass $params
     */
    protected function queueTask($task, StdClass $params)
    {
        $payload = new StdClass;

        $payload->task = $task;
        $payload->params = $params;

        $this->queue
            ->useTube(static::TUBE)
            ->put(json_encode($payload),
                Pheanstalk_PheanstalkInterface::DEFAULT_PRIORITY,
                Pheanstalk_PheanstalkInterface::DEFAULT_DELAY,
                self::EXECUTION_TIME_THRESHOLD)
        ;
    }
}