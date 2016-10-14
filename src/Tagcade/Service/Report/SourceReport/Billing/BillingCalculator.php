<?php

namespace Tagcade\Service\Report\SourceReport\Billing;


use DateTime;
use Tagcade\Domain\DTO\Report\RateAmount;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Repository\Report\PerformanceReport\Display\Hierarchy\Platform\AccountReportRepositoryInterface;
use Tagcade\Repository\Report\SourceReport\ReportRepositoryInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\Behaviors\CalculateBilledAmountTrait;

class BillingCalculator implements BillingCalculatorInterface
{
    use CalculateBilledAmountTrait;
    /**
     * @var CpmRateGetterInterface
     */
    private $cpmRateGetter;
    /**
     * @var AccountReportRepositoryInterface
     */
    private $reportRepository;
    /**
     * @var DateUtilInterface
     */
    private $dateUtil;

    function __construct(CpmRateGetterInterface $defaultRateGetter, ReportRepositoryInterface $reportRepository, DateUtilInterface $dateUtil)
    {
        $this->cpmRateGetter = $defaultRateGetter;
        $this->reportRepository = $reportRepository;
        $this->dateUtil = $dateUtil;
    }

    public function calculateBilledAmountForSiteForSingleDate(DateTime $date, SiteInterface $site, $module, $newWeight)
    {
        if (!is_int($newWeight) || $newWeight < 0) {
            throw new InvalidArgumentException('$newWeight must be a number');
        }

        $weight = $this->cpmRateGetter->getBillingWeightForSiteInMonthBeforeDate($site, $module, $date);
        $weight += $newWeight;

        $cpmRate = $this->cpmRateGetter->getCpmRateForPublisher($site->getPublisher(), $module, $weight);

        return new RateAmount($cpmRate, $this->calculateBilledAmount($cpmRate->getCpmRate(), $newWeight));
    }
}