<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Transformers;


use Tagcade\Model\ModelInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandPartnerWaterfallTag;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\VideoPublisherDemandPartner;

abstract class AbstractTransformer implements TransformerInterface
{
    /**
     * @inheritdoc
     */
    public function transform(array $reports)
    {
        $parentReportsRaw = [];
        $parentObjects = [];
        $reportTypes =[];

        /** @var ReportInterface $report */
        foreach ($reports as $report) {
            $date = $report->getDate();
            $parentId = $this->getParentId($report);

            $key = sprintf('%s:%s', $parentId , $date->format('Y-m-d'));  // format key: 'parentId:y-m-d'
            if (!array_key_exists($key, $parentReportsRaw)) {
                $parentReportsRaw[$key] = [];
            }
            $parentReportsRaw[$key][] = $report;

            if (!in_array($this->getParentObject($report), $parentObjects)){
                $parentObjects[] = $this->getParentObject($report);
            }
        }

        $parentReports = [];

        foreach ($parentReportsRaw as $key => $childReports) {
            /** @var ReportInterface $parentReport */
            $parentReport = (new \ReflectionClass($this->getTargetClass()))->newInstance();

            foreach ($childReports as $childReport) {
                $parentReport = $this->aggregateChildReport($parentReport, $childReport);
            }
            $parentReports[$key] = $parentReport;
        }

        foreach ($parentObjects as $parentObject) {
            if (!is_array($parentObject)) {
                $reportTypes[] = (new \ReflectionClass($this->getReportTypeClass()))->newInstance($parentObject);
            } else {
                //Transform to DemandPartnerWaterfallTag report
                if (array_key_exists('videoWaterfallTag',$parentObject)) {
                    $videoWaterfallTag = $parentObject['videoWaterfallTag'];
                    $videoDemandPartner = $parentObject['videoDemandPartner'];
                    $reportTypes[] = new DemandPartnerWaterfallTag($videoDemandPartner,$videoWaterfallTag);
                }
                //Transform to VideoPublisherDemandPartnerReportReport report
                if (array_key_exists('videoPublisher',$parentObject)) {
                    $videoPublisher = $parentObject['videoPublisher'];
                    $videoDemandPartner = $parentObject['videoDemandPartner'];
                    $reportTypes[] = new VideoPublisherDemandPartner($videoPublisher,$videoDemandPartner);
                }
            }
        }

        $reportResult['reportType'] = $reportTypes;
        $reportResult['reports'] = $parentReports;

        return $reportResult;
    }

    /**
     * add (do summary) a childReport to a parent report
     * @param ReportInterface $parentReport
     * @param ReportInterface $childReport
     * @return ReportInterface return modified $parentReport
     */
    protected function aggregateChildReport(ReportInterface $parentReport, ReportInterface $childReport)
    {
        $parentReport->setRequests($parentReport->getRequests() + $childReport->getRequests());
        $parentReport->setBids($parentReport->getBids() + $childReport->getBids());
        $parentReport->setClicks($parentReport->getClicks() + $childReport->getClicks());
        $parentReport->setImpressions($parentReport->getImpressions() + $childReport->getImpressions());
        $parentReport->setBlocks($parentReport->getBlocks() + $childReport->getBlocks());
        $parentReport->setErrors($parentReport->getErrors() + $childReport->getErrors());
        $parentReport->setDate($childReport->getDate());

        $bidRate = ($parentReport->getRequests() > 0) ? ($parentReport->getBids()/$parentReport->getRequests()): 0;
        $parentReport->setBidRate($bidRate);
        $errorRate = ($parentReport->getImpressions() >0) ? $parentReport->getErrors()/$parentReport->getImpressions(): 0;
        $parentReport->setErrorRate($errorRate);
        $requestFillRate = ($parentReport->getRequests() >0) ? $parentReport->getImpressions()/$parentReport->getRequests() : 0;
        $parentReport->setRequestFillRate($requestFillRate);
        $clickThoughRate = ($parentReport->getImpressions() >0) ? $parentReport->getClicks()/$parentReport->getImpressions():0;
        $parentReport->setClickThroughRate($clickThoughRate);

        return $parentReport;
    }

    /**
     * @return mixed target class need be transformed to
     */
    abstract protected function getTargetClass();

    /**
     * @return mixed report type class of transformed class
     */
    abstract protected function getReportTypeClass();

    /**
     * get Parent Id for a report
     *
     * @param ReportInterface $report
     * @return false|int false if not support report or not found id
     */
    protected function getParentId(ReportInterface $report)
    {
        /** @var ModelInterface $object */
        $object = $this->getParentObject($report);

        return $object->getId();
    }

    /**
     * get Parent object for a report
     *
     * @param ReportInterface $report
     * @return false|int false if not support report or not found id
     */
    abstract protected function getParentObject(ReportInterface $report);
}