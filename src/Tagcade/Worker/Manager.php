<?php

namespace Tagcade\Worker;

use DateTime;
use StdClass;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;
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