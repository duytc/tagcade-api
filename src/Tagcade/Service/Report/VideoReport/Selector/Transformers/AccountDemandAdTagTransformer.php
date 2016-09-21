<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Transformers;


use Tagcade\Entity\Report\VideoReport\Hierarchy\Platform\AccountReport;
use Tagcade\Model\Core\VideoWaterfallTagItemInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\AccountReportInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\DemandAdTagReportInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Account;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\DemandAdTag;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameter;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class AccountDemandAdTagTransformer extends AbstractTransformer implements TransformerInterface
{
    /**
     * @inheritdoc
     */
    protected function getParentObject(ReportInterface $report)
    {
        if (!$report instanceof DemandAdTagReportInterface) {
            return false;
        }

        $adTagItem = $report->getVideoDemandAdTag()->getVideoWaterfallTagItem();

        if (!$adTagItem instanceof VideoWaterfallTagItemInterface) {
            return false;
        }

        return $adTagItem->getVideoWaterfallTag()->getPublisher();
    }

    /**
     * @inheritdoc
     */
    protected function getTargetClass()
    {
        return AccountReport::class;
    }

    /**
     * @inheritdoc
     */
    protected function getReportTypeClass()
    {
        return Account::class;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportTypeAndBreakdown(ReportTypeInterface $reportType, BreakDownParameterInterface $breakDownParameter, FilterParameterInterface $filterParameter)
    {
        return ($reportType instanceof DemandAdTag && $breakDownParameter->getMinBreakdown() == BreakDownParameter::PUBLISHER_KEY);
    }

    /**
     * @inheritdoc
     */
    protected  function aggregateChildReport(ReportInterface $parentReport, ReportInterface $childReport)
    {
        parent::aggregateChildReport($parentReport, $childReport);
        
        /**@var AccountReportInterface $parentReport */
        $parentReport->setPublisher($this->getParentObject($childReport));
        $parentReport->setAdTagRequests(null);
        $parentReport->setAdTagBids(null);
        $parentReport->setAdTagErrors(null);

        return $parentReport;
    }
}