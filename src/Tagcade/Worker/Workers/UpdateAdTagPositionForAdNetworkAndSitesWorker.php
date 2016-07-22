<?php

namespace Tagcade\Worker\Workers;

use StdClass;
use Tagcade\DomainManager\AdNetworkManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Service\Core\AdTag\AdTagPositionEditorInterface;

// responsible for doing the background tasks assigned by the manager
// all public methods on the class represent tasks that can be done

class UpdateAdTagPositionForAdNetworkAndSitesWorker
{
    /** @var AdNetworkManagerInterface */
    protected $adNetworkManager;

    /** @var SiteManagerInterface */
    protected $siteManager;

    /** @var AdTagPositionEditorInterface */
    protected $adTagPositionEditor;

    public function __construct(AdNetworkManagerInterface $adNetworkManager, SiteManagerInterface $siteManager, AdTagPositionEditorInterface $adTagPositionEditor)
    {
        $this->adNetworkManager = $adNetworkManager;
        $this->siteManager = $siteManager;
        $this->adTagPositionEditor = $adTagPositionEditor;
    }

    public function updateAdTagPositionForAdNetworkAndSites(StdClass $params)
    {
        // get all params
        /** @var AdNetworkInterface $adNetwork */
        $adNetwork = $this->adNetworkManager->find($params->adNetworkId);

        if (!$adNetwork instanceof AdNetworkInterface) {
            throw new InvalidArgumentException('That ad network does not exist');
        }

        $position = filter_var($params->position, FILTER_VALIDATE_INT);

        $autoIncreasePosition = $params->autoIncreasePosition;

        $siteIds = $params->siteIds;
        $sites = null;

        if (is_array($siteIds)) {
            $sites = array_map(function ($siteId) {
                $site = $this->siteManager->find($siteId);

                if (!$site instanceof SiteInterface) {
                    throw new InvalidArgumentException('That site does not exist');
                }

                return $site;
            }, $params->siteIds);
        }

        // do cascading position
        $this->adTagPositionEditor->setAdTagPositionForAdNetworkAndSites($adNetwork, $position, $sites, $autoIncreasePosition);
    }
}