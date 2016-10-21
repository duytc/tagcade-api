<?php


namespace Tagcade\Service\Report\VideoReport\Billing;


use DateTime;
use Tagcade\Domain\DTO\Report\RateAmount;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Report\VideoReport\Hierarchy\Platform\VideoAccountReportRepositoryInterface;
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
     * @var VideoAccountReportRepositoryInterface
     */
    private $accountReportRepository;
    /**
     * @var DateUtilInterface
     */
    private $dateUtil;

    /**
     * BillingCalculator constructor.
     * @param CpmRateGetterInterface $cpmRateGetter
     * @param VideoAccountReportRepositoryInterface $accountReportRepository
     * @param DateUtilInterface $dateUtil
     */
    public function __construct(CpmRateGetterInterface $cpmRateGetter, VideoAccountReportRepositoryInterface $accountReportRepository, DateUtilInterface $dateUtil)
    {
        $this->cpmRateGetter = $cpmRateGetter;
        $this->accountReportRepository = $accountReportRepository;
        $this->dateUtil = $dateUtil;
    }

    public function calculateVideoBilledAmountForPublisherForSingleDay(DateTime $date, PublisherInterface $publisher, $module, $newWeight)
    {
        if ($newWeight < 0 || !is_numeric($newWeight)) {
            throw new InvalidArgumentException('$newWeight must be a number');
        }

        $firstDateInMonth = $this->dateUtil->getFirstDateInMonth($date);
        $yesterday = date_create($date->format('Y-m-d'))->modify('-1 day');

        $weight = $this->accountReportRepository->getSumVideoImpressionsForPublisher($publisher, $firstDateInMonth, $yesterday);
        $weight += $newWeight;

        $cpmRate = $this->cpmRateGetter->getCpmRateForPublisher($publisher, $module, $weight);

        return new RateAmount($cpmRate, $this->calculateBilledAmount($cpmRate->getCpmRate(), $newWeight));
    }
}