<?php


namespace Tagcade\Bundle\AppBundle\Command;


use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Tagcade\Model\Core\VideoWaterfallTagInterface;

class MigrateVideoWaterfallTagCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('tc:migration:video-waterfall-tag:run-on')
            ->setDescription('New feature: 1. add run_on field 2. mix server-to-server and vast-only value and then change them to one of these options Server-Side VAST+VAPID, Server-Side VAST Only, Client-Side VAST+VAPID into run_on field. 3. Delete server-to-server and vast-only');
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Add run_on field');
        $addFieldSql = ''
            . 'alter table video_waterfall_tag ADD run_on VARCHAR( 50 ) NOT NULL DEFAULT \'Client-Side VAST+VPAID\';';
        $this->executeSql($addFieldSql);

        //get all datas
        $addFieldSql = ''
            . 'SELECT * FROM `video_waterfall_tag`;';
        $videoWaterfallTags = $this->executeSql($addFieldSql, 1);
        $output->writeln(sprintf('Migrating server-to-server and vast-only value  of %d videoWaterfallTag', count($videoWaterfallTags)));

        // migrate
        $migratedVideoWaterfallTagsCount = 0;

        $this->migrateVideoWaterfallTag($videoWaterfallTags, $migratedVideoWaterfallTagsCount);

        $output->writeln(sprintf('Command runs successfully: migrate of %d videoWaterfallTag updated.', $migratedVideoWaterfallTagsCount));
    }

    /**
     * @param $sql
     * @param null $select
     * @return mixed
     */
    private function executeSql($sql, $select = null)
    {
        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        try {
            $stmt = $em->getConnection()->executeQuery($sql);
            if ($select)
                return $stmt->fetchAll();
            else
                return $stmt;
        } catch (\Exception $e) {
            throw new Exception('An error occur while execute sql command ' . $e);
        }
    }

    /**
     * @param array|VideoWaterfallTagInterface[] $videoWaterfallTags
     * @param int $migratedVideoWaterfallTagsCount
     */
    private function migrateVideoWaterfallTag(array $videoWaterfallTags, &$migratedVideoWaterfallTagsCount)
    {
        foreach ($videoWaterfallTags as $videoWaterfallTag) {

            if (!$videoWaterfallTag['is_server_to_server']) {
                $addFieldSql = ''
                    . 'UPDATE  video_waterfall_tag SET  run_on =  \'Client-Side VAST+VPAID\' WHERE id ='.$videoWaterfallTag['id'].';';
                $this->executeSql($addFieldSql);
            }

            if($videoWaterfallTag['is_server_to_server'] && !$videoWaterfallTag['is_vast_only']) {
                $addFieldSql = ''
                    . 'UPDATE  video_waterfall_tag SET  run_on =  \'Server-Side VAST+VPAID\' WHERE id ='.$videoWaterfallTag['id'].';';
                $this->executeSql($addFieldSql);
            } elseif ($videoWaterfallTag['is_server_to_server'] && $videoWaterfallTag['is_vast_only']) {
                $addFieldSql = ''
                    . 'UPDATE  video_waterfall_tag SET  run_on =  \'Server-Side VAST Only\' WHERE id ='.$videoWaterfallTag['id'].';';
                $this->executeSql($addFieldSql);
            }

            $migratedVideoWaterfallTagsCount++;
        }
    }
}