<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Behaviors\UserUtilTrait;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Worker\Manager;

class MigratePublisherEmailSendAlertForURCommand extends ContainerAwareCommand
{
    use UserUtilTrait;

    protected function configure()
    {
        $this
            ->setName('tc:migration:publisher:email-send-alert-ur:update')
            ->setDescription('Migrate email send UR alert for Publisher from string to array (multiple emails)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting command...');

        $container = $this->getContainer();
        $publisherManager = $container->get('tagcade_user.domain_manager.publisher');

        /** @var PublisherInterface[]|Collection $allPublishers */
        $allPublishers = $publisherManager->all();
        if (!is_array($allPublishers) || count($allPublishers) < 1) {
            $output->writeln('Done! Not found Publisher to be migrated');
            return;
        }

        $output->writeln(sprintf('Updating email send UR alert for %d Publishers...', count($allPublishers)));

        /* get all Publishers to be updated */
        /** @var PublisherInterface[] $updatingPublishers */
        $updatingPublishers = [];
        foreach ($allPublishers as $publisher) {
            // skip $publisher that has emailSendAlert already is array
            if (is_array($publisher->getEmailSendAlert())) {
                continue;
            }

            // COULD NOT use getter/setter to set email send alert for publisher
            // because of the type is change from string to array, so the old value is null instead of string
            // e.g: old (string) = "pub1@tagcade.dev" => new (array) = null instead of previous string
            // So that we use sql to update, then do synchronizeUser manually instead of listener

            $updatingPublishers[] = $publisher;
        }

        /* save all pending Publishers and sync to UR API */
        /** @var EntityManagerInterface $em */
        $em = $container->get('doctrine.orm.entity_manager');
        /** @var Manager $workerManager */
        $workerManager = $container->get('tagcade.worker.manager');
        foreach ($updatingPublishers as $publisher) {
            $sql = "UPDATE core_user_publisher SET email_send_alert = CONCAT('[', '{\"email\":\"', email_send_alert, '\"}', ']')"
                . " WHERE id = " . $publisher->getId()
                . " AND email_send_alert IS NOT NULL";
            $em->getConnection()->executeQuery($sql);
            $em->refresh($publisher);

            /* sync to UR API */
            $entityArray = $this->generatePublisherData($publisher);
            $workerManager->synchronizeUser($entityArray);
        }

        $output->writeln(sprintf('Done! %d Publishers are migrated', count($updatingPublishers)));
    }
}