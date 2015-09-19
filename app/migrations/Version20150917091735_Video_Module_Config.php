<?php

namespace Tagcade\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 * Version 20150917091735 for Video_Module_Config feature
 */
class Version20150917091735_Video_Module_Config extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // add players for site
        $this->addSql('ALTER TABLE core_site ADD players LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\'');

        // rename slottype to slot_type
        $this->addSql('ALTER TABLE core_ad_slot CHANGE slottype slot_type VARCHAR(255) NOT NULL');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // rename slot_type to slottype
        $this->addSql('ALTER TABLE core_ad_slot CHANGE slot_type slotType VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');

        // delete players in site
        $this->addSql('ALTER TABLE core_site DROP players');
    }
}
