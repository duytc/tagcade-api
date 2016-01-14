<?php

namespace Tagcade\Bundle\AppBundle\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Tagcade\Behaviors\CreateSiteTokenTrait;
use Tagcade\Bundle\UserBundle\DomainManager\PublisherManagerInterface;
use Tagcade\DomainManager\SiteManagerInterface;
use Tagcade\Exception\RuntimeException;
use Tagcade\Model\Core\SiteInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Repository\Core\SiteRepositoryInterface;

/**
 * Provides a command-line interface for generating site token for sites
 */
class GenerateSiteTokenCommand extends ContainerAwareCommand
{
    use CreateSiteTokenTrait;
    /**
     * Configure the CLI task
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('tc:site:generate-token')
            ->setDescription('Generate default token value for site. If option id not found then all sites will be affected')
            ->addOption(
                'id',
                'i',
                InputOption::VALUE_OPTIONAL,
                'The site id to be updated'
            )
        ;
    }

    /**
     * Execute the CLI task
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * @var SiteManagerInterface $siteManager
         */
        $siteManager = $this->getContainer()->get('tagcade.domain_manager.site');
        /**
         * @var SiteRepositoryInterface $siteRepository
         */
        $siteRepository = $this->getContainer()->get('tagcade.repository.site');
        /**
         * @var EntityManagerInterface $em
         */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $siteId = $input->getOption('id');

        $sites = [];

        if ($siteId == null) {
            $sites = $siteManager->all();
        }
        else {
            $site = $siteManager->find($siteId);

            if (!$site instanceof SiteInterface) {
                throw new RuntimeException('That site is not found');
            }

            $sites[] = $site;
        }

        foreach ($sites as $st) {
            /**
             * @var SiteInterface $st
             */
            if (!$st instanceof SiteInterface) {
                continue;
            }

            // Set site unique for the case of auto create.
            for ($i = 0; $i < 10; $i++) {
                $hash = $this->createSiteHash($st->getPublisherId(), $st->getDomain());
                $existingSites = $siteRepository->findBy(array('domain'=>$st->getDomain(), 'publisher' => $st->getPublisher()));
                $siteToken = $st->isAutoCreate() ?  $hash : (count($existingSites) < 2 ? $hash : uniqid(null, true));

                try {
                    $st->setSiteToken($siteToken);
                    $em->flush();
                    $output->writeln(sprintf('Setting token for site %d with value %s with length %d', $st->getId(), $siteToken, strlen($siteToken)));
                    break;
                }
                catch(\Exception $ex) {
                    $output->writeln(sprintf('Error setting token for site %d with value %s length %d. We are retrying for maximum 10 times.', $st->getId(), $siteToken, strlen($siteToken)));
                    continue;
                }
            }
        }

        $em->flush();

        $output->writeln('Finish setting token.');
    }
}
