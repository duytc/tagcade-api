<?php

namespace Tagcade\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 *
 * Commit at Develop: 4c2696e9ef28b4c83a6cfda24890fd0c50789f2c
 */
class Version20150702170158_RenameCoreAdSlotAbstract extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE core_ad_tag DROP FOREIGN KEY FK_D122BEED94AFF818');
        $this->addSql('ALTER TABLE core_display_ad_slot DROP FOREIGN KEY FK_5ED252C1BF396750');
        $this->addSql('ALTER TABLE core_dynamic_ad_slot DROP FOREIGN KEY FK_B7415E41DC8CAC7B');
        $this->addSql('ALTER TABLE core_dynamic_ad_slot DROP FOREIGN KEY FK_B7415E41BF396750');
        $this->addSql('ALTER TABLE core_expression DROP FOREIGN KEY FK_E47CD2E0E4E5E816');
        $this->addSql('ALTER TABLE core_native_ad_slot DROP FOREIGN KEY FK_5A19262EBF396750');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_slot DROP FOREIGN KEY FK_1E15646794AFF818');

        $this->addSql('CREATE TABLE core_ad_slot_abstract (id INT AUTO_INCREMENT NOT NULL, site_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, deleted_at DATE DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_C96551CF6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE core_ad_slot_abstract ADD CONSTRAINT FK_C96551CF6BD1646 FOREIGN KEY (site_id) REFERENCES core_site (id)');

        // tagcade manually: clone data from core_ad_slot to core_ad_slot_abstract
        // e.g copy site_id value from each ad slot type to one place */
        $this->addSql('INSERT INTO core_ad_slot_abstract SELECT s.id, ds.site_id, s.name, s.deleted_at, s.type FROM core_ad_slot s, core_display_ad_slot ds WHERE s.id = ds.id');
        $this->addSql('INSERT INTO core_ad_slot_abstract SELECT s.id, ns.site_id, s.name, s.deleted_at, s.type FROM core_ad_slot s, core_native_ad_slot ns WHERE s.id = ns.id');
        $this->addSql('INSERT INTO core_ad_slot_abstract SELECT s.id, ds.site_id, s.name, s.deleted_at, s.type FROM core_ad_slot s, core_dynamic_ad_slot ds WHERE s.id = ds.id');
        // end-tagcade manually: clone data from core_ad_slot to core_ad_slot_abstract

        $this->addSql('DROP TABLE core_ad_slot');

        //$this->addSql('ALTER TABLE core_native_ad_slot DROP FOREIGN KEY FK_5A19262EBF396750'); //already above
        $this->addSql('ALTER TABLE core_native_ad_slot DROP FOREIGN KEY FK_5A19262EF6BD1646'); //added manually required by DROP INDEX...
        $this->addSql('DROP INDEX IDX_5A19262EF6BD1646 ON core_native_ad_slot');
        $this->addSql('ALTER TABLE core_native_ad_slot DROP site_id');
        $this->addSql('ALTER TABLE core_native_ad_slot ADD CONSTRAINT FK_5A19262EBF396750 FOREIGN KEY (id) REFERENCES core_ad_slot_abstract (id) ON DELETE CASCADE');

        //$this->addSql('ALTER TABLE core_ad_tag DROP FOREIGN KEY FK_D122BEED94AFF818');
        $this->addSql('ALTER TABLE core_ad_tag ADD CONSTRAINT FK_D122BEED94AFF818 FOREIGN KEY (ad_slot_id) REFERENCES core_ad_slot_abstract (id)');

        //$this->addSql('ALTER TABLE core_dynamic_ad_slot DROP FOREIGN KEY FK_B7415E41DC8CAC7B'); //already above
        //$this->addSql('ALTER TABLE core_dynamic_ad_slot DROP FOREIGN KEY FK_B7415E41BF396750'); //already above
        $this->addSql('ALTER TABLE core_dynamic_ad_slot DROP FOREIGN KEY FK_B7415E41F6BD1646'); //added manually required by DROP INDEX...
        $this->addSql('DROP INDEX IDX_B7415E41F6BD1646 ON core_dynamic_ad_slot');
        $this->addSql('ALTER TABLE core_dynamic_ad_slot DROP site_id');
        $this->addSql('ALTER TABLE core_dynamic_ad_slot ADD CONSTRAINT FK_B7415E41DC8CAC7B FOREIGN KEY (default_ad_slot_id) REFERENCES core_ad_slot_abstract (id)');
        $this->addSql('ALTER TABLE core_dynamic_ad_slot ADD CONSTRAINT FK_B7415E41BF396750 FOREIGN KEY (id) REFERENCES core_ad_slot_abstract (id) ON DELETE CASCADE');

        //$this->addSql('ALTER TABLE core_expression DROP FOREIGN KEY FK_E47CD2E0E4E5E816');
        $this->addSql('ALTER TABLE core_expression ADD CONSTRAINT FK_E47CD2E0E4E5E816 FOREIGN KEY (expect_ad_slot_id) REFERENCES core_ad_slot_abstract (id)');

        //$this->addSql('ALTER TABLE core_display_ad_slot DROP FOREIGN KEY FK_5ED252C1BF396750'); //already above
        $this->addSql('ALTER TABLE core_display_ad_slot DROP FOREIGN KEY FK_5ED252C1F6BD1646'); //added manually required by DROP INDEX...
        $this->addSql('DROP INDEX IDX_5ED252C1F6BD1646 ON core_display_ad_slot');
        $this->addSql('ALTER TABLE core_display_ad_slot DROP site_id');
        $this->addSql('ALTER TABLE core_display_ad_slot ADD CONSTRAINT FK_5ED252C1BF396750 FOREIGN KEY (id) REFERENCES core_ad_slot_abstract (id) ON DELETE CASCADE');

        //$this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_slot DROP FOREIGN KEY FK_1E15646794AFF818'); //already above
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_slot ADD CONSTRAINT FK_1E15646794AFF818 FOREIGN KEY (ad_slot_id) REFERENCES core_ad_slot_abstract (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE core_native_ad_slot DROP FOREIGN KEY FK_5A19262EBF396750');
        $this->addSql('ALTER TABLE core_ad_tag DROP FOREIGN KEY FK_D122BEED94AFF818');
        $this->addSql('ALTER TABLE core_dynamic_ad_slot DROP FOREIGN KEY FK_B7415E41DC8CAC7B');
        $this->addSql('ALTER TABLE core_dynamic_ad_slot DROP FOREIGN KEY FK_B7415E41BF396750');
        $this->addSql('ALTER TABLE core_expression DROP FOREIGN KEY FK_E47CD2E0E4E5E816');
        $this->addSql('ALTER TABLE core_display_ad_slot DROP FOREIGN KEY FK_5ED252C1BF396750');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_slot DROP FOREIGN KEY FK_1E15646794AFF818');

        $this->addSql('CREATE TABLE core_ad_slot (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci, deleted_at DATE DEFAULT NULL, type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        // tagcade manually: clone data from core_ad_slot_abstract to core_ad_slot
        // needed for re-creating FK from core_display/native/dynamic_ad_slot to core_ad_slot
        $this->addSql('INSERT INTO core_ad_slot SELECT sa.id, sa.name, sa.deleted_at, sa.type FROM core_ad_slot_abstract sa');
        // end-tagcade manually: clone data from core_ad_slot_abstract to core_ad_slot

        //$this->addSql('DROP TABLE core_ad_slot_abstract'); //do at the end

        //$this->addSql('ALTER TABLE core_ad_tag DROP FOREIGN KEY FK_D122BEED94AFF818'); //already above
        $this->addSql('ALTER TABLE core_ad_tag ADD CONSTRAINT FK_D122BEED94AFF818 FOREIGN KEY (ad_slot_id) REFERENCES core_ad_slot (id)');

        //$this->addSql('ALTER TABLE core_display_ad_slot DROP FOREIGN KEY FK_5ED252C1BF396750'); //already above
        $this->addSql('ALTER TABLE core_display_ad_slot ADD site_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE core_display_ad_slot ADD CONSTRAINT FK_5ED252C1F6BD1646 FOREIGN KEY (site_id) REFERENCES core_site (id)');
        $this->addSql('ALTER TABLE core_display_ad_slot ADD CONSTRAINT FK_5ED252C1BF396750 FOREIGN KEY (id) REFERENCES core_ad_slot (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_5ED252C1F6BD1646 ON core_display_ad_slot (site_id)');

        //$this->addSql('ALTER TABLE core_dynamic_ad_slot DROP FOREIGN KEY FK_B7415E41DC8CAC7B'); //already above
        //$this->addSql('ALTER TABLE core_dynamic_ad_slot DROP FOREIGN KEY FK_B7415E41BF396750'); //already above
        $this->addSql('ALTER TABLE core_dynamic_ad_slot ADD site_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE core_dynamic_ad_slot ADD CONSTRAINT FK_B7415E41F6BD1646 FOREIGN KEY (site_id) REFERENCES core_site (id)');
        $this->addSql('ALTER TABLE core_dynamic_ad_slot ADD CONSTRAINT FK_B7415E41DC8CAC7B FOREIGN KEY (default_ad_slot_id) REFERENCES core_ad_slot (id)');
        $this->addSql('ALTER TABLE core_dynamic_ad_slot ADD CONSTRAINT FK_B7415E41BF396750 FOREIGN KEY (id) REFERENCES core_ad_slot (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_B7415E41F6BD1646 ON core_dynamic_ad_slot (site_id)');

        //$this->addSql('ALTER TABLE core_expression DROP FOREIGN KEY FK_E47CD2E0E4E5E816'); //already above
        $this->addSql('ALTER TABLE core_expression ADD CONSTRAINT FK_E47CD2E0E4E5E816 FOREIGN KEY (expect_ad_slot_id) REFERENCES core_ad_slot (id)');

        //$this->addSql('ALTER TABLE core_native_ad_slot DROP FOREIGN KEY FK_5A19262EBF396750'); //already above
        $this->addSql('ALTER TABLE core_native_ad_slot ADD site_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE core_native_ad_slot ADD CONSTRAINT FK_5A19262EF6BD1646 FOREIGN KEY (site_id) REFERENCES core_site (id)');
        $this->addSql('ALTER TABLE core_native_ad_slot ADD CONSTRAINT FK_5A19262EBF396750 FOREIGN KEY (id) REFERENCES core_ad_slot (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_5A19262EF6BD1646 ON core_native_ad_slot (site_id)');

        //$this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_slot DROP FOREIGN KEY FK_1E15646794AFF818'); //already above
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_slot ADD CONSTRAINT FK_1E15646794AFF818 FOREIGN KEY (ad_slot_id) REFERENCES core_ad_slot (id)');

        // tagcade manually: copy site_id value from each ad slot type to one place */
        $this->addSql('UPDATE core_display_ad_slot s, core_ad_slot_abstract sa SET s.site_id = sa.site_id where sa.id = s.id');
        $this->addSql('UPDATE core_native_ad_slot s, core_ad_slot_abstract sa SET s.site_id = sa.site_id where sa.id = s.id');
        $this->addSql('UPDATE core_dynamic_ad_slot s, core_ad_slot_abstract sa SET s.site_id = sa.site_id where sa.id = s.id');
        // end-tagcade manually: copy site_id value from each ad slot type to one place */

        $this->addSql('DROP TABLE core_ad_slot_abstract'); //finally
    }
}
