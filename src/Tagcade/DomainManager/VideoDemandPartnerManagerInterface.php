<?php

namespace Tagcade\DomainManager;

use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\User\Role\PublisherInterface;

interface VideoDemandPartnerManagerInterface extends ManagerInterface
{
    /**
     * get all VideoDemandPartners For a Publisher
     *
     * @param PublisherInterface $publisher
     * @param null|int $limit
     * @param null|int $offset
     * @return array|VideoDemandPartnerInterface[]
     */
    public function getVideoDemandPartnersForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param PublisherInterface $publisher
     * @param $canonicalName
     * @return array|VideoDemandPartnerInterface[]
     */
    public function getVideoDemandPartnerForPublisherByCanonicalName(PublisherInterface $publisher, $canonicalName);

    /**
     * @param VideoDemandPartnerInterface $videoDemandPartner
     * @param null|int $limit
     * @param null|int $offset
     * @return array|VideoDemandAdTagInterface[]
     */
    public function getVideoDemandAdTagsForVideoDemandPartner(VideoDemandPartnerInterface $videoDemandPartner, $limit = null, $offset = null);

    /**
     * @param null|int $limit
     * @param null|int $offset
     * @return array|VideoDemandAdTagInterface[]
     */
    public function allHasCap($limit = null, $offset = null);
}