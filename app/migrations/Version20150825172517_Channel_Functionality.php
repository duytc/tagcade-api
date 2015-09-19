<?php

namespace Tagcade\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150825172517_Channel_Functionality extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE core_channel_site (id INT AUTO_INCREMENT NOT NULL, channel_id INT DEFAULT NULL, site_id INT DEFAULT NULL, deleted_at DATE DEFAULT NULL, INDEX IDX_58A7134272F5A1AA (channel_id), INDEX IDX_58A71342F6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE core_channel (id INT AUTO_INCREMENT NOT NULL, publisher_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, deleted_at DATE DEFAULT NULL, INDEX IDX_B0EE3B7440C86FCE (publisher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE core_channel_site ADD CONSTRAINT FK_58A7134272F5A1AA FOREIGN KEY (channel_id) REFERENCES core_channel (id)');
        $this->addSql('ALTER TABLE core_channel_site ADD CONSTRAINT FK_58A71342F6BD1646 FOREIGN KEY (site_id) REFERENCES core_site (id)');
        $this->addSql('ALTER TABLE core_channel ADD CONSTRAINT FK_B0EE3B7440C86FCE FOREIGN KEY (publisher_id) REFERENCES core_user (id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE core_channel_site DROP FOREIGN KEY FK_58A7134272F5A1AA');
        $this->addSql('DROP TABLE core_channel_site');
        $this->addSql('DROP TABLE core_channel');
    }
}
