<?php

namespace Tagcade\Bundle\ReportApiBundle\Command;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Entity\Report\PerformanceReport\Display\Platform\AccountReport;
use Tagcade\Model\Report\PerformanceReport\Display\ReportType\Hierarchy\Platform\Account as AccountReportType;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class DailyAccountRotateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:report:daily-rotate:account')
            ->addOption('id', 'i', InputOption::VALUE_REQUIRED, 'publisher id')
            ->setDescription('Daily rotate publisher report.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $id = $input->getOption('id');

        /** @var \Psr\Log\LoggerInterface $logger */
        $logger = $container->get('logger');

        $entityManager = $container->get('doctrine.orm.entity_manager');
        $reportCreator = $container->get('tagcade.service.report.performance_report.display.creator.report_creator');
        $publisherManager = $container->get('tagcade_user.domain_manager.publisher');

        $publisher = $publisherManager->findPublisher($id);
        /**
         * @var PublisherInterface|UserEntityInterface $publisher
         */
        if (!$publisher instanceof PublisherInterface || $publisher->isTestAccount() || !$publisher->isEnabled()) {
            throw new \Exception(sprintf('Not found that publisher %s', $id));
        }

        $reportCreator->setDate(new DateTime('yesterday'));

        $logger->info('start daily rotate for account');
        /**
         * @var AccountReport $accountReport
         */
        $accountReport = $reportCreator->getReport(
            new AccountReportType($publisher)
        );

        $logger->info(sprintf('Persisting report for publisher %s', $id));

        $entityManager->persist($accountReport);

        $logger->info(sprintf('Flushing report for publisher %s', $id));

        $entityManager->flush();

        $logger->info('finished account daily rotation');
    }
}
