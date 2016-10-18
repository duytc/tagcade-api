<?php

namespace Tagcade\Bundle\ReportApiBundle\Command;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Exception\InvalidArgumentException;
use Tagcade\Model\User\Role\PublisherInterface;

class UpdateBillingSourceReportForPublisherCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:report:update-billing-source-report')
            ->addOption('id', 'i', InputOption::VALUE_OPTIONAL, 'Publisher id to be update billing source report. Default is all publishers')
            ->addOption('date', 'd', InputOption::VALUE_OPTIONAL, 'date to update source reports')
            ->setDescription('Update billed rate and billed amount source reports for single publisher.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $id = $input->getOption('id');
        $date = $input->getOption('date');

        if (empty($date)) {
            $date = new DateTime('yesterday');
        } else if (!preg_match('/\d{4}-\d{2}-\d{2}/', $date)) {
            throw new InvalidArgumentException('expect date format to be "YYYY-MM-DD"');
        } else {
            $date = DateTime::createFromFormat('Y-m-d', $date);
        }

        /** @var \Psr\Log\LoggerInterface $logger */
        $logger = $container->get('logger');
        $publisherManager = $container->get('tagcade_user.domain_manager.publisher');
        $billingEditor = $container->get('tagcade.service.report.source_report.billing.billing_rate_and_amount_editor');

        $logger->info('start update billed source reports');

        $publishers = [];
        if ($id) {
            $publisher = $publisherManager->findPublisher($id);
            if (!$publisher instanceof PublisherInterface) {
                throw new \Exception(sprintf('Not found that publisher %s', $id));
            }

            $publishers[] = $publisher;
        } else {
            $logger->info('update billed source reports for all publishers');
            $publishers = $publisherManager->all();
        }

        /** @var PublisherInterface $publisher */
        foreach ($publishers as $publisher) {
            $logger->info(sprintf('start updating billing source report for publisher "%s"', $publisher->getUser()->getUsername()));
            $billingEditor->updateBilledRateAndBilledAmountSourceReportForPublisher($publisher, $date);
            $logger->info(sprintf('finish updating billing source report for publisher "%s"', $publisher->getUser()->getUsername()));
        }

        $logger->info('Finish update billed source reports');
    }
}