<?php

namespace Tagcade\Bundle\ReportApiBundle\Command;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateBilledAmountCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:report:update')
            ->setDescription('Update billed amount corresponding to total slot opportunities up to current day and pre-configured thresholds')
            ->addArgument(
                'date',
                InputArgument::OPTIONAL,
                'Update to this date. Default is today'
            )

        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dateUtil = $this->getContainer()->get('tagcade.service.date_util');
        $date = $input->getArgument('date');
        $date = (null !== $date) ? $dateUtil->getDateTime($date, true) : new DateTime('today');

        $output->writeln($date);
    }
}