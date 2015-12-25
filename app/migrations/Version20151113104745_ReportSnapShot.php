<?php

namespace Tagcade\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 *
 * Commit at Master: cd814186466f814249069c231f458092a7aa53b7
 */
class Version20151113104745_ReportSnapShot extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // 1.change name and fields of unique constraint of ron ad slot
        $this->addSql('ALTER TABLE ron_ad_slot DROP INDEX UNIQ_7C2F130370BBCB64, ADD INDEX IDX_7C2F130370BBCB64 (library_ad_slot_id)');
        $this->addSql('ALTER TABLE ron_ad_slot CHANGE deleted_at deleted_at DATETIME DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX unique_library_ad_slot_id_constraint ON ron_ad_slot (library_ad_slot_id, deleted_at)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // 1.rollback name and fields of unique constraint of ron ad slot
        $this->addSql('ALTER TABLE ron_ad_slot DROP INDEX IDX_7C2F130370BBCB64, ADD UNIQUE INDEX UNIQ_7C2F130370BBCB64 (library_ad_slot_id)');
        $this->addSql('DROP INDEX unique_library_ad_slot_id_constraint ON ron_ad_slot');
        $this->addSql('ALTER TABLE ron_ad_slot CHANGE deleted_at deleted_at DATE DEFAULT NULL');
    }
}
