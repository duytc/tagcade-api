<?php

namespace Tagcade\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 *
 * Commit at Master: 0831cd1d2c51a55e9487fcd4d11b973652290d58
 */
class Version20151105103937_CloneConfig_For_SourceReport extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE source_report_email_config ADD included_all_sites_of_publishers LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\'');
        $this->addSql('ALTER TABLE core_user_publisher CHANGE tag_domain tag_domain LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE core_user_publisher CHANGE tag_domain tag_domain VARCHAR(255) DEFAULT NULL COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE source_report_email_config DROP included_all_sites_of_publishers');
    }
}
