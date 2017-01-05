<?php

namespace Tagcade\Bundle\ReportApiBundle\Command;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Entity\Report\VideoReport\Hierarchy\DemandPartner\DemandPartnerReport;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\VideoDemandPartnerInterface;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\DemandPartner\DemandPartner as DemandPartnerReportType;

class VideoDailyDemandPartnerRotateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:video-report:daily-rotate:demand-partner')
            ->addOption('date', 'd', InputOption::VALUE_OPTIONAL, 'the date to rotate data')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'force to override existing data on the given date')
            ->addOption('id', 'i', InputOption::VALUE_REQUIRED, 'ad network id')
            ->setDescription('Daily rotate video demand partner report.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        /** @var \Psr\Log\LoggerInterface $logger */
        $logger = $container->get('logger');
        $id = $input->getOption('id');
        $date = $input->getOption('date');
        $override = filter_var($input->getOption('force'), FILTER_VALIDATE_BOOLEAN);

        if (empty($date)) {
            $date = new DateTime('yesterday');
        } else if (!preg_match('/\d{4}-\d{2}-\d{2}/', $date)) {
            throw new InvalidArgumentException('expect date format to be "YYYY-MM-DD"');
        } else {
            $date = DateTime::createFromFormat('Y-m-d', $date);
        }

        if ($date->setTime(0,0,0) == new DateTime('today')) {
            $logger->error(sprintf('can not create today video report for demand partner %d', $id));
            return;
        }

        $entityManager = $container->get('doctrine.orm.entity_manager');
        $reportCreator = $container->get('tagcade.service.report.video_report.creator.report_creator');
        $demandPartnerManager = $container->get('tagcade.domain_manager.video_demand_partner');
        $demandPartnerReportRepository = $container->get('tagcade.repository.report.video_report.hierarchy.demand_partner.demand_partner');

        $demandPartner = $demandPartnerManager->find($id);
        if (!$demandPartner instanceof VideoDemandPartnerInterface) {
            $logger->error(sprintf('demand partner %d does not exist', $id));
            return;
        }

        $report = current($demandPartnerReportRepository->getReportsFor($demandPartner, $date, $date));
        if ($report instanceof ReportInterface && $override === false) {
            $logger->error(sprintf('report for demand partner %d on %s is already existed, use "--force" option to override', $id, $date->format('Y-m-d')));
            return;
        }

        $reportCreator->setDate($date);
        /* create performance and billing reports */
        $logger->info(sprintf('start daily rotate for video demand partner %d', $demandPartner->getId()));
        /**
         * @var DemandPartnerReport $demandPartnerReport
         */
        $demandPartnerReport = $reportCreator->getReport(
            new DemandPartnerReportType($demandPartner)
        );

        if ($override === true && $report instanceof ReportInterface) {
            $entityManager->remove($report);
            $entityManager->flush();
            unset($report);
        }

        $logger->info(sprintf('Persisting report for video demand partner %s', $id));
        $entityManager->persist($demandPartnerReport);
        $logger->info(sprintf('Flushing report for video demand partner %s', $id));
        $entityManager->flush();
        $entityManager->clear();
        gc_collect_cycles();
        unset($demandPartnerReport);
        $logger->info(sprintf('finish daily rotate for video demand partner %d', $demandPartner->getId()));
    }
}
