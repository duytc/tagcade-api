<?php

namespace Tagcade\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 *
 * Commit at Master: 2f83dfcf11922833f7c99fbf7e149d02b2a49e0d
 */
class Version20151009093556_PassbackMode_For_DisplayAdSlot extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE library_display_ad_slot ADD passback_mode VARCHAR(255) NOT NULL');

        // update passback_mode default as 'position' for library display ad slot if NULL
        $this->addSql('UPDATE library_display_ad_slot SET passback_mode = \'position\' WHERE passback_mode IS NULL OR passback_mode = \'\'');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE library_display_ad_slot DROP passback_mode');
    }
}
