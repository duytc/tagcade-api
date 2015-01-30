<?php

namespace Tagcade\Worker\Workers;

use StdClass;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\RevenueEditorInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\DomainManager\AdNetworkManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;

// responsible for doing the background tasks assigned by the manager
// all public methods on the class represent tasks that can be done

class UpdateRevenueWorker
{
    protected $dateUtil;
    protected $revenueEditor;
    protected $adTagManager;
    protected $adNetworkManager;
    protected $siteManager;

    public function __construct(DateUtilInterface $dateUtil, RevenueEditorInterface $revenueEditor, AdTagManagerInterface $adTagManager, AdNetworkManagerInterface $adNetworkManager, SiteManagerInterface $siteManager)
    {
        $this->dateUtil = $dateUtil;
        $this->revenueEditor = $revenueEditor;
        $this->adTagManager = $adTagManager;
        $this->adNetworkManager = $adNetworkManager;
        $this->siteManager = $siteManager;
    }

    public function updateRevenueForAdTag(StdClass $params) {
        $adTag = $this->adTagManager->find($params->adTagId);

        if (!$adTag) {
            throw new InvalidArgumentException('That ad tag does not exist');
        }

        $this->revenueEditor->updateRevenueForAdTag(
            $adTag,
            $params->estCpm,
            $this->dateUtil->getDateTime($params->startDate),
            $this->dateUtil->getDateTime($params->endDate)
        );
    }

    public function updateRevenueForAdNetwork(StdClass $params) {
        $adNetwork = $this->adNetworkManager->find($params->adNetworkId);

        if (!$adNetwork) {
            throw new InvalidArgumentException('That ad network does not exist');
        }

        $this->revenueEditor->updateRevenueForAdNetwork(
            $adNetwork,
            $params->estCpm,
            $this->dateUtil->getDateTime($params->startDate),
            $this->dateUtil->getDateTime($params->endDate)
        );
    }

    public function updateRevenueForAdNetworkAndSite(StdClass $params) {
        $adNetwork = $this->adNetworkManager->find($params->adNetworkId);

        if (!$adNetwork) {
            throw new InvalidArgumentException('That ad network does not exist');
        }

        $site = $this->siteManager->find($params->siteId);

        if (!$site) {
            throw new InvalidArgumentException('That site does not exist');
        }

        $this->revenueEditor->updateRevenueForAdNetworkSite(
            $adNetwork,
            $site,
            $params->estCpm,
            $this->dateUtil->getDateTime($params->startDate),
            $this->dateUtil->getDateTime($params->endDate)
        );
    }
}