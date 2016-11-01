<?php

namespace Tagcade\Bundle\ReportApiBundle\Command;

use DateTime;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Tagcade\Entity\Report\VideoReport\Hierarchy\Platform\AccountReport;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Report\VideoReport\ReportInterface;
use Tagcade\Model\Report\VideoReport\ReportType\Hierarchy\Platform\Account as AccountReportType;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\UserEntityInterface;

class VideoDailyAccountRotateCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:video-report:daily-rotate:account')
            ->addOption('id', 'i', InputOption::VALUE_REQUIRED, 'publisher id')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'force to override existing data on the given date')
            ->addOption('date', 'd', InputOption::VALUE_OPTIONAL, 'date to rotate data')
            ->setDescription('Daily rotate publisher report.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
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

        /** @var \Psr\Log\LoggerInterface $logger */
        $logger = $container->get('logger');

        $entityManager = $container->get('doctrine.orm.entity_manager');
        $reportCreator = $container->get('tagcade.service.report.video_report.creator.report_creator');
        $publisherManager = $container->get('tagcade_user.domain_manager.publisher');
        $accountReportRepository = $container->get('tagcade.repository.report.video_report.hierarchy.platform.account');

        $publisher = $publisherManager->findPublisher($id);
        /** @var PublisherInterface|UserEntityInterface $publisher */
        if (!$publisher instanceof PublisherInterface || $publisher->isTestAccount() || !$publisher->isEnabled()) {
            throw new \Exception(sprintf('Not found that publisher %s', $id));
        }

        if (!$publisher->hasVideoModule()) {
            throw new \Exception(sprintf('publisher %d does not have module VIDEO enabled', $id));
        }

        $report = current($accountReportRepository->getReportsFor($publisher, $date, $date));
        if ($report instanceof ReportInterface && $override === false) {
            throw new RuntimeException('report for the given date is already existed, use "--force" option to override.');
        }

        $reportCreator->setDate($date);
        $logger->info('start daily rotate for account');
        /**
         * @var AccountReport $accountReport
         */
        $accountReport = $reportCreator->getReport(
            new AccountReportType($publisher)
        );

        if ($override === true && $report instanceof ReportInterface) {
            $entityManager->remove($report);
            $entityManager->flush();
            unset($report);
        }

        $logger->info(sprintf('Persisting report for publisher %s', $id));
        $entityManager->persist($accountReport);
        $logger->info(sprintf('Flushing report for publisher %s', $id));
        $entityManager->flush();
        $entityManager->clear();
        gc_collect_cycles();
        unset($accountReport);
        $logger->info('finished account daily rotation');
    }


}
