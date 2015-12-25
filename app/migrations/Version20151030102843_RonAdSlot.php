<?php

namespace Tagcade\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 *
 * Commit at Master: 6835558b8fc58b39ff7f0e4431aa685ae4ab682c
 */
class Version20151030102843_RonAdSlot extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // 1.create tables ron_ad_slot_segment, segment, ron_ad_slot, report_performance_display_hierarchy_segment_ron_ad_tag, report_performance_display_hierarchy_segment_ron_ad_slot, report_performance_display_hierarchy_segment_segment
        $this->addSql('CREATE TABLE ron_ad_slot_segment (id INT AUTO_INCREMENT NOT NULL, segment_id INT DEFAULT NULL, ron_ad_slot_id INT DEFAULT NULL, created_at DATETIME NOT NULL, deleted_at DATE DEFAULT NULL, INDEX IDX_7EA82F85DB296AAD (segment_id), INDEX IDX_7EA82F85AE3907DE (ron_ad_slot_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE segment (id INT AUTO_INCREMENT NOT NULL, publisher_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, deleted_at DATE DEFAULT NULL, INDEX IDX_1881F56540C86FCE (publisher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ron_ad_slot (id INT AUTO_INCREMENT NOT NULL, library_ad_slot_id INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATE DEFAULT NULL, UNIQUE INDEX UNIQ_7C2F130370BBCB64 (library_ad_slot_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_performance_display_hierarchy_segment_ron_ad_tag (id INT AUTO_INCREMENT NOT NULL, ron_ad_tag_id INT DEFAULT NULL, super_report_id INT DEFAULT NULL, segment_id INT DEFAULT NULL, date DATE NOT NULL, name VARCHAR(100) DEFAULT NULL, position INT DEFAULT NULL, total_opportunities INT NOT NULL, impressions INT NOT NULL, passbacks INT DEFAULT NULL, fill_rate NUMERIC(10, 4) NOT NULL, relative_fill_rate NUMERIC(10, 4) NOT NULL, est_revenue NUMERIC(10, 4) DEFAULT NULL, est_cpm NUMERIC(10, 4) DEFAULT NULL, first_opportunities INT NOT NULL, verified_impressions INT NOT NULL, unverified_impressions INT DEFAULT NULL, blank_impressions INT DEFAULT NULL, void_impressions INT DEFAULT NULL, clicks INT DEFAULT NULL, INDEX IDX_3713B6044937C85 (ron_ad_tag_id), INDEX IDX_3713B604E7B18F1F (super_report_id), INDEX IDX_3713B604DB296AAD (segment_id), UNIQUE INDEX unique_platform_adtag_report_idx (date, ron_ad_tag_id, super_report_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_performance_display_hierarchy_segment_ron_ad_slot (id INT AUTO_INCREMENT NOT NULL, ron_ad_slot_id INT DEFAULT NULL, segment_id INT DEFAULT NULL, super_report_id INT DEFAULT NULL, date DATE NOT NULL, name VARCHAR(100) DEFAULT NULL, total_opportunities INT NOT NULL, slot_opportunities INT NOT NULL, impressions INT DEFAULT NULL, passbacks INT DEFAULT NULL, fill_rate NUMERIC(10, 4) DEFAULT NULL, est_revenue NUMERIC(10, 4) DEFAULT NULL, est_cpm NUMERIC(10, 4) DEFAULT NULL, billed_rate NUMERIC(10, 4) DEFAULT NULL, custom_rate NUMERIC(10, 4) DEFAULT NULL, billed_amount NUMERIC(10, 4) DEFAULT NULL, INDEX IDX_DFE6ACE5AE3907DE (ron_ad_slot_id), INDEX IDX_DFE6ACE5DB296AAD (segment_id), INDEX IDX_DFE6ACE5E7B18F1F (super_report_id), UNIQUE INDEX unique_platform_adslot_report_idx (date, ron_ad_slot_id, segment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_performance_display_hierarchy_segment_segment (id INT AUTO_INCREMENT NOT NULL, segment_id INT DEFAULT NULL, date DATE NOT NULL, name VARCHAR(100) DEFAULT NULL, total_opportunities INT NOT NULL, slot_opportunities INT NOT NULL, impressions INT NOT NULL, passbacks INT NOT NULL, fill_rate NUMERIC(10, 4) NOT NULL, est_revenue NUMERIC(10, 4) DEFAULT NULL, est_cpm NUMERIC(10, 4) DEFAULT NULL, billed_rate NUMERIC(10, 4) DEFAULT NULL, billed_amount NUMERIC(10, 4) DEFAULT NULL, INDEX IDX_79A7ED54DB296AAD (segment_id), UNIQUE INDEX unique_platform_site_report_idx (date, segment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        // 2.create all constraints from *related-ron* to themselves and to core_user, library_ad_slot, library_ad_slot_ad_tag
        //// ron_ad_slot_segment to segment
        $this->addSql('ALTER TABLE ron_ad_slot_segment ADD CONSTRAINT FK_7EA82F85DB296AAD FOREIGN KEY (segment_id) REFERENCES segment (id)');
        //// ron_ad_slot_segment to ron_ad_slot
        $this->addSql('ALTER TABLE ron_ad_slot_segment ADD CONSTRAINT FK_7EA82F85AE3907DE FOREIGN KEY (ron_ad_slot_id) REFERENCES ron_ad_slot (id)');
        //// segment to core_user
        $this->addSql('ALTER TABLE segment ADD CONSTRAINT FK_1881F56540C86FCE FOREIGN KEY (publisher_id) REFERENCES core_user (id)');
        //// ron_ad_slot to library_ad_slot
        $this->addSql('ALTER TABLE ron_ad_slot ADD CONSTRAINT FK_7C2F130370BBCB64 FOREIGN KEY (library_ad_slot_id) REFERENCES library_ad_slot (id)');
        //// report_performance_display_hierarchy_segment_ron_ad_tag to library_ad_slot_ad_tag, report_performance_display_hierarchy_segment_ron_ad_slot, segment
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_segment_ron_ad_tag ADD CONSTRAINT FK_3713B6044937C85 FOREIGN KEY (ron_ad_tag_id) REFERENCES library_ad_slot_ad_tag (id)');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_segment_ron_ad_tag ADD CONSTRAINT FK_3713B604E7B18F1F FOREIGN KEY (super_report_id) REFERENCES report_performance_display_hierarchy_segment_ron_ad_slot (id)');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_segment_ron_ad_tag ADD CONSTRAINT FK_3713B604DB296AAD FOREIGN KEY (segment_id) REFERENCES segment (id)');
        //// report_performance_display_hierarchy_segment_ron_ad_slot to ron_ad_slot, segment, report_performance_display_hierarchy_segment_segment
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_segment_ron_ad_slot ADD CONSTRAINT FK_DFE6ACE5AE3907DE FOREIGN KEY (ron_ad_slot_id) REFERENCES ron_ad_slot (id)');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_segment_ron_ad_slot ADD CONSTRAINT FK_DFE6ACE5DB296AAD FOREIGN KEY (segment_id) REFERENCES segment (id)');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_segment_ron_ad_slot ADD CONSTRAINT FK_DFE6ACE5E7B18F1F FOREIGN KEY (super_report_id) REFERENCES report_performance_display_hierarchy_segment_segment (id)');
        //// report_performance_display_hierarchy_segment_segment to segment
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_segment_segment ADD CONSTRAINT FK_79A7ED54DB296AAD FOREIGN KEY (segment_id) REFERENCES segment (id)');

        // 3.rename unique constraint (slot-tag-ref_id) on library_ad_slot_ad_tag
        $this->addSql('ALTER TABLE library_ad_slot_ad_tag DROP FOREIGN KEY FK_DC3B33AE70BBCB64');
        $this->addSql('ALTER TABLE library_ad_slot_ad_tag DROP FOREIGN KEY FK_DC3B33AE3DC10368');
        $this->addSql('DROP INDEX unique_report_idx ON library_ad_slot_ad_tag');
        $this->addSql('CREATE UNIQUE INDEX slot_tag_compound_primary_key ON library_ad_slot_ad_tag (library_ad_tag_id, library_ad_slot_id, ref_id)');
        $this->addSql('ALTER TABLE library_ad_slot_ad_tag ADD CONSTRAINT FK_DC3B33AE70BBCB64 FOREIGN KEY (library_ad_slot_id) REFERENCES library_ad_slot (id)');
        $this->addSql('ALTER TABLE library_ad_slot_ad_tag ADD CONSTRAINT FK_DC3B33AE3DC10368 FOREIGN KEY (library_ad_tag_id) REFERENCES library_ad_tag (id)');

        // 4.add expression_in_js for library_expression for updating cache
        $this->addSql('ALTER TABLE library_expression ADD expression_in_js LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\'');

        // 5.add 'auto_create' when creating site for ron ad slot
        $this->addSql('ALTER TABLE core_site ADD auto_create TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT NULL');

        // 6.create unique constraint on site (domain must be unique)
        $this->addSql('CREATE UNIQUE INDEX site_compound_primary_key ON core_site (domain, publisher_id, deleted_at)');

        // 7.add 'auto_create' when creating ad slot for ron ad slot
        $this->addSql('ALTER TABLE core_ad_slot ADD auto_create TINYINT(1) DEFAULT \'0\' NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // 1.drop all FK of *related-ron* tables
        $this->addSql('ALTER TABLE ron_ad_slot_segment DROP FOREIGN KEY FK_7EA82F85DB296AAD');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_segment_ron_ad_tag DROP FOREIGN KEY FK_3713B604DB296AAD');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_segment_ron_ad_slot DROP FOREIGN KEY FK_DFE6ACE5DB296AAD');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_segment_segment DROP FOREIGN KEY FK_79A7ED54DB296AAD');
        $this->addSql('ALTER TABLE ron_ad_slot_segment DROP FOREIGN KEY FK_7EA82F85AE3907DE');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_segment_ron_ad_slot DROP FOREIGN KEY FK_DFE6ACE5AE3907DE');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_segment_ron_ad_tag DROP FOREIGN KEY FK_3713B604E7B18F1F');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_segment_ron_ad_slot DROP FOREIGN KEY FK_DFE6ACE5E7B18F1F');

        // 2.drop all *related-ron* tables
        $this->addSql('DROP TABLE ron_ad_slot_segment');
        $this->addSql('DROP TABLE segment');
        $this->addSql('DROP TABLE ron_ad_slot');
        $this->addSql('DROP TABLE report_performance_display_hierarchy_segment_ron_ad_tag');
        $this->addSql('DROP TABLE report_performance_display_hierarchy_segment_ron_ad_slot');
        $this->addSql('DROP TABLE report_performance_display_hierarchy_segment_segment');

        // 3.drop 'auto_create' of core_ad_slot
        $this->addSql('ALTER TABLE core_ad_slot DROP auto_create');

        // 4.drop unique constraint of core_site
        $this->addSql('DROP INDEX site_compound_primary_key ON core_site');

        // 5.drop 'auto_create' of core_site
        $this->addSql('ALTER TABLE core_site DROP auto_create, CHANGE deleted_at deleted_at DATE DEFAULT NULL');

        // 6.rename unique constraint (slot-tag-ref_id) of library_ad_slot_ad_tag
        $this->addSql('ALTER TABLE library_ad_slot_ad_tag DROP FOREIGN KEY FK_DC3B33AE3DC10368');
        $this->addSql('ALTER TABLE library_ad_slot_ad_tag DROP FOREIGN KEY FK_DC3B33AE70BBCB64');
        $this->addSql('DROP INDEX slot_tag_compound_primary_key ON library_ad_slot_ad_tag');
        $this->addSql('CREATE UNIQUE INDEX unique_report_idx ON library_ad_slot_ad_tag (library_ad_tag_id, library_ad_slot_id, ref_id)');
        $this->addSql('ALTER TABLE library_ad_slot_ad_tag ADD CONSTRAINT FK_DC3B33AE3DC10368 FOREIGN KEY (library_ad_tag_id) REFERENCES library_ad_tag (id)');
        $this->addSql('ALTER TABLE library_ad_slot_ad_tag ADD CONSTRAINT FK_DC3B33AE70BBCB64 FOREIGN KEY (library_ad_slot_id) REFERENCES library_ad_slot (id)');

        // 7.drop 'expression_in_js' of library_expression
        $this->addSql('ALTER TABLE library_expression DROP expression_in_js');
    }
}
