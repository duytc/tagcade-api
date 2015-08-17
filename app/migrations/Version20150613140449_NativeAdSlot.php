<?php

namespace Tagcade\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 *
 * Commit at Master: 0c7f2e17b5e39f30592d7c7a43a1104c27087fbb
 */
class Version20150613140449_NativeAdSlot extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //// create table core_native_ad_slot
        $this->addSql('CREATE TABLE core_native_ad_slot (id INT NOT NULL, site_id INT DEFAULT NULL, INDEX IDX_5A19262EF6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        // tagcade manually: delete all data from core_ad_slot if type = 'native' because 'site_id' already lost
        $this->addSql('UPDATE core_ad_slot s SET s.deleted_at = "2015-06-13" WHERE s.type = "native"');
        // end-tagcade manually: clone datat from core_ad_slot to native ad slot

        //// add constraints for table core_native_ad_slot to core_site
        $this->addSql('ALTER TABLE core_native_ad_slot ADD CONSTRAINT FK_5A19262EF6BD1646 FOREIGN KEY (site_id) REFERENCES core_site (id)');

        //// add constraints for table core_native_ad_slot to core_ad_slot on id
        $this->addSql('ALTER TABLE core_native_ad_slot ADD CONSTRAINT FK_5A19262EBF396750 FOREIGN KEY (id) REFERENCES core_ad_slot (id) ON DELETE CASCADE');

        //// drop (if existed) and re-create constraints for table core_native_ad_slot to core_site and core_ad_slot on ad_slot_id
        $this->addSql('ALTER TABLE core_ad_tag DROP FOREIGN KEY FK_D122BEED94AFF818');
        $this->addSql('ALTER TABLE core_ad_tag ADD CONSTRAINT FK_D122BEED94AFF818 FOREIGN KEY (ad_slot_id) REFERENCES core_ad_slot (id)');

        //// add field 'native' for core_dynamic_ad_slot
        $this->addSql('ALTER TABLE core_dynamic_ad_slot ADD native TINYINT(1) DEFAULT \'0\' NOT NULL');

        //// drop (if existed) and re-create constraints for table core_dynamic_ad_slot to core_site and core_ad_slot
        //// TODO: still error here if up from master0 in which dynamic adslot has ref to native before:
        //// 'SQLSTATE[42000]: Syntax error or access violation: 1091 Can't DROP 'FK_B7415E41DC8CAC7B'; check that column/key exists'
        $this->addSql('ALTER TABLE core_dynamic_ad_slot DROP FOREIGN KEY FK_B7415E41DC8CAC7B');

        $this->addSql('ALTER TABLE core_dynamic_ad_slot ADD CONSTRAINT FK_B7415E41DC8CAC7B FOREIGN KEY (default_ad_slot_id) REFERENCES core_ad_slot (id)');

        //// drop (if existed) and re-create constraints for table core_dynamic_ad_slot to core_expression
        $this->addSql('ALTER TABLE core_expression DROP FOREIGN KEY FK_E47CD2E0E4E5E816');
        $this->addSql('ALTER TABLE core_expression ADD CONSTRAINT FK_E47CD2E0E4E5E816 FOREIGN KEY (expect_ad_slot_id) REFERENCES core_ad_slot (id)');

        //// change fields for report_performance_display_hierarchy_platform_ad_tag/ad_slot
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_tag CHANGE position position INT DEFAULT NULL, CHANGE passbacks passbacks INT DEFAULT NULL, CHANGE unverified_impressions unverified_impressions INT DEFAULT NULL, CHANGE blank_impressions blank_impressions INT DEFAULT NULL, CHANGE void_impressions void_impressions INT DEFAULT NULL, CHANGE clicks clicks INT DEFAULT NULL');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_slot CHANGE impressions impressions INT DEFAULT NULL, CHANGE passbacks passbacks INT DEFAULT NULL, CHANGE fill_rate fill_rate NUMERIC(10, 4) DEFAULT NULL');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_ad_network_ad_tag CHANGE passbacks passbacks INT DEFAULT NULL, CHANGE unverified_impressions unverified_impressions INT DEFAULT NULL, CHANGE blank_impressions blank_impressions INT DEFAULT NULL, CHANGE void_impressions void_impressions INT DEFAULT NULL, CHANGE clicks clicks INT DEFAULT NULL');

        //// drop (if existed) and re-create constraints for table core_dynamic_ad_slot to core_expression
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_slot DROP FOREIGN KEY FK_1E15646794AFF818');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_slot ADD CONSTRAINT FK_1E15646794AFF818 FOREIGN KEY (ad_slot_id) REFERENCES core_ad_slot (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        //// drop table core_native_ad_slot
        $this->addSql('DROP TABLE core_native_ad_slot');

        //// drop (if existed) and re-create constraints for table core_ad_tag to core_static_ad_slot on id
        $this->addSql('ALTER TABLE core_ad_tag DROP FOREIGN KEY FK_D122BEED94AFF818');
        $this->addSql('ALTER TABLE core_ad_tag ADD CONSTRAINT FK_D122BEED94AFF818 FOREIGN KEY (ad_slot_id) REFERENCES core_static_ad_slot (id)');

        //// drop (if existed) and re-create constraints for table core_ad_tag to core_static_ad_slot on id
        $this->addSql('ALTER TABLE core_dynamic_ad_slot DROP FOREIGN KEY FK_B7415E41DC8CAC7B');

        //// TODO: still error here if down from this to master0 in which dynamic adslot has ref to native before:
        //// 'SQLSTATE[23000]: Integrity constraint violation: 1452 Cannot add or update a child row: a foreign key constraint fails (`tagcade_api`.`#sql-a3b_b4c`, CONSTRAINT `FK_B7415E41DC8CAC7B` FOREIGN KEY (`default_ad_slot_id`) REFERENCES `core_static_ad_slot` (`id`))'
        $this->addSql('ALTER TABLE core_dynamic_ad_slot ADD CONSTRAINT FK_B7415E41DC8CAC7B FOREIGN KEY (default_ad_slot_id) REFERENCES core_static_ad_slot (id)');

        //// remove field 'native' for core_dynamic_ad_slot
        $this->addSql('ALTER TABLE core_dynamic_ad_slot DROP native');

        //// drop (if existed) and re-create constraints for table core_ad_tag to core_static_ad_slot on id
        $this->addSql('ALTER TABLE core_expression DROP FOREIGN KEY FK_E47CD2E0E4E5E816');
        $this->addSql('ALTER TABLE core_expression ADD CONSTRAINT FK_E47CD2E0E4E5E816 FOREIGN KEY (expect_ad_slot_id) REFERENCES core_static_ad_slot (id)');

        //// revert fields  for core_dynamic_ad_slot
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_slot DROP FOREIGN KEY FK_1E15646794AFF818');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_slot ADD CONSTRAINT FK_1E15646794AFF818 FOREIGN KEY (ad_slot_id) REFERENCES core_static_ad_slot (id)');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_ad_network_ad_tag CHANGE passbacks passbacks INT NOT NULL, CHANGE unverified_impressions unverified_impressions INT NOT NULL, CHANGE blank_impressions blank_impressions INT NOT NULL, CHANGE void_impressions void_impressions INT NOT NULL, CHANGE clicks clicks INT NOT NULL');

        //// drop (if existed) and re-create constraints for table core_ad_tag to core_static_ad_slot on id
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_slot CHANGE impressions impressions INT NOT NULL, CHANGE passbacks passbacks INT NOT NULL, CHANGE fill_rate fill_rate NUMERIC(10, 4) NOT NULL');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_tag CHANGE position position INT NOT NULL, CHANGE passbacks passbacks INT NOT NULL, CHANGE unverified_impressions unverified_impressions INT NOT NULL, CHANGE blank_impressions blank_impressions INT NOT NULL, CHANGE void_impressions void_impressions INT NOT NULL, CHANGE clicks clicks INT NOT NULL');
    }
}
