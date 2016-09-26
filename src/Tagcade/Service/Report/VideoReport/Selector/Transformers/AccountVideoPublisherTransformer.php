<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Transformers;


use Tagcade\Entity\Report\VideoReport\Hierarchy\Platform\AccountReport;
use Tagcade\Entity\Report\VideoReport\Hierarchy\Platform\PublisherReport;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\AccountReportInterface;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\PublisherReportInterface as VideoPublisherReportInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Account;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Publisher;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameter;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class AccountVideoPublisherTransformer extends AbstractTransformer implements TransformerInterface
{
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
    protected function getParentObject(ReportInterface $report)
    {
        if (!$report instanceof VideoPublisherReportInterface) {
            return false;
        }

        return $report->getVideoPublisher()->getPublisher();
    }

    /**
     * @inheritdoc
     */
    public function supportsReportTypeAndBreakdown(ReportTypeInterface $reportType, BreakDownParameterInterface $breakDownParameter, FilterParameterInterface $filterParameter)
    {
        return ($reportType instanceof Publisher && $breakDownParameter->getMinBreakdown() == BreakDownParameter::PUBLISHER_KEY);
    }

    /**
     * @inheritdoc
     */
    protected function aggregateChildReport(ReportInterface $parentReport, ReportInterface $childReport)
    {
        parent::aggregateChildReport($parentReport, $childReport);

        /**@var AccountReportInterface $parentReport */
        $parentReport->setPublisher($this->getParentObject($childReport));

        return $parentReport;
    }

}