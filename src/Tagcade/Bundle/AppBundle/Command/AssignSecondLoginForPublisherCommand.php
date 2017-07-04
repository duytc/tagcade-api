<?php

namespace Tagcade\Bundle\AppBundle\Command;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

class AssignSecondLoginForPublisherCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:publisher:second-login:assign')
            ->addArgument('publisher', InputArgument::REQUIRED, 'The master Publisher account id')
            ->addArgument('second-login', InputArgument::REQUIRED, 'The second-login Publisher account ids, separated by ","')
            ->setDescription('Assign second login Publisher accounts for a Publisher');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        // get input
        $publisherId = filter_var($input->getArgument('publisher'), FILTER_VALIDATE_INT);
        if (!is_integer($publisherId) || $publisherId < 0) {
            $output->writeln('Expect publisher input is non negative integer');
            return;
        }

        $secondLoginIdsString = $input->getArgument('second-login');
        if (empty($secondLoginIdsString)) {
            $output->writeln('Expect 2nd-login input is array-string of ids');
            return;
        }

        $publisherManager = $container->get('tagcade_user.domain_manager.publisher');
        $masterAccount = $publisherManager->find($publisherId);
        if (!($masterAccount instanceof PublisherInterface) || ($masterAccount instanceof SubPublisherInterface)) {
            $output->writeln(sprintf('Account with id #%d does not exist or not is Publisher account', $publisherId));
            return;
        }

        $secondLoginIds = explode(',', $secondLoginIdsString);
        if (!is_array($secondLoginIds) || count($secondLoginIds) < 1) {
            $output->writeln('Expect 2nd-login input is array-string of ids');
            return;
        }

        /** @var PublisherInterface[] $secondLoginAccounts */
        $secondLoginAccounts = [];
        foreach ($secondLoginIds as $secondLoginId) {
            $secondLoginAccounts[$secondLoginId] = $publisherManager->find($secondLoginId);
        }

        $updatedSecondLoginAccounts = 0;
        foreach ($secondLoginAccounts as $id => $secondLoginAccount) {
            if (!$secondLoginAccount instanceof PublisherInterface) {
                $output->writeln(sprintf('[warning] The second-login Publisher account #%d does not exist => skip', $id));
                continue;
            }

            $secondLoginAccount->setMasterAccount($masterAccount);
            $publisherManager->save($secondLoginAccount);

            $updatedSecondLoginAccounts++;
        }

        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine.orm.entity_manager');
        $em->flush();

        $output->writeln(sprintf('Set %d second-login Publisher account for the master Publisher account', $updatedSecondLoginAccounts));
    }
}