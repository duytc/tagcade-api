<?php

namespace Tagcade\Worker\Workers;

use StdClass;
use Tagcade\DomainManager\AdNetworkManagerInterface;
use Tagcade\DomainManager\AdTagManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;

// responsible for doing the background tasks assigned by the manager
// all public methods on the class represent tasks that can be done

class UpdateAdTagStatusForAdNetworkWorker
{
    protected $adTagManager;
    protected $adNetworkManager;
    protected $siteManager;

    public function __construct(AdTagManagerInterface $adTagManager, AdNetworkManagerInterface $adNetworkManager, SiteManagerInterface $siteManager)
    {
        $this->adTagManager = $adTagManager;
        $this->adNetworkManager = $adNetworkManager;
        $this->siteManager = $siteManager;
    }

    public function updateAdTagStatusForAdNetwork(StdClass $params)
    {
        /** @var AdNetworkInterface $adNetwork */
        $adNetwork = $this->adNetworkManager->find($params->adNetworkId);

        if (!$adNetwork instanceof AdNetworkInterface) {
            throw new InvalidArgumentException('That ad network does not exist');
        }

        $status = filter_var($params->status, FILTER_VALIDATE_INT);
        if (isset($params->siteId)) {
            $site = $this->siteManager->find($params->siteId);
            if (!$site instanceof SiteInterface) {
                throw new InvalidArgumentException('That site does not exist');
            }

            $this->adTagManager->updateActiveStateBySingleSiteForAdNetwork($adNetwork, $site, $status);
            return;
        }

        $this->adTagManager->updateAdTagStatusForAdNetwork($adNetwork, $status);
    }
}