<?php

namespace Tagcade\Bundle\AppBundle\Command;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Bundle\UserSystem\PublisherBundle\Entity\User;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Model\User\Role\SubPublisherInterface;

class AssignSecondLoginForPublisherCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:publisher:second-login:assign')
            ->addArgument('publisher', InputArgument::REQUIRED, 'The master Publisher account id')
            ->addOption('second-login', 'l', InputOption::VALUE_OPTIONAL, 'The second-login Publisher account ids, separated by ","')
            ->addOption('create', 'c', InputOption::VALUE_NONE, 'Create new Publisher and assign as second login for master Publisher account')
            ->addOption('username', 'u', InputOption::VALUE_OPTIONAL, 'Username of new Publisher need be created')
            ->addOption('password', 'p', InputOption::VALUE_OPTIONAL, 'Username of new Publisher need be created')
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

        $publisherManager = $container->get('tagcade_user.domain_manager.publisher');
        $masterAccount = $publisherManager->find($publisherId);
        if (!($masterAccount instanceof PublisherInterface) || ($masterAccount instanceof SubPublisherInterface)) {
            $output->writeln(sprintf('Account with id #%d does not exist or not is Publisher account', $publisherId));
            return;
        }

        /* prefer create new than assign ids */
        /* if allow create new Publisher */
        $isCreateNew = (bool)$input->getOption('create');
        if ($isCreateNew) {
            $username = $input->getOption('username');
            $password = $input->getOption('password');

            if (empty($username) || empty($password)) {
                $output->writeln('Expect username and password are not empty');
                return;
            }

            // try to find if publisher is existing before
            $exisingPublisher = $publisherManager->findUserByUsernameOrEmail($username);
            if ($exisingPublisher instanceof PublisherInterface) {
                $output->writeln(sprintf('Publisher with username %s has already existed (id=%d). Please use command to assign only: tc:publisher:second-login:assign %d -l %d',
                    $username, $exisingPublisher->getId(), $publisherId, $exisingPublisher->getId()));
                return;
            }

            $newPublisher = new User();
            $newPublisher->setUsername($username);
            $newPublisher->setPlainPassword($password);
            $newPublisher->setMasterAccount($masterAccount);
            $newPublisher->setEnabled(true);

            $publisherManager->save($newPublisher);

            /** @var EntityManagerInterface $em */
            $em = $container->get('doctrine.orm.entity_manager');
            $em->flush();

            $output->writeln(sprintf('Set %d second-login Publisher account for the master Publisher account', 1));
            return;
        }

        /* if assign ids to master Publisher account */
        $secondLoginIdsString = $input->getOption('second-login');
        if (empty($secondLoginIdsString)) {
            $output->writeln('Expect 2nd-login input is array-string of ids');
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