<?php

namespace Tagcade\Bundle\ReportApiBundle\Command;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Report\PerformanceReport\Display\Hierarchy\AdNetwork\AdNetworkReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\AdNetwork\AdNetwork as AdNetworkReportType;

class DailyAdNetworkRotateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:report:daily-rotate:ad-network')
            ->addOption('id', 'i', InputOption::VALUE_REQUIRED, 'ad network id')
            ->setDescription('Daily rotate ad network report.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $id = $input->getOption('id');

        /** @var \Psr\Log\LoggerInterface $logger */
        $logger = $container->get('logger');

        $entityManager = $container->get('doctrine.orm.entity_manager');
        $reportCreator = $container->get('tagcade.service.report.performance_report.display.creator.report_creator');
        $adNetworkManager = $container->get('tagcade.domain_manager.ad_network');

        $adNetwork = $adNetworkManager->find($id);
        if (!$adNetwork instanceof AdNetworkInterface) {
            throw new \Exception(sprintf('Not found that ad network %s', $id));
        }

        $reportCreator->setDate(new DateTime('yesterday'));
        
        /* create performance and billing reports */
        $logger->info('start daily rotate for performance');
        /**
         * @var AdNetworkReport $adNetworkReport
         */
        $adNetworkReport = $reportCreator->getReport(
            new AdNetworkReportType($adNetwork)
        );

        $logger->info(sprintf('Persisting report for ad network %s', $id));

        $entityManager->persist($adNetworkReport);

        $logger->info(sprintf('Flushing report for ad network %s', $id));

        $entityManager->flush();

        $logger->info('finished daily rotation');
    }
}
