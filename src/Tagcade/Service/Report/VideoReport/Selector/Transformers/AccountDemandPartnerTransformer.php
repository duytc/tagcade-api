<?php


namespace Tagcade\Service\Report\VideoReport\Selector\Transformers;


use Tagcade\Entity\Report\VideoReport\Hierarchy\Platform\AccountReport;
use Tagcade\Model\Report\VideoReport\Hierarchy\DemandPartner\DemandPartnerReport;
use Tagcade\Model\Report\VideoReport\Hierarchy\Platform\AccountReportInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandPartner;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Account;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameter;
use Tagcade\Service\Report\VideoReport\Parameter\BreakDownParameterInterface;
use Tagcade\Service\Report\VideoReport\Parameter\FilterParameterInterface;

class AccountDemandPartnerTransformer extends AbstractTransformer implements TransformerInterface
{
    /**
     * @return mixed target class need be transformed to
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
        if (!$report instanceof DemandPartnerReport) {
            return false;
        }

        return $report->getVideoDemandPartner()->getPublisher();
    }

    /**
     * @inheritdoc
     */
    public function supportsReportTypeAndBreakdown(ReportTypeInterface $reportType, BreakDownParameterInterface $breakDownParameter, FilterParameterInterface $filterParameter)
    {
        return ($reportType instanceof DemandPartner && $breakDownParameter->getMinBreakdown() == BreakDownParameter::PUBLISHER_KEY);
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