<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateRemoveNetworkPartnerTableCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:migration:network-partner:remove')
            ->setDescription('Move all info from Network Partners to Ad Networks (name) if have relationship, then delete table Network Partner');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Starting command...');

        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $dropSql = ''
            . 'set foreign_key_checks = 0;'
            . 'drop table core_partner;';
        $stmt = $em->getConnection()->prepare($dropSql);
        $stmt->execute();

        $output->writeln('Command finished!');
    }
}
