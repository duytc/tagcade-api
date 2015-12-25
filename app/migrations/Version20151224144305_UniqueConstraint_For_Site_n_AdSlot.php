<?php

namespace Tagcade\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 *
 * Commit at Develop: ff94e3f049545bca51a090a16be24e4bb6b8f248
 */
class Version20151224144305_UniqueConstraint_For_Site_n_AdSlot extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // 1.change unique constraint for site from (domain, publisher_id, deleted_at) to only (domain_canonical)
        $this->addSql('DROP INDEX site_compound_primary_key ON core_site');
        $this->addSql('ALTER TABLE core_site ADD delete_token VARCHAR(255) NOT NULL, CHANGE domain domain VARCHAR(240) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX site_publisher_key ON core_site (publisher_id, domain, delete_token)');

        // 2.add 'delete_token' and create unique constraint for ad slot (site, library_ad_slot, delete_token)
        $this->addSql('ALTER TABLE core_ad_slot ADD delete_token VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX ad_slot_by_site_and_library_key ON core_ad_slot (site_id, library_ad_slot_id, delete_token)');
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
        $this->addSql('DROP INDEX site_publisher_key ON core_site');
        $this->addSql('ALTER TABLE core_site DROP delete_token, CHANGE domain domain VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        $this->addSql('CREATE UNIQUE INDEX site_compound_primary_key ON core_site (domain, publisher_id, deleted_at)');
    }
}
