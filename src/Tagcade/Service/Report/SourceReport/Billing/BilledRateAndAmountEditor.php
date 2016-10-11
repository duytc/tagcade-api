<?php

namespace Tagcade\Service\Report\SourceReport\Billing;

use DateTime;
use Doctrine\ORM\EntityManager;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManager;
use Tagcade\Bundle\UserBundle\Entity\User;
use Tagcade\Model\Core\BillingConfiguration;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\BillingConfigurationRepositoryInterface;
use Tagcade\Repository\Report\SourceReport\ReportRepositoryInterface;

class BilledRateAndAmountEditor implements BilledRateAndAmountEditorInterface
{
    /** @var PublisherManager */
    protected $publisherManager;

    /** @var EntityManager */
    protected $entityManager;

    /** @var BillingCalculatorInterface */
    protected $billingCalculator;

    /** @var ReportRepositoryInterface */
    protected $sourceReportRepository;

    /** @var BillingConfigurationRepositoryInterface */
    protected $billingConfigurationRepository;

    function __construct(
        PublisherManager $publisherManager,
        EntityManager $entityManager,
        BillingCalculatorInterface $billingCalculator,
        ReportRepositoryInterface $sourceReportRepository,
        BillingConfigurationRepositoryInterface $billingConfigurationRepository
    )
    {
        $this->publisherManager = $publisherManager;
        $this->entityManager = $entityManager;
        $this->billingCalculator = $billingCalculator;
        $this->sourceReportRepository = $sourceReportRepository;
        $this->billingConfigurationRepository = $billingConfigurationRepository;
    }

    /**
     * @inheritdoc
     */
    public function updateBilledRateAndBilledAmountSourceReportForPublisher(PublisherInterface $publisher, DateTime $date)
    {
        $sourceReports = $this->sourceReportRepository->getSourceReportsForPublisher($publisher);
        if (current($sourceReports) instanceof ReportRepositoryInterface) {
            throw new \Exception('Not found any source reports needed to update');
        }

        $billingConfiguration = $this->billingConfigurationRepository->getConfigurationForModule($publisher, User::MODULE_VIDEO_ANALYTICS);
        if (!$billingConfiguration instanceof BillingConfiguration) {
            return;
        }
        $billingFactor = $billingConfiguration->getBillingFactor();

        foreach ($sourceReports as $sourceReport) {
            $newWeight = 0;
            if ($billingFactor === BillingConfiguration::VIDEO_IMPRESSION_BILLING_FACTOR) {
                $newWeight = $sourceReport->getVideoAdImpressions();
            } else if ($billingFactor === BillingConfiguration::VISIT_BILLING_FACTOR) {
                $newWeight = $sourceReport->getVisits();
            }

            $rateAmount = $this->billingCalculator->calculateBilledAmountForPublisherForSingleDate($date, $publisher, User::MODULE_VIDEO_ANALYTICS, $newWeight);
            $billedRate = $rateAmount->getRate()->getCpmRate();
            $billedAmount = $rateAmount->getAmount();
            $sourceReport->setBilledRate($billedRate);
            $sourceReport->setBilledAmount($billedAmount);

            $this->entityManager->persist($sourceReport);
        }
        $this->entityManager->flush();
    }
}