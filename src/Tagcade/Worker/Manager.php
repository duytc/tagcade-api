<?php

namespace Tagcade\Worker;

use DateTime;
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

    protected $dateUtil;
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
        $params->ronAdSlotId = $ronSlotId;

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
            ->put(json_encode($payload))
        ;
    }
}