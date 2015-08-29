<?php
namespace Tagcade\Service\TagLibrary;

use Tagcade\DomainManager\AdSlotManagerInterface;
use Tagcade\DomainManager\ChannelManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Entity\Core\DisplayAdSlot;
use Tagcade\Entity\Core\DynamicAdSlot;
use Tagcade\Entity\Core\Expression;
use Tagcade\Entity\Core\NativeAdSlot;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\LogicException;
use Tagcade\Model\Core\BaseAdSlotInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\ChannelInterface;
use Tagcade\Model\Core\DynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryDisplayAdSlotInterface;
use Tagcade\Model\Core\LibraryDynamicAdSlotInterface;
use Tagcade\Model\Core\LibraryExpressionInterface;
use Tagcade\Model\Core\LibraryNativeAdSlotInterface;
use Tagcade\Model\Core\ReportableAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;

class AdSlotGenerator implements AdSlotGeneratorInterface
{

    /** @var AdSlotManagerInterface */
    private $adSlotManager;

    /** @var ChannelManagerInterface */
    private $channelManager;

    /** @var SiteManagerInterface */
    private $siteManager;

    public function setAdSlotManager(AdSlotManagerInterface $adSlotManager)
    {
        $this->adSlotManager = $adSlotManager;
    }

    public function setChannelManager(ChannelManagerInterface $channelManager)
    {
        $this->channelManager = $channelManager;
    }

    public function setSiteManager(SiteManagerInterface $siteManager)
    {
        $this->siteManager = $siteManager;
    }

    /**
     * @param LibraryDynamicAdSlotInterface $libraryDynamicAdSlot
     * @param SiteInterface $site
     *
     * @return DynamicAdSlotInterface
     */
    public function getProspectiveDynamicAdSlotForLibraryAndSite(LibraryDynamicAdSlotInterface $libraryDynamicAdSlot, SiteInterface $site)
    {
        $dynamicAdSlot = new DynamicAdSlot();
        $dynamicAdSlot->setSite($site);


        $this->generateTrueDefaultAdSlotAndExpectAdSlotInExpressionsForLibraryDynamicAdSlotBySite($libraryDynamicAdSlot, $site);

        $libraryExpressions = $libraryDynamicAdSlot->getLibraryExpressions();
        if ($libraryExpressions !== null) {
            /**
             * @var LibraryExpressionInterface $libraryExpression
             */
            foreach ($libraryExpressions as $libraryExpression) {
                $expectAdSlot = $this->adSlotManager->getReferencedAdSlotsForSite($libraryExpression->getExpectLibraryAdSlot(), $site);
                if (!$expectAdSlot instanceof ReportableAdSlotInterface) {
                    throw new LogicException('expect existing expect ad slot for expression');
                }

                $libraryExpression->getExpressions()->clear();
                $expression = new Expression();
                $expression->setExpectAdSlot($expectAdSlot);
                $expression->setLibraryExpression($libraryExpression);

                $libraryExpression->getExpressions()->add($expression);
                $expression->setDynamicAdSlot($dynamicAdSlot);
                $dynamicAdSlot->getExpressions()->add($expression);
            }
        }


        $dynamicAdSlot->setLibraryAdSlot($libraryDynamicAdSlot);

        $defaultLibraryAdSlot = $libraryDynamicAdSlot->getDefaultLibraryAdSlot();
        if ($defaultLibraryAdSlot instanceof LibraryDisplayAdSlotInterface || $defaultLibraryAdSlot instanceof LibraryNativeAdSlotInterface) {
            $defaultAdSlot = $this->adSlotManager->getReferencedAdSlotsForSite($defaultLibraryAdSlot, $site);

            if (!$defaultAdSlot instanceof ReportableAdSlotInterface) {
                throw new LogicException('expect existing default ad slot for expression');
            }

            $dynamicAdSlot->setDefaultAdSlot($defaultAdSlot);
        }

        return $dynamicAdSlot;
    }

    /**
     * Generate expression and default ad slot associated to this site
     *
     * @param LibraryDynamicAdSlotInterface $libraryDynamicAdSlot
     * @param SiteInterface $site
     * @return void
     */
    public function generateTrueDefaultAdSlotAndExpectAdSlotInExpressionsForLibraryDynamicAdSlotBySite(LibraryDynamicAdSlotInterface $libraryDynamicAdSlot, SiteInterface $site)
    {
        // Step 1. Get library default ad slot associated this dynamic ad slot and check if there is any ad slot for this site refer to this library
        $defaultLibraryAdSlot = $libraryDynamicAdSlot->getDefaultLibraryAdSlot();
        if ($defaultLibraryAdSlot instanceof LibraryDisplayAdSlotInterface || $defaultLibraryAdSlot instanceof LibraryNativeAdSlotInterface) {
            $currentReference = $this->adSlotManager->getReferencedAdSlotsForSite($defaultLibraryAdSlot, $site);
            // Step 2. generate default ad slot if step 1 return empty (generate default ad slot base on default library ad slot in library dynamic ad slot)
            if (null === $currentReference) {
                $this->createReportableAdSlotForSite($defaultLibraryAdSlot, $site);
            }
        }

        // Step 3. Get expressions associated to this dynamic ad slot
        $libraryExpressions = $libraryDynamicAdSlot->getLibraryExpressions();
        if (null !== $libraryExpressions) {
            // step 4. generate expect ad slot in expression if no ad slot refer to expect library (generation is based on expect library ad slot in each library expression)
            foreach ($libraryExpressions as $libraryExpression) {
                /**
                 * @var LibraryExpressionInterface $libraryExpression
                 */
                $libraryExpectAdSlot = $libraryExpression->getExpectLibraryAdSlot();
                if (!$libraryExpectAdSlot instanceof LibraryDisplayAdSlotInterface && !$libraryExpectAdSlot instanceof LibraryNativeAdSlotInterface) {
                    throw new InvalidArgumentException('expect display or native library');
                }
                $currentReference = $this->adSlotManager->getReferencedAdSlotsForSite($libraryExpectAdSlot, $site);

                if (null === $currentReference) {
                    $this->createReportableAdSlotForSite($libraryExpectAdSlot, $site);
                }
            }
        }
    }

    public function generateAdSlotFromLibraryForChannelsAndSites(BaseLibraryAdSlotInterface $libraryAdSlot, array $channels, array $sites)
    {
        $channels = array_unique($channels);
        //get all sites for channels
        /** @var SiteInterface[] $allSites */
        $allSites = $sites;
        foreach ($channels as $cn) {
            /** @var ChannelInterface $cn */
            $tmpSites = $cn->getSites();
            $allSites = array_merge($allSites, $tmpSites);

            unset($tmpSites);
        }

        $allSites = array_unique($allSites);

        return $this->generateAdSlotFromLibraryForSites($libraryAdSlot, $allSites);
    }

    /**
     * @inheritdoc
     */
    public function generateAdSlotFromLibraryForChannels(BaseLibraryAdSlotInterface $libraryAdSlot, array $channels)
    {
        $channels = array_unique($channels);

        //get all sites for channels
        /** @var SiteInterface[] $allSites */
        $allSites = [];
        foreach ($channels as $cn) {
            /** @var ChannelInterface $cn */
            $sites = $cn->getSites();
            $allSites = array_merge($allSites, $sites);

            unset($sites);
        }

        return $this->generateAdSlotFromLibraryForSites($libraryAdSlot, array_unique($allSites));
    }

    /**
     * @inheritdoc
     */
    public function generateAdSlotFromLibraryForSites(BaseLibraryAdSlotInterface $libraryAdSlot, array $sites)
    {
        $sites = array_unique($sites);

        if (count($sites) < 1) {
            return 0;
        }

        //filter all sites have no ad slots linked to library Ad Slot
        $filteredSites = array_filter($sites, function (SiteInterface $site) use ($libraryAdSlot) {
            return null === $this->adSlotManager->getReferencedAdSlotsForSite($libraryAdSlot, $site);
        });

        //do create link
        $createdLinks = 0;

        foreach ($filteredSites as $site) {
            /** @var BaseAdSlotInterface $adSlot */
            if ($libraryAdSlot instanceof LibraryDisplayAdSlotInterface || $libraryAdSlot instanceof LibraryNativeAdSlotInterface) {
                //this for library display and library native ad slot
                $this->createReportableAdSlotForSite($libraryAdSlot, $site);

                $createdLinks++;
            } else if ($libraryAdSlot instanceof LibraryDynamicAdSlotInterface) {
                //this for library dynamic ad slot
                $dynamicAdSlot = $this->getProspectiveDynamicAdSlotForLibraryAndSite($libraryAdSlot, $site);

                $this->adSlotManager->save($dynamicAdSlot);

                $createdLinks++;
            }
        }

        return $createdLinks;
    }

    /**
     * Create new display ad slot from site and library
     *
     * @param SiteInterface $site
     * @param BaseLibraryAdSlotInterface $libraryAdSlot
     * @return ReportableAdSlotInterface
     */
    protected function createReportableAdSlotForSite(BaseLibraryAdSlotInterface $libraryAdSlot, SiteInterface $site)
    {
        if ($libraryAdSlot instanceof LibraryDynamicAdSlotInterface) {
            throw new InvalidArgumentException('expect display or native library');
        }

        $adSlot = $libraryAdSlot instanceof LibraryDisplayAdSlotInterface ? new DisplayAdSlot() : new NativeAdSlot();
        $adSlot->setLibraryAdSlot($libraryAdSlot);
        $adSlot->setSite($site);
        $this->adSlotManager->save($adSlot);

        return $adSlot;
    }
}