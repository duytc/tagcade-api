<?php

namespace Tagcade\DomainManager;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ObjectManager;
use InvalidArgumentException;
use ReflectionClass;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\BaseLibraryAdSlotInterface;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;
use Tagcade\Repository\Core\SiteRepositoryInterface;

class SiteManager implements SiteManagerInterface
{
    protected $om;
    protected $repository;

    public function __construct(ObjectManager $om, SiteRepositoryInterface $repository)
    {
        $this->om = $om;
        $this->repository = $repository;
    }

    /**
     * @inheritdoc
     */
    public function supportsEntity($entity)
    {
        return is_subclass_of($entity, SiteInterface::class);
    }

    /**
     * @inheritdoc
     */
    public function save(ModelInterface $site)
    {
        if (!$site instanceof SiteInterface) throw new InvalidArgumentException('expect SiteInterface object');

        $this->om->persist($site);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function delete(ModelInterface $site)
    {
        if (!$site instanceof SiteInterface) throw new InvalidArgumentException('expect SiteInterface object');

        $this->om->remove($site);
        $this->om->flush();
    }

    /**
     * @inheritdoc
     */
    public function createNew()
    {
        $entity = new ReflectionClass($this->repository->getClassName());
        return $entity->newInstance();
    }

    /**
     * @inheritdoc
     */
    public function find($id)
    {
        return $this->repository->find($id);
    }

    /**
     * @inheritdoc
     */
    public function all($limit = null, $offset = null)
    {
        return $this->repository->findBy($criteria = [], $orderBy = null, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getSitesForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getSitesForPublisher($publisher, $limit, $offset);
    }

    public function getSitesForPublishers(array $publishers, $limit = null, $offset = null)
    {
        if (empty($publishers)) {
            return [];
        }

        $publishers = array_filter($publishers, function($publisher) {
            return $publisher instanceof PublisherInterface;
        });

        $publishers = array_map(function(PublisherInterface $publisher) {
            return $publisher->getId();
        }, $publishers);

        return $this->repository->getSitesForPublishers($publishers, $limit, $offset);
    }

    public function getSitesThatHaveAdTagsBelongingToAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        return $this->repository->getSitesThatHaveAdTagsBelongingToAdNetwork($adNetwork);
    }

    /**
     * @inheritdoc
     */
    public function getSitesThatHaveAdTagsBelongingToPartnerForPublisher(PublisherInterface $publisher, $limit = null, $offset = null)
    {
        return $this->repository->getSitesThatHaveAdTagsBelongingToPartnerForPublisher($publisher, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getSitesThatHaveAdTagsBelongingToPartner(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        return $this->repository->getSitesThatHaveAdTagsBelongingToPartner($adNetwork, $limit, $offset);
    }

    /**
     * @inheritdoc
     */
    public function getSitesThatHaveAdTagsBelongingToPartnerWithSubPublisher(AdNetworkInterface $adNetwork, SubPublisherInterface $subPublisher, $limit = null, $offset = null)
    {
        return $this->repository->getSitesThatHaveAdTagsBelongingToPartnerWithSubPublisher($adNetwork, $subPublisher, $limit, $offset);
    }

    public function getSiteIdsThatHaveAdTagsBelongingToAdNetwork(AdNetworkInterface $adNetwork, $limit = null, $offset = null)
    {
        return $this->repository->getSiteIdsThatHaveAdTagsBelongingToAdNetwork($adNetwork, $limit, $offset);
    }


    public function getSitesThatHaveSourceReportConfigForPublisher(PublisherInterface $publisher, $hasSourceReportConfig = true)
    {
        return $this->repository->getSitesThatHastConfigSourceReportForPublisher($publisher, $hasSourceReportConfig);
    }

    public function getSitesThatEnableSourceReportForPublisher(PublisherInterface $publisher, $enableSourceReport = true)
    {
        return $this->repository->getSitesThatEnableSourceReportForPublisher($publisher, $enableSourceReport);
    }

    /**
     * @inheritdoc
     */
    public function getAllSitesThatEnableSourceReport($enableSourceReport = true)
    {
        return $this->repository->getAllSitesThatEnableSourceReport($enableSourceReport);
    }

    /**
     * @inheritdoc
     */
    public function getSitesUnreferencedToLibraryAdSlot(BaseLibraryAdSlotInterface $slotLibrary, $limit = null, $offset = null)
    {
        return $this->repository->getSitesUnreferencedToLibraryAdSlot($slotLibrary, $limit = null, $offset = null);
    }

    /**
     * @inheritdoc
     */
    public function deleteChannelForSite(SiteInterface $site, $channelId)
    {
        $channelSites = $site->getChannelSites();

        if ($channelSites instanceof Collection) {
            $channelSites = $channelSites->toArray();
        }

        //number of removed channels
        $removedCount = 0;

        foreach ($channelSites as $idx => $cs) {
            if ($cs->getChannel()->getId() == $channelId) {
                //remove matched element
                $this->om->remove($cs);
                $removedCount++;
            }
        }

        //flush to db if has element removed
        if ($removedCount > 0) {
            $this->om->flush();
        }

        //return number of removed channels
        return $removedCount;
    }

    public function getSiteByDomainAndPublisher($domain, PublisherInterface $publisher)
    {
        return $this->repository->getSitesByDomainAndPublisher($publisher, $domain);
    }

    public function getUniqueDomainsForPublisher(PublisherInterface $publisher)
    {
        return $this->repository->getUniqueDomainsForPublisher($publisher);
    }

    /**
     * @param $siteToken
     * @return mixed
     */
    public function getSiteBySiteToken($siteToken)
    {
        return $this->repository->getSiteBySiteToken($siteToken);
    }

    /**
     * @param PublisherInterface $publisher
     * @param $siteName
     * @return mixed
     */
    public function getSiteByPublisherAndSiteName(PublisherInterface $publisher, $siteName)
    {
        return $this->repository->getSiteByPublisherAndSiteName($publisher, $siteName);
    }

    /**
     * @param ModelInterface $site
     * @return mixed|void
     */
    public function persists(ModelInterface $site)
    {
         $this->om->persist($site);
    }

    /**
     * Flush message to data base
     */
    public function flush()
    {
        $this->om->flush();
    }
}