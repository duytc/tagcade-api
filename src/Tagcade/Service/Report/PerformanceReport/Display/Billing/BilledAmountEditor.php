<?php

namespace Tagcade\Service\Report\PerformanceReport\Display\Billing;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Tagcade\Bundle\UserBundle\DomainManager\UserManagerInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\UnexpectedValueException;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AbstractCalculatedReport;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\Platform\AdSlotReportInterface;
use Tagcade\Model\Report\PerformanceReport\Display\RootReportInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\GetRootReportTrait;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Params;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportBuilderInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\ReportSelectorInterface;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\Group\BilledReportGroup;
use Tagcade\Service\Report\PerformanceReport\Display\Selector\Result\ReportCollection;


class BilledAmountEditor implements BilledAmountEditorInterface
{
    use GetRootReportTrait;
    /**
     * @var ReportSelectorInterface
     */
    protected  $reportBuilder;
    /**
     * @var ObjectManager
     */
    protected $om;
    /**
     * @var BillingCalculatorInterface
     */
    protected $billingCalculator;
    /**
     * @var CpmRateGetterInterface
     */
    protected $rateGetter;
    /**
     * @var UserManagerInterface
     */
    protected $userManager;
    /**
     * @var DateUtilInterface
     */
    protected $dateUtil;

    function __construct(
        ReportBuilderInterface $reportBuilder,
        BillingCalculatorInterface $billingCalculator,
        ObjectManager $om,
        CpmRateGetterInterface $rateGetter,
        UserManagerInterface $userManager,
        DateUtilInterface $dateUtil
    )
    {
        $this->reportBuilder     = $reportBuilder;
        $this->billingCalculator = $billingCalculator;
        $this->om                = $om;
        $this->rateGetter        = $rateGetter;
        $this->userManager       = $userManager;
        $this->dateUtil          = $dateUtil;
    }

    /**
     * @inheritdoc
     */
    public function updateBilledAmountForPublisher(PublisherInterface $publisher, $billingRate, DateTime $startDate, DateTime $endDate)
    {
        $params = new Params($startDate, $endDate);
        /**
         * @var AdSlotReportInterface $reportRow
         */
        $this->doUpdateBilledAmountForPublisher($publisher, $billingRate, $params);
        $publisher->getUser()->setBillingRate($billingRate); // set custom rate for publisher

        return $this;
    }

    public function updateBilledAmountToCurrentDateForPublisher(PublisherInterface $publisher, DateTime $date = null)
    {
        if (null === $date) {
            $date = new DateTime('yesterday');
        }

        $today = new DateTime('today');

        if ($date >= $today) {
            return false; // nothing updated for first day of month, because update can only be done with yesterday of the same month
        }

        $params = new Params($this->dateUtil->getFirstDateOfMonth($date), $this->dateUtil->getLastDateOfMonth($date));
        $params->setGrouped(true);

        try {
            /**
             * @var BilledReportGroup $reportGroup
             */
            $reportGroup = $this->reportBuilder->getPublisherReport($publisher, $params);
            $newBilledRate = $this->rateGetter->getBilledRateForPublisher($publisher, $reportGroup->getSlotOpportunities());
            $lastRate = $this->rateGetter->getLastRateForPublisher($publisher);

            if ($lastRate !== $newBilledRate) {
                // TODO set last rate for publisher then do update billedAmount
                $this->doUpdateBilledAmountForPublisher($publisher, $newBilledRate, $params);

                return true; // 1 publisher updated
            }
        }
        catch(UnexpectedValueException $ex) {
            // TODO print warning data of no content causing unexpected value in report grouper
        }

        return false; // none is updated
    }

    public function updateBilledAmountToCurrentDateForAllPublishers()
    {
        $publishers = $this->userManager->allPublisherRoles();
        $updatedPublisherCount = 0;

        foreach ($publishers as $publisher) {
            $updatedPublisherCount += $this->updateBilledAmountToCurrentDateForPublisher($publisher);
        }

        return $updatedPublisherCount;
    }


    protected function doUpdateBilledAmountForPublisher(PublisherInterface $publisher, $billedRate, Params $param)
    {
        if( !is_numeric($billedRate) || $billedRate < 0) {
            throw new InvalidArgumentException('billing rate must be a float and positive number');
        }

        $reportResult = $this->reportBuilder->getPublisherAdSlotsReport($publisher, $param);

        $rootReports = [];

        /**
         * @var AbstractCalculatedReport $reportRow
         * @var ReportCollection $report
         */
        foreach($reportResult->getReports() as $report) {
            foreach ($report->getReports() as $reportRow) {
                $billedAmount = $this->billingCalculator->calculateBilledAmount($billedRate, $reportRow->getSlotOpportunities());
                $reportRow->setBilledAmount($billedAmount);
                $reportRow->setBilledRate($billedRate);
                $root = $this->getRootReport($reportRow);
                
                if (!in_array($root, $rootReports, true)) {
                    $rootReports[] = $root;
                }

                unset($root);
            }
        }

        unset($report);
        unset($reportRow);

        // Step 2. update calculated fields from top level (Platform) to sub level (Account, Site, AdSlot,)
        foreach ($rootReports as $report) {
            // very important, must be called manually because doctrine preUpdate listener doesn't work if changes happen in associated entities.
            /**
             * @var RootReportInterface $report
             */
            $report->setCalculatedFields();
        }

        // Step 3. Update database
        $this->om->flush();
    }

} 