<?php

namespace Tagcade\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 *
 * Commit at Master: 5b3dd19befb39ae83be9f0ce4a451f880d81c354
 */
class Version20151026100427_AdTagCount_For_AdNetwork extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE core_ad_network ADD active_ad_tags_count INT DEFAULT 0 NOT NULL, ADD paused_ad_tags_count INT DEFAULT 0 NOT NULL');

        $this->warnIf(true, sprintf('After this, You must run the command "%s" to update ad-tag-count for all existing Ad Networks!!!', 'tc:ad-network:refresh-ad-tag-count'));
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE core_ad_network DROP active_ad_tags_count, DROP paused_ad_tags_count');
    }
}
