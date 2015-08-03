<?php

namespace Tagcade\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 *
 * Commit at Master: 4148a1731f875b096e6a637cb2b6d42aa55365ce
 */
class Version20150610000000_Master extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE core_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, username_canonical VARCHAR(255) NOT NULL, enabled TINYINT(1) NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, locked TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', credentials_expired TINYINT(1) NOT NULL, credentials_expire_at DATETIME DEFAULT NULL, join_date DATE NOT NULL, email VARCHAR(255) DEFAULT NULL, email_canonical VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_BF76157C92FC23A8 (username_canonical), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE source_report_site_config (id INT AUTO_INCREMENT NOT NULL, site_id INT DEFAULT NULL, source_report_email_config_id INT DEFAULT NULL, deleted_at DATE DEFAULT NULL, INDEX IDX_9F66C1B5F6BD1646 (site_id), INDEX IDX_9F66C1B5B0256EB3 (source_report_email_config_id), UNIQUE INDEX unique_source_report_site_config_idx (source_report_email_config_id, site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE action_log (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, ip VARCHAR(45) NOT NULL, server_ip VARCHAR(45) NOT NULL, action VARCHAR(255) NOT NULL, data LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', created_at DATETIME NOT NULL, INDEX IDX_B2C5F685A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE source_report_email_config (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, included_all TINYINT(1) DEFAULT \'0\' NOT NULL, active TINYINT(1) DEFAULT \'1\' NOT NULL, deleted_at DATE DEFAULT NULL, UNIQUE INDEX UNIQ_B04788BDE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE core_user_admin (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE core_user_publisher (id INT NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, company VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, billing_rate NUMERIC(10, 4) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, postal_code VARCHAR(255) DEFAULT NULL, country VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE core_ad_tag (id INT AUTO_INCREMENT NOT NULL, ad_slot_id INT DEFAULT NULL, ad_network_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, html LONGTEXT DEFAULT NULL, position INT DEFAULT 1 NOT NULL, active TINYINT(1) DEFAULT \'1\' NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATE DEFAULT NULL, frequency_cap INT DEFAULT NULL, rotation INT DEFAULT NULL, ad_type INT DEFAULT 0 NOT NULL, descriptor LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', INDEX IDX_D122BEED94AFF818 (ad_slot_id), INDEX IDX_D122BEEDCB9BD82B (ad_network_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE core_dynamic_ad_slot (id INT NOT NULL, site_id INT DEFAULT NULL, default_ad_slot_id INT DEFAULT NULL, INDEX IDX_B7415E41F6BD1646 (site_id), INDEX IDX_B7415E41DC8CAC7B (default_ad_slot_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE core_expression (id INT AUTO_INCREMENT NOT NULL, dynamic_ad_slot_id INT DEFAULT NULL, expect_ad_slot_id INT DEFAULT NULL, expression_descriptor LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', starting_position INT DEFAULT 1 NOT NULL, expression_in_js LONGTEXT NOT NULL COMMENT \'(DC2Type:json_array)\', deleted_at DATE DEFAULT NULL, INDEX IDX_E47CD2E01D925722 (dynamic_ad_slot_id), INDEX IDX_E47CD2E0E4E5E816 (expect_ad_slot_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE core_static_ad_slot (id INT NOT NULL, site_id INT DEFAULT NULL, width INT NOT NULL, height INT NOT NULL, INDEX IDX_761FCF1FF6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE core_ad_network (id INT AUTO_INCREMENT NOT NULL, publisher_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, url VARCHAR(255) DEFAULT NULL, active TINYINT(1) DEFAULT \'1\' NOT NULL, default_cpm_rate NUMERIC(10, 4) DEFAULT NULL, INDEX IDX_9182EAD940C86FCE (publisher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE core_site (id INT AUTO_INCREMENT NOT NULL, publisher_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, domain VARCHAR(255) NOT NULL, deleted_at DATE DEFAULT NULL, enable_source_report TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_5BA6CAD140C86FCE (publisher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE core_ad_slot (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, deleted_at DATE DEFAULT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_source_report (id INT AUTO_INCREMENT NOT NULL, site_id INT DEFAULT NULL, date DATE NOT NULL, display_opportunities INT DEFAULT NULL, display_impressions INT DEFAULT NULL, display_fill_rate NUMERIC(10, 4) DEFAULT NULL, display_clicks INT DEFAULT NULL, display_ctr NUMERIC(10, 4) DEFAULT NULL, display_ipv NUMERIC(10, 4) DEFAULT NULL, video_player_ready INT DEFAULT NULL, video_ad_plays INT DEFAULT NULL, video_ad_impressions INT DEFAULT NULL, video_ad_completions INT DEFAULT NULL, video_ad_completion_rate NUMERIC(10, 4) DEFAULT NULL, video_ipv NUMERIC(10, 4) DEFAULT NULL, video_ad_clicks INT DEFAULT NULL, video_starts INT DEFAULT NULL, video_ends INT DEFAULT NULL, visits INT DEFAULT NULL, page_views INT DEFAULT NULL, qtos INT DEFAULT NULL, qtos_percentage NUMERIC(10, 4) DEFAULT NULL, INDEX IDX_FB0D1312F6BD1646 (site_id), UNIQUE INDEX unique_report_idx (date, site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_source_report_record (id INT AUTO_INCREMENT NOT NULL, source_report_id INT DEFAULT NULL, embedded_tracking_keys LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', display_opportunities INT DEFAULT NULL, display_impressions INT DEFAULT NULL, display_fill_rate NUMERIC(10, 4) DEFAULT NULL, display_clicks INT DEFAULT NULL, display_ctr NUMERIC(10, 4) DEFAULT NULL, display_ipv NUMERIC(10, 4) DEFAULT NULL, video_player_ready INT DEFAULT NULL, video_ad_plays INT DEFAULT NULL, video_ad_impressions INT DEFAULT NULL, video_ad_completions INT DEFAULT NULL, video_ad_completion_rate NUMERIC(10, 4) DEFAULT NULL, video_ipv NUMERIC(10, 4) DEFAULT NULL, video_ad_clicks INT DEFAULT NULL, video_starts INT DEFAULT NULL, video_ends INT DEFAULT NULL, visits INT DEFAULT NULL, page_views INT DEFAULT NULL, qtos INT DEFAULT NULL, qtos_percentage NUMERIC(10, 4) DEFAULT NULL, INDEX IDX_BEF3D0D7234C370E (source_report_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_source_report_record_x_tracking_key (record_id INT NOT NULL, tracking_key_id INT NOT NULL, INDEX IDX_89D775414DFD750C (record_id), INDEX IDX_89D77541A598D67F (tracking_key_id), PRIMARY KEY(record_id, tracking_key_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_performance_display_hierarchy_platform (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, total_opportunities INT NOT NULL, slot_opportunities INT NOT NULL, impressions INT NOT NULL, passbacks INT NOT NULL, fill_rate NUMERIC(10, 4) NOT NULL, est_revenue NUMERIC(10, 4) DEFAULT NULL, est_cpm NUMERIC(10, 4) DEFAULT NULL, billed_rate NUMERIC(10, 4) DEFAULT NULL, billed_amount NUMERIC(10, 4) DEFAULT NULL, UNIQUE INDEX unique_platform_report_idx (date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_performance_display_hierarchy_platform_site (id INT AUTO_INCREMENT NOT NULL, site_id INT DEFAULT NULL, super_report_id INT DEFAULT NULL, date DATE NOT NULL, name VARCHAR(100) DEFAULT NULL, total_opportunities INT NOT NULL, slot_opportunities INT NOT NULL, impressions INT NOT NULL, passbacks INT NOT NULL, fill_rate NUMERIC(10, 4) NOT NULL, est_revenue NUMERIC(10, 4) DEFAULT NULL, est_cpm NUMERIC(10, 4) DEFAULT NULL, billed_rate NUMERIC(10, 4) DEFAULT NULL, billed_amount NUMERIC(10, 4) DEFAULT NULL, INDEX IDX_ADCD6666F6BD1646 (site_id), INDEX IDX_ADCD6666E7B18F1F (super_report_id), UNIQUE INDEX unique_platform_site_report_idx (date, site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_source_tracking_key (id INT AUTO_INCREMENT NOT NULL, tracking_term_id INT DEFAULT NULL, value VARCHAR(255) NOT NULL, INDEX IDX_4F61B9D171EEB4C4 (tracking_term_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_performance_display_hierarchy_ad_network_site (id INT AUTO_INCREMENT NOT NULL, site_id INT DEFAULT NULL, super_report_id INT DEFAULT NULL, date DATE NOT NULL, name VARCHAR(100) DEFAULT NULL, total_opportunities INT NOT NULL, impressions INT NOT NULL, passbacks INT NOT NULL, fill_rate NUMERIC(10, 4) NOT NULL, est_revenue NUMERIC(10, 4) DEFAULT NULL, est_cpm NUMERIC(10, 4) DEFAULT NULL, first_opportunities INT NOT NULL, verified_impressions INT NOT NULL, unverified_impressions INT NOT NULL, blank_impressions INT NOT NULL, void_impressions INT NOT NULL, clicks INT NOT NULL, INDEX IDX_C0092AB5F6BD1646 (site_id), INDEX IDX_C0092AB5E7B18F1F (super_report_id), UNIQUE INDEX unique_ad_network_site_report_idx (date, site_id, super_report_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_performance_display_hierarchy_platform_account (id INT AUTO_INCREMENT NOT NULL, publisher_id INT DEFAULT NULL, super_report_id INT DEFAULT NULL, date DATE NOT NULL, name VARCHAR(100) DEFAULT NULL, total_opportunities INT NOT NULL, slot_opportunities INT NOT NULL, impressions INT NOT NULL, passbacks INT NOT NULL, fill_rate NUMERIC(10, 4) NOT NULL, est_revenue NUMERIC(10, 4) DEFAULT NULL, est_cpm NUMERIC(10, 4) DEFAULT NULL, billed_rate NUMERIC(10, 4) DEFAULT NULL, billed_amount NUMERIC(10, 4) DEFAULT NULL, INDEX IDX_77E240C140C86FCE (publisher_id), INDEX IDX_77E240C1E7B18F1F (super_report_id), UNIQUE INDEX unique_platform_account_report_idx (date, publisher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_source_tracking_term (id INT AUTO_INCREMENT NOT NULL, term VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_performance_display_hierarchy_platform_ad_tag (id INT AUTO_INCREMENT NOT NULL, ad_tag_id INT DEFAULT NULL, super_report_id INT DEFAULT NULL, date DATE NOT NULL, name VARCHAR(100) DEFAULT NULL, position INT NOT NULL, total_opportunities INT NOT NULL, impressions INT NOT NULL, passbacks INT NOT NULL, fill_rate NUMERIC(10, 4) NOT NULL, relative_fill_rate NUMERIC(10, 4) NOT NULL, est_revenue NUMERIC(10, 4) DEFAULT NULL, est_cpm NUMERIC(10, 4) DEFAULT NULL, first_opportunities INT NOT NULL, verified_impressions INT NOT NULL, unverified_impressions INT NOT NULL, blank_impressions INT NOT NULL, void_impressions INT NOT NULL, clicks INT NOT NULL, INDEX IDX_A5C69F3A273D74E4 (ad_tag_id), INDEX IDX_A5C69F3AE7B18F1F (super_report_id), UNIQUE INDEX unique_platform_adtag_report_idx (date, ad_tag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_performance_display_hierarchy_platform_ad_slot (id INT AUTO_INCREMENT NOT NULL, ad_slot_id INT DEFAULT NULL, super_report_id INT DEFAULT NULL, date DATE NOT NULL, name VARCHAR(100) DEFAULT NULL, total_opportunities INT NOT NULL, slot_opportunities INT NOT NULL, impressions INT NOT NULL, passbacks INT NOT NULL, fill_rate NUMERIC(10, 4) NOT NULL, est_revenue NUMERIC(10, 4) DEFAULT NULL, est_cpm NUMERIC(10, 4) DEFAULT NULL, billed_rate NUMERIC(10, 4) DEFAULT NULL, custom_rate NUMERIC(10, 4) DEFAULT NULL, billed_amount NUMERIC(10, 4) DEFAULT NULL, INDEX IDX_1E15646794AFF818 (ad_slot_id), INDEX IDX_1E156467E7B18F1F (super_report_id), UNIQUE INDEX unique_platform_adslot_report_idx (date, ad_slot_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_performance_display_hierarchy_ad_network (id INT AUTO_INCREMENT NOT NULL, ad_network_id INT DEFAULT NULL, date DATE NOT NULL, name VARCHAR(100) DEFAULT NULL, total_opportunities INT NOT NULL, impressions INT NOT NULL, passbacks INT NOT NULL, fill_rate NUMERIC(10, 4) NOT NULL, est_revenue NUMERIC(10, 4) DEFAULT NULL, est_cpm NUMERIC(10, 4) DEFAULT NULL, first_opportunities INT NOT NULL, verified_impressions INT NOT NULL, unverified_impressions INT NOT NULL, blank_impressions INT NOT NULL, void_impressions INT NOT NULL, clicks INT NOT NULL, INDEX IDX_88AB3042CB9BD82B (ad_network_id), UNIQUE INDEX unique_ad_network_report_idx (date, ad_network_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE report_performance_display_hierarchy_ad_network_ad_tag (id INT AUTO_INCREMENT NOT NULL, ad_tag_id INT DEFAULT NULL, super_report_id INT DEFAULT NULL, date DATE NOT NULL, name VARCHAR(100) DEFAULT NULL, total_opportunities INT NOT NULL, impressions INT NOT NULL, passbacks INT NOT NULL, fill_rate NUMERIC(10, 4) NOT NULL, est_revenue NUMERIC(10, 4) DEFAULT NULL, est_cpm NUMERIC(10, 4) DEFAULT NULL, first_opportunities INT NOT NULL, verified_impressions INT NOT NULL, unverified_impressions INT NOT NULL, blank_impressions INT NOT NULL, void_impressions INT NOT NULL, clicks INT NOT NULL, INDEX IDX_70B96999273D74E4 (ad_tag_id), INDEX IDX_70B96999E7B18F1F (super_report_id), UNIQUE INDEX unique_ad_network_adtag_report_idx (date, ad_tag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE source_report_site_config ADD CONSTRAINT FK_9F66C1B5F6BD1646 FOREIGN KEY (site_id) REFERENCES core_site (id)');
        $this->addSql('ALTER TABLE source_report_site_config ADD CONSTRAINT FK_9F66C1B5B0256EB3 FOREIGN KEY (source_report_email_config_id) REFERENCES source_report_email_config (id)');
        $this->addSql('ALTER TABLE action_log ADD CONSTRAINT FK_B2C5F685A76ED395 FOREIGN KEY (user_id) REFERENCES core_user (id)');
        $this->addSql('ALTER TABLE core_user_admin ADD CONSTRAINT FK_568072CFBF396750 FOREIGN KEY (id) REFERENCES core_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE core_user_publisher ADD CONSTRAINT FK_6754B12DBF396750 FOREIGN KEY (id) REFERENCES core_user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE core_ad_tag ADD CONSTRAINT FK_D122BEED94AFF818 FOREIGN KEY (ad_slot_id) REFERENCES core_static_ad_slot (id)');
        $this->addSql('ALTER TABLE core_ad_tag ADD CONSTRAINT FK_D122BEEDCB9BD82B FOREIGN KEY (ad_network_id) REFERENCES core_ad_network (id)');
        $this->addSql('ALTER TABLE core_dynamic_ad_slot ADD CONSTRAINT FK_B7415E41F6BD1646 FOREIGN KEY (site_id) REFERENCES core_site (id)');
        $this->addSql('ALTER TABLE core_dynamic_ad_slot ADD CONSTRAINT FK_B7415E41DC8CAC7B FOREIGN KEY (default_ad_slot_id) REFERENCES core_static_ad_slot (id)');
        $this->addSql('ALTER TABLE core_dynamic_ad_slot ADD CONSTRAINT FK_B7415E41BF396750 FOREIGN KEY (id) REFERENCES core_ad_slot (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE core_expression ADD CONSTRAINT FK_E47CD2E01D925722 FOREIGN KEY (dynamic_ad_slot_id) REFERENCES core_dynamic_ad_slot (id)');
        $this->addSql('ALTER TABLE core_expression ADD CONSTRAINT FK_E47CD2E0E4E5E816 FOREIGN KEY (expect_ad_slot_id) REFERENCES core_static_ad_slot (id)');
        $this->addSql('ALTER TABLE core_static_ad_slot ADD CONSTRAINT FK_761FCF1FF6BD1646 FOREIGN KEY (site_id) REFERENCES core_site (id)');
        $this->addSql('ALTER TABLE core_static_ad_slot ADD CONSTRAINT FK_761FCF1FBF396750 FOREIGN KEY (id) REFERENCES core_ad_slot (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE core_ad_network ADD CONSTRAINT FK_9182EAD940C86FCE FOREIGN KEY (publisher_id) REFERENCES core_user (id)');
        $this->addSql('ALTER TABLE core_site ADD CONSTRAINT FK_5BA6CAD140C86FCE FOREIGN KEY (publisher_id) REFERENCES core_user (id)');
        $this->addSql('ALTER TABLE report_source_report ADD CONSTRAINT FK_FB0D1312F6BD1646 FOREIGN KEY (site_id) REFERENCES core_site (id)');
        $this->addSql('ALTER TABLE report_source_report_record ADD CONSTRAINT FK_BEF3D0D7234C370E FOREIGN KEY (source_report_id) REFERENCES report_source_report (id)');
        $this->addSql('ALTER TABLE report_source_report_record_x_tracking_key ADD CONSTRAINT FK_89D775414DFD750C FOREIGN KEY (record_id) REFERENCES report_source_report_record (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE report_source_report_record_x_tracking_key ADD CONSTRAINT FK_89D77541A598D67F FOREIGN KEY (tracking_key_id) REFERENCES report_source_tracking_key (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_site ADD CONSTRAINT FK_ADCD6666F6BD1646 FOREIGN KEY (site_id) REFERENCES core_site (id)');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_site ADD CONSTRAINT FK_ADCD6666E7B18F1F FOREIGN KEY (super_report_id) REFERENCES report_performance_display_hierarchy_platform_account (id)');
        $this->addSql('ALTER TABLE report_source_tracking_key ADD CONSTRAINT FK_4F61B9D171EEB4C4 FOREIGN KEY (tracking_term_id) REFERENCES report_source_tracking_term (id)');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_ad_network_site ADD CONSTRAINT FK_C0092AB5F6BD1646 FOREIGN KEY (site_id) REFERENCES core_site (id)');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_ad_network_site ADD CONSTRAINT FK_C0092AB5E7B18F1F FOREIGN KEY (super_report_id) REFERENCES report_performance_display_hierarchy_ad_network (id)');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_account ADD CONSTRAINT FK_77E240C140C86FCE FOREIGN KEY (publisher_id) REFERENCES core_user (id)');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_account ADD CONSTRAINT FK_77E240C1E7B18F1F FOREIGN KEY (super_report_id) REFERENCES report_performance_display_hierarchy_platform (id)');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_tag ADD CONSTRAINT FK_A5C69F3A273D74E4 FOREIGN KEY (ad_tag_id) REFERENCES core_ad_tag (id)');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_tag ADD CONSTRAINT FK_A5C69F3AE7B18F1F FOREIGN KEY (super_report_id) REFERENCES report_performance_display_hierarchy_platform_ad_slot (id)');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_slot ADD CONSTRAINT FK_1E15646794AFF818 FOREIGN KEY (ad_slot_id) REFERENCES core_static_ad_slot (id)');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_slot ADD CONSTRAINT FK_1E156467E7B18F1F FOREIGN KEY (super_report_id) REFERENCES report_performance_display_hierarchy_platform_site (id)');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_ad_network ADD CONSTRAINT FK_88AB3042CB9BD82B FOREIGN KEY (ad_network_id) REFERENCES core_ad_network (id)');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_ad_network_ad_tag ADD CONSTRAINT FK_70B96999273D74E4 FOREIGN KEY (ad_tag_id) REFERENCES core_ad_tag (id)');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_ad_network_ad_tag ADD CONSTRAINT FK_70B96999E7B18F1F FOREIGN KEY (super_report_id) REFERENCES report_performance_display_hierarchy_ad_network_site (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE action_log DROP FOREIGN KEY FK_B2C5F685A76ED395');
        $this->addSql('ALTER TABLE core_user_admin DROP FOREIGN KEY FK_568072CFBF396750');
        $this->addSql('ALTER TABLE core_user_publisher DROP FOREIGN KEY FK_6754B12DBF396750');
        $this->addSql('ALTER TABLE core_ad_network DROP FOREIGN KEY FK_9182EAD940C86FCE');
        $this->addSql('ALTER TABLE core_site DROP FOREIGN KEY FK_5BA6CAD140C86FCE');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_account DROP FOREIGN KEY FK_77E240C140C86FCE');
        $this->addSql('ALTER TABLE source_report_site_config DROP FOREIGN KEY FK_9F66C1B5B0256EB3');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_tag DROP FOREIGN KEY FK_A5C69F3A273D74E4');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_ad_network_ad_tag DROP FOREIGN KEY FK_70B96999273D74E4');
        $this->addSql('ALTER TABLE core_expression DROP FOREIGN KEY FK_E47CD2E01D925722');
        $this->addSql('ALTER TABLE core_ad_tag DROP FOREIGN KEY FK_D122BEED94AFF818');
        $this->addSql('ALTER TABLE core_dynamic_ad_slot DROP FOREIGN KEY FK_B7415E41DC8CAC7B');
        $this->addSql('ALTER TABLE core_expression DROP FOREIGN KEY FK_E47CD2E0E4E5E816');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_slot DROP FOREIGN KEY FK_1E15646794AFF818');
        $this->addSql('ALTER TABLE core_ad_tag DROP FOREIGN KEY FK_D122BEEDCB9BD82B');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_ad_network DROP FOREIGN KEY FK_88AB3042CB9BD82B');
        $this->addSql('ALTER TABLE source_report_site_config DROP FOREIGN KEY FK_9F66C1B5F6BD1646');
        $this->addSql('ALTER TABLE core_dynamic_ad_slot DROP FOREIGN KEY FK_B7415E41F6BD1646');
        $this->addSql('ALTER TABLE core_static_ad_slot DROP FOREIGN KEY FK_761FCF1FF6BD1646');
        $this->addSql('ALTER TABLE report_source_report DROP FOREIGN KEY FK_FB0D1312F6BD1646');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_site DROP FOREIGN KEY FK_ADCD6666F6BD1646');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_ad_network_site DROP FOREIGN KEY FK_C0092AB5F6BD1646');
        $this->addSql('ALTER TABLE core_dynamic_ad_slot DROP FOREIGN KEY FK_B7415E41BF396750');
        $this->addSql('ALTER TABLE core_static_ad_slot DROP FOREIGN KEY FK_761FCF1FBF396750');
        $this->addSql('ALTER TABLE report_source_report_record DROP FOREIGN KEY FK_BEF3D0D7234C370E');
        $this->addSql('ALTER TABLE report_source_report_record_x_tracking_key DROP FOREIGN KEY FK_89D775414DFD750C');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_account DROP FOREIGN KEY FK_77E240C1E7B18F1F');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_slot DROP FOREIGN KEY FK_1E156467E7B18F1F');
        $this->addSql('ALTER TABLE report_source_report_record_x_tracking_key DROP FOREIGN KEY FK_89D77541A598D67F');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_ad_network_ad_tag DROP FOREIGN KEY FK_70B96999E7B18F1F');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_site DROP FOREIGN KEY FK_ADCD6666E7B18F1F');
        $this->addSql('ALTER TABLE report_source_tracking_key DROP FOREIGN KEY FK_4F61B9D171EEB4C4');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_platform_ad_tag DROP FOREIGN KEY FK_A5C69F3AE7B18F1F');
        $this->addSql('ALTER TABLE report_performance_display_hierarchy_ad_network_site DROP FOREIGN KEY FK_C0092AB5E7B18F1F');
        $this->addSql('DROP TABLE core_user');
        $this->addSql('DROP TABLE source_report_site_config');
        $this->addSql('DROP TABLE action_log');
        $this->addSql('DROP TABLE source_report_email_config');
        $this->addSql('DROP TABLE core_user_admin');
        $this->addSql('DROP TABLE core_user_publisher');
        $this->addSql('DROP TABLE core_ad_tag');
        $this->addSql('DROP TABLE core_dynamic_ad_slot');
        $this->addSql('DROP TABLE core_expression');
        $this->addSql('DROP TABLE core_static_ad_slot');
        $this->addSql('DROP TABLE core_ad_network');
        $this->addSql('DROP TABLE core_site');
        $this->addSql('DROP TABLE core_ad_slot');
        $this->addSql('DROP TABLE report_source_report');
        $this->addSql('DROP TABLE report_source_report_record');
        $this->addSql('DROP TABLE report_source_report_record_x_tracking_key');
        $this->addSql('DROP TABLE report_performance_display_hierarchy_platform');
        $this->addSql('DROP TABLE report_performance_display_hierarchy_platform_site');
        $this->addSql('DROP TABLE report_source_tracking_key');
        $this->addSql('DROP TABLE report_performance_display_hierarchy_ad_network_site');
        $this->addSql('DROP TABLE report_performance_display_hierarchy_platform_account');
        $this->addSql('DROP TABLE report_source_tracking_term');
        $this->addSql('DROP TABLE report_performance_display_hierarchy_platform_ad_tag');
        $this->addSql('DROP TABLE report_performance_display_hierarchy_platform_ad_slot');
        $this->addSql('DROP TABLE report_performance_display_hierarchy_ad_network');
        $this->addSql('DROP TABLE report_performance_display_hierarchy_ad_network_ad_tag');
    }
}
