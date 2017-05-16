<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Model\Core\DisplayWhiteListInterface;

class RefreshWhiteListCacheCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('tc:white-list-cache:refresh')
            ->addOption('id', 'id', InputOption::VALUE_REQUIRED, 'id of the entity to be refresh')
            ->setDescription('refresh cache for the given white list, removing duplicates included. Default to refresh all');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getOption('id');
        $whiteListManager = $this->getContainer()->get('tagcade.domain_manager.display.white_list');
        $refreshWhiteListService = $this->getContainer()->get('tagcade_app.service.core.display_whitelist.refresh_display_whitelist_cache');
        if ($id) {
            $whiteList = $whiteListManager->find($id);
            if (!$whiteList instanceof DisplayWhiteLIstInterface) {
                $output->writeln(sprintf('<error>White List %d does not exists!</error>', $id));
                return;
            }

            $refreshWhiteListService->refreshCacheForASingleWhiteList($whiteList);
            $output->writeln('The white list\'s cache has been refreshed!');

            return;
        }

        $whiteLists = $whiteListManager->all();
        $totalWhiteList = count($whiteLists);
        $progress = new ProgressBar($output, $totalWhiteList);
        $progress->start();
        foreach ($whiteLists as $whiteList) {
            $refreshWhiteListService->refreshCacheForASingleWhiteList($whiteList);
            $progress->advance();
        }

        $progress->finish();
        $output->writeln('');
        $output->writeln(sprintf('%d white lists get refreshed', $totalWhiteList));
    }
}