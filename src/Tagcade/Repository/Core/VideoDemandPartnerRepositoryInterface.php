<?php


namespace Tagcade\Repository\Core;

use Doctrine\Common\Persistence\ObjectRepository;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\PagerParam;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\UserRoleInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

interface VideoDemandPartnerRepositoryInterface extends ObjectRepository
{
    /**
     * get all VideoDemandPartners For a Publisher
     *
     * @param PublisherInterface $publisher
     * @param null $limit
     * @param null $offset
     * @return array|VideoDemandPartnerInterface[]
     */
    public function getVideoDemandPartnersForPublisher(PublisherInterface $publisher, $limit = null, $offset = null);

    /**
     * @param UserRoleInterface $user
     * @param PagerParam $param
     * @return mixed
     */
    public function getVideoDemandPartnersForPublisherWithPagination(UserRoleInterface $user, PagerParam $param);

    /**
     * @param PublisherInterface $publisher
     * @param $canonicalName
     * @return array|VideoDemandPartnerInterface[]
     */
    public function getVideoDemandPartnerForPublisherByCanonicalName(PublisherInterface $publisher, $canonicalName);

    /**
     * get VideoDemandPartners By FilterParams
     *
     * @param FilterParameterInterface $filterParameter
     * @return array
     */
    public function getVideoDemandPartnersByFilterParams(FilterParameterInterface $filterParameter);

    /**
     * @param null|int $limit
     * @param null|int $offset
     * @return array|VideoDemandPartnerInterface[]
     */
    public function allHasCap($limit = null, $offset = null);
}