<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Model\Core\SiteInterface;

class ExportSiteCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:site:export')
            ->addArgument('site', InputArgument::REQUIRED, 'Site id of a specific site in the system.')
            ->addOption('directory', 'd', InputOption::VALUE_OPTIONAL, 'directory where the json will be created')
            ->setDescription('Export site to json format');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $siteManager = $container->get('tagcade.domain_manager.site');

        $site = $input->getArgument('site');
        $site = $siteManager->find($site);

        if (!$site instanceof SiteInterface) {
            throw new \Exception(sprintf('Not found that site %s', $site));
        }

        $dir = $input->getOption('directory');
        if ($dir == null) {
            $dir = $container->getParameter('kernel.root_dir');
            $dir = rtrim($dir, '/app');
            $dir = sprintf('%s/data', $dir);
        }

        if (!is_dir($dir)) {
            throw new \Exception(sprintf('Expect a valid directory. The input was %s', $dir));
        }

        $serializer = $container->get('serializer');

        $output->writeln(sprintf('Getting ad tags for site %s', $site->getId()));

        $adTags = $container->get('tagcade.domain_manager.ad_tag')->getAdTagsForSite($site);

        $fileFullPath = sprintf("$dir/%s.json", $site->getDomain());
        $output->writeln(sprintf('Open file to write %s', $fileFullPath));

        $myFile = fopen($fileFullPath, "w") or die("Unable to open file!");
        fwrite($myFile, $serializer->serialize($adTags, 'json'));
        fclose($myFile);

        $output->writeln('complete exporting site to json');
    }
}