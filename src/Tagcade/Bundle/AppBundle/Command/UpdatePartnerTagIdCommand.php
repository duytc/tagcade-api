<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Model\Core\AdNetworkInterface;
use Tagcade\Model\Core\AdTagInterface;
use Tagcade\Model\User\Role\PublisherInterface;
use Tagcade\Service\Core\AdTag\PartnerTagIdMatcherInterface;

class UpdatePartnerTagIdCommand extends ContainerAwareCommand
{
    const BATCH_SIZE = 30;

    protected function configure()
    {
        $this
            ->setName('tc:partner-tag-id:update')
            ->addOption('id', 'id', InputOption::VALUE_OPTIONAL, 'id of a specific ad tag in the system.')
            ->addOption('adNetwork', 'adNetwork', InputOption::VALUE_OPTIONAL, 'ad network id whose all ad tags\'s partner tag id will be updated')
            ->addOption('publisher', 'publisher', InputOption::VALUE_OPTIONAL, 'publisher id whose all ad tags\'s partner tag id will be updated')
            ->addOption('all', 'all', InputOption::VALUE_NONE, 'specify if all ad tags in the system need to be updated')
            ->setDescription('Refresh partner tag id for ad tags in the system');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $all = filter_var($input->getOption('all'), FILTER_VALIDATE_BOOLEAN);
        $partnerTagIdMatcher = $this->getContainer()->get('tagcade_app.service.core.ad_tag.partner_tag_id_matcher');
        $adTagManager = $this->getContainer()->get('tagcade.domain_manager.ad_tag');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $logger = $this->getContainer()->get('logger');

        $publisherId = filter_var($input->getOption('publisher'), FILTER_VALIDATE_INT);
        $publisher = $this->getContainer()->get('tagcade_user.domain_manager.publisher')->find($publisherId);

        $adNetworkId = filter_var($input->getOption('adNetwork'), FILTER_VALIDATE_INT);
        $adNetwork = $this->getContainer()->get('tagcade.domain_manager.ad_network')->find($adNetworkId);

        $adTagId = filter_var($input->getOption('id'), FILTER_VALIDATE_INT);
        $adTag = $adTagManager->find($adTagId);

        $adTags = [$adTag];

        if ($all === true) {
            $adTags = $adTagManager->all();
        } else if ($publisher instanceof PublisherInterface) {
            $adTags = $adTagManager->getAdTagsForPublisher($publisher);
        } else if ($adNetwork instanceof AdNetworkInterface) {
            $adTags = $adNetwork->getAdTags();
        } else {
            if (!$adTag instanceof AdTagInterface) {
                $output->writeln('<error>either "--all" or "--publisher" or "--adNetwork" or "--id" must be valid</error>');
                return false;
            }

            // final else: use the first initialized value above
        }

        $count = $this->updateAdTags($adTags, $partnerTagIdMatcher, $em, $logger);

        $output->writeln(sprintf('<info>%d ad tags have been updated successfully</info>', $count));
        return true;
    }

    protected function updateAdTags(array $adTags, PartnerTagIdMatcherInterface $matcher, EntityManagerInterface $em, LoggerInterface $logger)
    {
        $count = 0;
        /** @var AdTagInterface $adTag */
        foreach ($adTags as $adTag) {
            $partnerTagId = $matcher->extractPartnerTagId($adTag->getLibraryAdTag());

            if (!is_string($partnerTagId)) {
                continue;
            }

            if (strcmp($partnerTagId, $adTag->getPartnerTagId()) !== 0) {
                $adTag->getLibraryAdTag()->setPartnerTagId($partnerTagId);
                $em->persist($adTag);
                $count++;
            }

            if ($count % self::BATCH_SIZE === 0) {
                $em->flush();
                $logger->info(sprintf('%d ad tags get updated', $count));
            }
        }

        $em->flush();

        return $count;
    }
}