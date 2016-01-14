<?php

namespace Tagcade\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 *
 * Commit at Develop: ff94e3f049545bca51a090a16be24e4bb6b8f248
 */
class Version20151224144305_UniqueConstraint_For_Site_n_AdSlot extends AbstractMigration implements ContainerAwareInterface
{
    /** @var ContainerInterface */
    protected $container;

    const GENERATE_SITE_TOKEN_COMMAND = 'tc:site:generate-token';

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // 1.change unique constraint for site from (domain, publisher_id, deleted_at) to only (domain_canonical)
        $this->addSql('DROP INDEX site_compound_primary_key ON core_site');
        $this->addSql('ALTER TABLE core_site ADD site_token VARCHAR(255) NOT NULL, CHANGE domain domain VARCHAR(240) NOT NULL');

        //// we need update site_token value and create unique index site_token_key
        //// but this must be executed in postUp()

        // 2.add 'delete_token' and create unique constraint for ad slot (site, library_ad_slot, delete_token)
        $this->addSql('ALTER TABLE core_ad_slot ADD delete_token VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX ad_slot_by_site_and_library_key ON core_ad_slot (site_id, library_ad_slot_id, delete_token)');

        // 3.update 'delete_token' bases on 'deleted_at' for site and ad slot
        $this->addSql('UPDATE core_ad_slot set delete_token = 0 WHERE deleted_at IS NULL ');
        $this->addSql('UPDATE core_ad_slot set delete_token = SUBSTRING(md5(deleted_at), 1, 23) WHERE deleted_at IS NOT NULL ');
    }

    /**
     * @param Schema $schema
     */
    public function postUp(Schema $schema)
    {
        $this->write('==postUp() executing...');

        //// update site_token value before create unique index site_token_key
        $this->updateSiteToken();

        //// then create unique site_token_key on site_token
        //// replace this auto-gen command "$this->addSql('CREATE UNIQUE INDEX site_token_key ON core_site (site_token)');" by:
        $this->write('==creating unique site_token_key on site_token...');
        $this->connection->executeQuery('CREATE UNIQUE INDEX site_token_key ON core_site (site_token)');
        $this->write('==creating unique site_token_key on site_token... done!');

        $this->write('==postUp() executing... done!');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // 1.drop 'delete_token' and unique constraint for ad slot
        $this->addSql('DROP INDEX ad_slot_by_site_and_library_key ON core_ad_slot');
        $this->addSql('ALTER TABLE core_ad_slot DROP delete_token');

        // 2.rollback unique constraint for site
        $this->addSql('DROP INDEX site_token_key ON core_site');
        $this->addSql('ALTER TABLE core_site DROP site_token, CHANGE domain domain VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('CREATE UNIQUE INDEX site_compound_primary_key ON core_site (domain, publisher_id, deleted_at)');
    }

    /**
     * update site_token for site
     */
    private function updateSiteToken()
    {

        try {
            $this->write('==updating Site Token...');

            $kernel = $this->container->get('kernel');
            $application = new Application($kernel);
            $application->setAutoExit(false);

            $input = new ArrayInput(
                array(
                    'command' => self::GENERATE_SITE_TOKEN_COMMAND
                )
            );

            $output = new BufferedOutput();

            $application->run($input, $output);

            $this->write('==updating Site Token...');
        } catch (\Exception $e) {
            $this->write(sprintf('==updating Site Token... Error: %s\n', $e->getMessage()));
        }
    }
}
