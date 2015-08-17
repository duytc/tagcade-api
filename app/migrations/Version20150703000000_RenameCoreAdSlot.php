<?php

namespace Tagcade\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 *
 * Commit at Develop: bed0216c4249aaf7b4ea3e4755cec9e7f208e0ce
 */
class Version20150703000000_RenameCoreAdSlot extends AbstractMigration
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
        $this->addSql('ALTER TABLE core_dynamic_ad_slot DROP FOREIGN KEY FK_B7415E41BF396750');
        $this->addSql('ALTER TABLE core_dynamic_ad_slot DROP FOREIGN KEY FK_B7415E41DC8CAC7B');
        $this->addSql('ALTER TABLE core_expression DROP FOREIGN KEY FK_E47CD2E0E4E5E816');
        $this->addSql('ALTER TABLE core_native_ad_slot DROP FOREIGN KEY FK_5A19262EBF396750');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_slot DROP FOREIGN KEY FK_1E15646794AFF818');

        $this->addSql('CREATE TABLE core_ad_slot (id INT AUTO_INCREMENT NOT NULL, site_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, deleted_at DATE DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_6D6C731F6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE core_ad_slot ADD CONSTRAINT FK_6D6C731F6BD1646 FOREIGN KEY (site_id) REFERENCES core_site (id)');

        // tagcade manually: clone data from core_ad_slot_abstract to core_ad_slot
        $this->addSql('INSERT INTO core_ad_slot SELECT * FROM core_ad_slot_abstract');
        // end-tagcade manually: clone data from core_ad_slot_abstract to core_ad_slot

        $this->addSql('DROP TABLE core_ad_slot_abstract');

        //$this->addSql('ALTER TABLE core_native_ad_slot DROP FOREIGN KEY FK_5A19262EBF396750');
        $this->addSql('ALTER TABLE core_native_ad_slot ADD CONSTRAINT FK_5A19262EBF396750 FOREIGN KEY (id) REFERENCES core_ad_slot (id) ON DELETE CASCADE');

        //$this->addSql('ALTER TABLE core_ad_tag DROP FOREIGN KEY FK_D122BEED94AFF818');
        $this->addSql('ALTER TABLE core_ad_tag ADD CONSTRAINT FK_D122BEED94AFF818 FOREIGN KEY (ad_slot_id) REFERENCES core_ad_slot (id)');

        //$this->addSql('ALTER TABLE core_dynamic_ad_slot DROP FOREIGN KEY FK_B7415E41BF396750');
        //$this->addSql('ALTER TABLE core_dynamic_ad_slot DROP FOREIGN KEY FK_B7415E41DC8CAC7B');
        $this->addSql('ALTER TABLE core_dynamic_ad_slot ADD CONSTRAINT FK_B7415E41BF396750 FOREIGN KEY (id) REFERENCES core_ad_slot (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE core_dynamic_ad_slot ADD CONSTRAINT FK_B7415E41DC8CAC7B FOREIGN KEY (default_ad_slot_id) REFERENCES core_ad_slot (id)');

        //$this->addSql('ALTER TABLE core_expression DROP FOREIGN KEY FK_E47CD2E0E4E5E816');
        $this->addSql('ALTER TABLE core_expression ADD CONSTRAINT FK_E47CD2E0E4E5E816 FOREIGN KEY (expect_ad_slot_id) REFERENCES core_ad_slot (id)');

        //$this->addSql('ALTER TABLE core_display_ad_slot DROP FOREIGN KEY FK_5ED252C1BF396750');
        $this->addSql('ALTER TABLE core_display_ad_slot ADD CONSTRAINT FK_5ED252C1BF396750 FOREIGN KEY (id) REFERENCES core_ad_slot (id) ON DELETE CASCADE');

        //$this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_slot DROP FOREIGN KEY FK_1E15646794AFF818');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_slot ADD CONSTRAINT FK_1E15646794AFF818 FOREIGN KEY (ad_slot_id) REFERENCES core_ad_slot (id)');
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

        $this->addSql('CREATE TABLE core_ad_slot_abstract (id INT AUTO_INCREMENT NOT NULL, site_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci, deleted_at DATE DEFAULT NULL, type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, INDEX IDX_C96551CF6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE core_ad_slot_abstract ADD CONSTRAINT FK_C96551CF6BD1646 FOREIGN KEY (site_id) REFERENCES core_site (id)');

        // tagcade manually: clone data from core_ad_slot to core_ad_slot_abstract
        $this->addSql('INSERT INTO core_ad_slot_abstract SELECT * FROM core_ad_slot');
        // end-tagcade manually: clone data from core_ad_slot to core_ad_slot_abstract

        $this->addSql('DROP TABLE core_ad_slot');

        //$this->addSql('ALTER TABLE core_ad_tag DROP FOREIGN KEY FK_D122BEED94AFF818');
        $this->addSql('ALTER TABLE core_ad_tag ADD CONSTRAINT FK_D122BEED94AFF818 FOREIGN KEY (ad_slot_id) REFERENCES core_ad_slot_abstract (id)');

        //$this->addSql('ALTER TABLE core_display_ad_slot DROP FOREIGN KEY FK_5ED252C1BF396750');
        $this->addSql('ALTER TABLE core_display_ad_slot ADD CONSTRAINT FK_5ED252C1BF396750 FOREIGN KEY (id) REFERENCES core_ad_slot_abstract (id) ON DELETE CASCADE');

        //$this->addSql('ALTER TABLE core_dynamic_ad_slot DROP FOREIGN KEY FK_B7415E41DC8CAC7B');
        //$this->addSql('ALTER TABLE core_dynamic_ad_slot DROP FOREIGN KEY FK_B7415E41BF396750');
        $this->addSql('ALTER TABLE core_dynamic_ad_slot ADD CONSTRAINT FK_B7415E41DC8CAC7B FOREIGN KEY (default_ad_slot_id) REFERENCES core_ad_slot_abstract (id)');
        $this->addSql('ALTER TABLE core_dynamic_ad_slot ADD CONSTRAINT FK_B7415E41BF396750 FOREIGN KEY (id) REFERENCES core_ad_slot_abstract (id) ON DELETE CASCADE');

        //$this->addSql('ALTER TABLE core_expression DROP FOREIGN KEY FK_E47CD2E0E4E5E816');
        $this->addSql('ALTER TABLE core_expression ADD CONSTRAINT FK_E47CD2E0E4E5E816 FOREIGN KEY (expect_ad_slot_id) REFERENCES core_ad_slot_abstract (id)');

        //$this->addSql('ALTER TABLE core_native_ad_slot DROP FOREIGN KEY FK_5A19262EBF396750');
        $this->addSql('ALTER TABLE core_native_ad_slot ADD CONSTRAINT FK_5A19262EBF396750 FOREIGN KEY (id) REFERENCES core_ad_slot_abstract (id) ON DELETE CASCADE');

        //$this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_slot DROP FOREIGN KEY FK_1E15646794AFF818');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_slot ADD CONSTRAINT FK_1E15646794AFF818 FOREIGN KEY (ad_slot_id) REFERENCES core_ad_slot_abstract (id)');
    }
}
