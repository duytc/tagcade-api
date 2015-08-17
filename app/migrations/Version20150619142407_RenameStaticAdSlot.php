<?php

namespace Tagcade\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 *
 * Commit at Master: 31aafc5cca070659d1f2c93b3b08cc9f39b3e65b
 */
class Version20150619142407_RenameStaticAdSlot extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // create table core_display_ad_slot like core_static_ad_slot
        $this->addSql('CREATE TABLE core_display_ad_slot (id INT NOT NULL, site_id INT DEFAULT NULL, width INT NOT NULL, height INT NOT NULL, INDEX IDX_5ED252C1F6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE core_display_ad_slot ADD CONSTRAINT FK_5ED252C1F6BD1646 FOREIGN KEY (site_id) REFERENCES core_site (id)');
        $this->addSql('ALTER TABLE core_display_ad_slot ADD CONSTRAINT FK_5ED252C1BF396750 FOREIGN KEY (id) REFERENCES core_ad_slot (id) ON DELETE CASCADE');

        // tagcade manually: clone data from core_static_ad_slot to core_display_ad_slot
        $this->addSql('INSERT INTO core_display_ad_slot SELECT * FROM core_static_ad_slot');
        $this->addSql('UPDATE core_ad_slot s SET s.type = "display" WHERE s.type = "static"');
        // end-tagcade manually: clone data from core_static_ad_slot to core_display_ad_slot

        // drop table core_static_ad_slot
        $this->addSql('DROP TABLE core_static_ad_slot');

        // change field 'starting_position' for table core_expression
        $this->addSql('ALTER TABLE core_expression CHANGE starting_position starting_position INT DEFAULT 1');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // create table core_static_ad_slot like core_display_ad_slot
        $this->addSql('CREATE TABLE core_static_ad_slot (id INT NOT NULL, site_id INT DEFAULT NULL, width INT NOT NULL, height INT NOT NULL, INDEX IDX_761FCF1FF6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE core_static_ad_slot ADD CONSTRAINT FK_761FCF1FBF396750 FOREIGN KEY (id) REFERENCES core_ad_slot (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE core_static_ad_slot ADD CONSTRAINT FK_761FCF1FF6BD1646 FOREIGN KEY (site_id) REFERENCES core_site (id)');

        // tagcade manually: clone data from core_display_ad_slot to core_static_ad_slot
        $this->addSql('INSERT INTO core_static_ad_slot SELECT ds.id, ds.site_id, ds.width, ds.height FROM core_display_ad_slot ds'); //not using *, if not will get error "SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row: a foreign key constraint fails..."
        $this->addSql('UPDATE core_ad_slot s SET s.type = "static" WHERE s.type = "display"');
        // end-tagcade manually: clone data from core_display_ad_slot to core_static_ad_slot

        // drop table core_display_ad_slot
        $this->addSql('DROP TABLE core_display_ad_slot');

        // change field 'starting_position' for table core_expression
        $this->addSql('ALTER TABLE core_expression CHANGE starting_position starting_position INT DEFAULT 1 NOT NULL');
    }
}
