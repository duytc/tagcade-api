<?php


namespace Tagcade\Service\Report\SourceReport\Billing;


use DateTime;
use Tagcade\Domain\DTO\Report\RateAmount;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\SourceReport\ReportRepositoryInterface;
use Tagcade\Service\DateUtilInterface;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\Behaviors\CalculateBilledAmountTrait;
use Tagcade\Service\Report\PerformanceReport\Display\Billing\CpmRateGetterInterface;

class BillingCalculator implements BillingCalculatorInterface
{
    use CalculateBilledAmountTrait;
    /**
     * @var CpmRateGetterInterface
     */
    protected $cpmRateGetter;

    /**
     * @var ReportRepositoryInterface
     */
    protected $sourceReportRepository;

    /**
     * @var DateUtilInterface
     */
    protected $dateUtil;

    /**
     * BillingCalculator constructor.
     * @param CpmRateGetterInterface $cpmRateGetter
     * @param ReportRepositoryInterface $sourceReportRepository
     * @param DateUtilInterface $dateUtil
     */
    public function __construct(CpmRateGetterInterface $cpmRateGetter, ReportRepositoryInterface $sourceReportRepository, DateUtilInterface $dateUtil)
    {
        $this->cpmRateGetter = $cpmRateGetter;
        $this->sourceReportRepository = $sourceReportRepository;
        $this->dateUtil = $dateUtil;
    }


    public function calculateBilledAmountForPublisherForSingleDate(DateTime $date, PublisherInterface $publisher, $module, $newWeight)
    {
        if (!is_int($newWeight) || $newWeight < 0) {
            throw new InvalidArgumentException('$newWeight must be a number');
        }

        $weight = $this->cpmRateGetter->getBillingWeightForPublisherInMonthBeforeDate($publisher, $module, $date);
        $weight += $newWeight;

        $cpmRate = $this->cpmRateGetter->getCpmRateForPublisher($publisher, $module, $weight);

        return new RateAmount($cpmRate, $this->calculateBilledAmount($cpmRate->getCpmRate(), $newWeight));
    }
}