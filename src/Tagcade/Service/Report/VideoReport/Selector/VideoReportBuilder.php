<?php


namespace Tagcade\Service\Report\VideoReport\Selector;

use Tagcade\Exception\NotSupportedException;
use Tagcade\Model\ModelInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandPartner as DemandPartnerReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandAdTag as DemandPartnerDemandAdTagReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Account as PlatformAccountReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\DemandAdTag as PlatformDemandAdTagReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Platform as PlatformReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Publisher as PlatformPublisherReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\WaterfallTag as PlatformWaterfallReportType;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;
use Tagcade\Service\Report\VideoReport\VideoEntityService;


class VideoReportBuilder implements VideoReportBuilderInterface
{
    static $SUPPORTED_REPORT_TYPES = [
        PlatformAccountReportType::class,
        PlatformDemandAdTagReportType::class,
        PlatformWaterfallReportType::class,
        PlatformReportType::class,
        PlatformPublisherReportType::class,
        DemandPartnerReportType::class,
        DemandPartnerDemandAdTagReportType::class
    ];

    /**
     * @var ReportSelectorInterface
     */
    private $videoReportSelector;

    /**
     * @var VideoEntityService
     */
    private $videoEntityService;

    function __construct(ReportSelectorInterface $videoReportSelector, VideoEntityService $videoEntityService)
    {
        $this->videoReportSelector = $videoReportSelector;
        $this->videoEntityService = $videoEntityService;
    }

    /**
     * @param FilterParameterInterface $filterParameter
     * @param BreakDownParameterInterface $breakDownParameter
     * @return mixed
     * @throws \Exception
     */
    public function getReports(FilterParameterInterface $filterParameter, BreakDownParameterInterface $breakDownParameter)
    {
        /* 1. get all reportTypes with common params, BUT WITHOUT filtered entities */
        /** @var array|ReportTypeInterface|ReportTypeInterface[] $reportTypes */
        $reportTypes = $this->getReportTypes($filterParameter, $breakDownParameter);

        /* 2. using report selector to get report due to a single reportType */
        if (!is_array($reportTypes)) {
            return $this->videoReportSelector->getReport($reportTypes, $filterParameter, $breakDownParameter);
        }

        /* 2'. using report selector to get report due to an array reportTypes */
        return $this->videoReportSelector->getMultipleReports($reportTypes, $filterParameter, $breakDownParameter);
    }

    /**
     * @param FilterParameterInterface $filterParameter
     * @param BreakDownParameterInterface $breakDownParameter
     * @return mixed
     * @throws \Exception
     */
    public function getReportsHourly(FilterParameterInterface $filterParameter, BreakDownParameterInterface $breakDownParameter)
    {
        /* 1. get all reportTypes with common params, BUT WITHOUT filtered entities */
        /** @var array|ReportTypeInterface|ReportTypeInterface[] $reportTypes */
        $reportTypes = $this->getReportTypes($filterParameter, $breakDownParameter);

        /* 2. using report selector to get report due to a single reportType */
        if (!is_array($reportTypes)) {
            return $this->videoReportSelector->getReportHourly($reportTypes, $filterParameter, $breakDownParameter);
        }

        /* 2'. using report selector to get report due to an array reportTypes */
        return $this->videoReportSelector->getMultipleReportsHourly($reportTypes, $filterParameter, $breakDownParameter);
    }

    /**
     * @param FilterParameterInterface $filterParameter
     * @param BreakDownParameterInterface $breakDownParameter
     * @return array|null
     * @throws \Exception
     */
    protected function getReportTypes(FilterParameterInterface $filterParameter, BreakDownParameterInterface $breakDownParameter)
    {
        $startDate = $filterParameter->getStartDate();
        $endDate = $filterParameter->getEndDate();

        if (!isset($startDate) || !isset($endDate)) {
            throw new \Exception('Start date or end date must be set to get video reports');
        }

        // get reportType
        /** @var ReportTypeInterface $reportType */
        $reportType = null;
        $reportTypeClass = null;

        foreach (self::$SUPPORTED_REPORT_TYPES as $supportReportTypeClass) {
            /** @var ReportTypeInterface $supportedReportType */
            $supportedReportType = $this->createReportTypeInstance($supportReportTypeClass); // only object for checking 'isSupportParams'

            if ($supportedReportType->isSupportParams($filterParameter, $breakDownParameter)) {
                $reportType = $supportedReportType;
                $reportTypeClass = $supportReportTypeClass;
                break;
            }
        }

        if (null == $reportType) {
            throw new NotSupportedException('Could not found satisfied report type for these filter and breakDown Parameters');
        }

        // get all entities due to reportType and filterParam
        $entities = $this->videoEntityService->getEntitiesByFilterParam($reportType->getReportType(), $filterParameter);

        if ($reportType instanceof PlatformReportType) {
            $reportType = new PlatformReportType($entities); // Recreate report type with argument
            return $reportType;
        }

        // create all reportTypes for all entities
        $reportTypes = [];

        foreach ($entities as $entity) {
            $reportTypes[] = $this->createReportTypeInstance($reportTypeClass, $entity);
        }

        return $reportTypes;
    }

    /**
     * create ReportType Instance
     *
     * @param string $reportTypeClass
     * @param null|ModelInterface $entity
     * @return false|ModelInterface
     */
    protected function createReportTypeInstance($reportTypeClass, $entity = null)
    {
        $r = new \ReflectionClass($reportTypeClass);

        /** @var ReportTypeInterface $reportType */
        $reportType = $r->newInstanceArgs([$entity]);

        return $reportType;
    }
}