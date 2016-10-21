<?php

namespace Tagcade\Service\Report\VideoReport\Creator\Creators\Hierarchy\Platform;

use Tagcade\Model\Core\VideoDemandAdTagInterface;
use Tagcade\Model\Core\VideoWaterfallTagItemInterface;
use Tagcade\Service\Report\VideoReport\Billing\BillingCalculatorInterface;
use Tagcade\Service\Report\VideoReport\Creator\Creators\CreatorAbstract;
use Tagcade\Entity\Report\VideoReport\Hierarchy\Platform\WaterfallTagReport;
use Tagcade\Model\Report\VideoReport\ReportType\ReportTypeInterface;
use Tagcade\Service\Report\VideoReport\Creator\Creators\HasSubReportsTrait;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\DemandAdTag as DemandAdTagReportType;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\WaterfallTag as WaterfallTagReportType;
use Tagcade\Bundle\UserBundle\Entity\User as AbstractUser;

class WaterfallTag extends CreatorAbstract implements WaterfallTagInterface
{
    use HasSubReportsTrait;
    /**
     * @var BillingCalculatorInterface
     */
    protected  $billingCalculator;

    public function __construct(DemandAdTagInterface $subReportCreator, BillingCalculatorInterface $billingCalculator)
    {
        $this->subReportCreator = $subReportCreator;
        $this->billingCalculator = $billingCalculator;
    }

    /**
     * @inheritdoc
     */
    public function doCreateReport(WaterfallTagReportType $reportType)
    {
        $this->syncEventCounterForSubReports();

        $report = new WaterfallTagReport();

        $videoWaterfallTag = $reportType->getVideoWaterfallTag();
        $videoWaterfallTagUuid = $videoWaterfallTag->getUuid();

        if (!$this->isValidUuidV4($videoWaterfallTagUuid)) {
            return $report; // skip process if invalid uuid!!!
        }

        $report
            ->setAdTagBids($this->eventCounter->getVideoWaterfallTagBidCount($videoWaterfallTagUuid))
            ->setAdTagErrors($this->eventCounter->getVideoWaterfallTagErrorCount($videoWaterfallTagUuid))
            ->setAdTagRequests($this->eventCounter->getVideoWaterfallTagRequestCount($videoWaterfallTagUuid));

        $report->setVideoWaterfallTag($videoWaterfallTag)
               ->setDate($this->getDate());

        $impressions = 0;
        /**
         * @var VideoWaterfallTagItemInterface $adTagItem
         * @var VideoDemandAdTagInterface $videoDemandAdTag
         */
        foreach ($videoWaterfallTag->getVideoWaterfallTagItems() as $adTagItem) {
            foreach($adTagItem->getVideoDemandAdTags() as $videoDemandAdTag) {
                $report->addSubReport(
                    $this->subReportCreator->createReport(new DemandAdTagReportType($videoDemandAdTag))
                        ->setSuperReport($report)
                );
                $impressions += $this->eventCounter->getVideoDemandAdTagImpressionsCount($videoDemandAdTag->getId());
            }
        }

        $rateAmount = $this->billingCalculator->calculateVideoBilledAmountForPublisherForSingleDay($this->getDate(), $videoWaterfallTag->getPublisher(), AbstractUser::MODULE_VIDEO, $impressions);
        $report->setBilledAmount($rateAmount->getAmount());
        $report->setBilledRate($rateAmount->getRate()->getCpmRate());

        if ($rateAmount->getRate()->isCustom()) {
            $report->setCustomRate($rateAmount->getRate()->getCpmRate());
        }
        return $report;
    }

    /**
     * @inheritdoc
     */
    public function supportsReportType(ReportTypeInterface $reportType)
    {
        return $reportType instanceof WaterfallTagReportType;
    }

    /**
     * validate that an uuid string is an uuid version 4
     * the format of uuid version 4: xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx
     *
     * @param $uuid4
     * @return bool
     */
    private function isValidUuidV4($uuid4)
    {
        return (bool)preg_match('/[a-f0-9]{8}\-[a-f0-9]{4}\-4[a-f0-9]{3}\-(8|9|a|b)[a-f0-9]{3}\-[a-f0-9]{12}/', $uuid4, $m);
    }
}