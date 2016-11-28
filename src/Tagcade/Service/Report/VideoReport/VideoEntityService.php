<?php

namespace Tagcade\Service\Report\VideoReport;


use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\Model\Core\VideoPublisherInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandAdTag as DemandPartnerDemandAdTagReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandPartner as DemandPartnerDemandPartnerReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Account as PlatformAccountReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Publisher as PlatformPublisherReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\DemandAdTag as PlatformAdSourceReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\WaterfallTag as PlatformAdTagReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Platform as PlatformPlatformReportType;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;
use Tagcade\Repository\Core\VideoDemandAdTagRepositoryInterface;
use Tagcade\Repository\Core\VideoPublisherRepositoryInterface;
use Tagcade\Repository\Core\VideoWaterfallTagRepositoryInterface;
use Tagcade\Repository\Core\VideoDemandPartnerRepositoryInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class VideoEntityService
{
    /** @var VideoDemandAdTagRepositoryInterface */
    private $videoDemandAdTagRepository;

    /** @var VideoWaterfallTagRepositoryInterface */
    private $videoWaterfallTagRepository;

    /** @var VideoDemandPartnerRepositoryInterface */
    private $videoDemandPartnerRepository;

    /** @var PublisherManagerInterface */
    private $publisherManager;
    /**
     * @var VideoPublisherRepositoryInterface
     */
    private $videoPublisher;

    public function __construct(
        VideoDemandAdTagRepositoryInterface $videoDemandAdTagRepository,
        VideoWaterfallTagRepositoryInterface $videoWaterfallTagRepository,
        VideoDemandPartnerRepositoryInterface $videoDemandPartnerRepository,
        PublisherManagerInterface $publisherManager,
        VideoPublisherRepositoryInterface $videoPublisher)
    {
        $this->publisherManager = $publisherManager;
        $this->videoDemandAdTagRepository = $videoDemandAdTagRepository;
        $this->videoWaterfallTagRepository = $videoWaterfallTagRepository;
        $this->videoDemandPartnerRepository = $videoDemandPartnerRepository;
        $this->videoPublisher = $videoPublisher;
    }

    /**
     * @param string $reportTypeName
     * @param FilterParameterInterface $filterParameter
     * @return array|bool
     */
    public function getEntitiesByFilterParam($reportTypeName, FilterParameterInterface $filterParameter)
    {
//        $filterParameter = $this->getActivePublisherInFilter($filterParameter);

        if (PlatformAdSourceReportType::REPORT_TYPE == $reportTypeName
            || DemandPartnerDemandAdTagReportType::REPORT_TYPE == $reportTypeName
        ) {
            return $this->getVideoDemandAdTagsByFilterParams($filterParameter);
        }

        if (PlatformAdTagReportType::REPORT_TYPE == $reportTypeName) {
            return $this->getWaterfallTagsByFilterParams($filterParameter);
        }

        if (DemandPartnerDemandPartnerReportType::REPORT_TYPE == $reportTypeName) {
            return $this->getDemandPartnersByFilterParams($filterParameter);
        }

        if (PlatformAccountReportType::REPORT_TYPE == $reportTypeName) {
            return $this->getPublisherByFilterParams($filterParameter);
        }

        if (PlatformPublisherReportType::REPORT_TYPE == $reportTypeName) {
            return $this->getVideoPublisherByFilterParams($filterParameter);
        }

        if (PlatformPlatformReportType::REPORT_TYPE == $reportTypeName) {
            return $this->getAllActivePublisher();
        }

        return false;
    }

    /**
     * Remove all publisher in active
     * @param FilterParameterInterface $filterParameter
     * @return FilterParameterInterface
     */
    public function getActivePublisherInFilter(FilterParameterInterface $filterParameter)
    {
        $activePublishersId = [];

        if(!empty($filterParameter->getPublishers())) {
            $publisherIds= $filterParameter->getPublishers();
            foreach ($publisherIds as $publisherId) {
                /** @var UserEntityInterface  $publisher */
                $publisher = $this->publisherManager->find($publisherId);

                if(!$publisher instanceof PublisherInterface) {
                    continue;
                }

                if($publisher->isEnabled() && $publisher->hasVideoModule()) {
                    $activePublishersId[] = $publisher->getId();
                }
            }
        } else {
            /**@var PublisherInterface[] $activePublishers */
            $activePublishers = $this->publisherManager->allPublisherWithVideoModule();
            foreach ($activePublishers as $activePublisher) {
                $activePublishersId[] = $activePublisher->getId();
            }
        }

        $filterParameter->setPublisherId($activePublishersId);

        return $filterParameter;
    }

    public function getDemandPartnersByFilterParams(FilterParameterInterface $filterParameter)
    {
        return $this->videoDemandPartnerRepository->getVideoDemandPartnersByFilterParams($filterParameter);
    }

    /**
     * @param FilterParameterInterface $filterParameter
     * @return array
     */
    public function getVideoDemandAdTagsByFilterParams(FilterParameterInterface $filterParameter)
    {
        return $this->videoDemandAdTagRepository->getVideoDemandAdTagsByFilterParams($filterParameter);
    }

    /**
     * @return array
     */
    public function getAllActivePublisher()
    {
        return $this->publisherManager->allActivePublishers();
    }

    /**
     * @param FilterParameterInterface $filterParameter
     * @return array
     * @throws \Exception
     */
    public function getPublisherByFilterParams(FilterParameterInterface $filterParameter)
    {
        $publishers = [];

        $publisherIds = $filterParameter->getPublishers();
        if (empty($publisherIds)) {
            return $this->publisherManager->allPublisherWithVideoModule(); // get all publisher if not set in parameter
        }

        foreach ($publisherIds as $publisherId) {
            $publisher = $this->publisherManager->findPublisher($publisherId);
            if ($publisher instanceof PublisherInterface && $publisher->hasVideoModule()) {
                $publishers[] = $publisher;
            }
        }

        return $publishers;
    }

    /**
     * @param FilterParameterInterface $filterParameter
     * @return mixed
     */
    public function getWaterfallTagsByFilterParams(FilterParameterInterface $filterParameter)
    {
        return $this->videoWaterfallTagRepository->getVideoWaterfallTagsByFilterParams($filterParameter);
    }

    /**
     * Get video publisher by filter parameter
     * @param FilterParameterInterface $filterParameter
     * @return mixed
     */
    public function getVideoPublisherByFilterParams(FilterParameterInterface $filterParameter)
    {
        return $this->videoPublisher->getVideoPublishersByFilterParams($filterParameter);
    }
}