<?php

namespace Tagcade\Service\Report\SourceReport\Billing;

use DateTime;
use Doctrine\ORM\EntityManager;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManager;
use Tagcade\Bundle\UserBundle\Entity\User;
use Tagcade\Entity\Report\SourceReport\Report;
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
        $sourceReports = $this->sourceReportRepository->getSourceReportsForPublisher($publisher, $date);

        $billingConfiguration = $this->billingConfigurationRepository->getConfigurationForModule($publisher, User::MODULE_VIDEO_ANALYTICS);
        if (!$billingConfiguration instanceof BillingConfiguration) {
            return;
        }

        $method = '';
        if ($billingConfiguration->getBillingFactor() === BillingConfiguration::VIDEO_IMPRESSION_BILLING_FACTOR) {
            $method = 'getVideoAdImpressions';
        } else if ($billingConfiguration->getBillingFactor() === BillingConfiguration::VISIT_BILLING_FACTOR) {
            $method = 'getVisits';
        }

        foreach ($sourceReports as $sourceReport) {
            if (!$sourceReport instanceof Report) {
                continue;
            }
            $newWeight = $sourceReport->$method();

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