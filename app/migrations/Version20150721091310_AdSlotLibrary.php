<?php

namespace Tagcade\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 *
 * Version 2015/07/21 09:13:10 for AdSlot Library
 */
class Version20150721091310_AdSlotLibrary extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // 1.create all table display-native-dynamic library
        //// library_display_ad_slot (id,width, height)
        $this->addSql('CREATE TABLE library_display_ad_slot (id INT NOT NULL, width INT NOT NULL, height INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        //// library_ad_tag (id, ad_network_id, name, html, visible, ad_type, descriptor, created_at, updated_at, deleted_at);
        $this->addSql('CREATE TABLE library_ad_tag (id INT AUTO_INCREMENT NOT NULL, ad_network_id INT DEFAULT NULL, name VARCHAR(100) NOT NULL, html LONGTEXT DEFAULT NULL, visible TINYINT(1) DEFAULT \'0\' NOT NULL, ad_type INT DEFAULT 0 NOT NULL, descriptor LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json_array)\', created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATE DEFAULT NULL, INDEX IDX_DA9C453FCB9BD82B (ad_network_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        //// library_slot_tag (id, library_ad_tag_id, library_ad_slot_id, position, active, frequency_cap, rotation, ref_id, created_at, updated_at, deleted_at)
        $this->addSql('CREATE TABLE library_slot_tag (id INT AUTO_INCREMENT NOT NULL, library_ad_tag_id INT DEFAULT NULL, library_ad_slot_id INT DEFAULT NULL, position INT DEFAULT NULL, active TINYINT(1) DEFAULT \'1\' NOT NULL, frequency_cap INT DEFAULT NULL, rotation INT DEFAULT NULL, ref_id VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATE DEFAULT NULL, INDEX IDX_B71E147A3DC10368 (library_ad_tag_id), INDEX IDX_B71E147A70BBCB64 (library_ad_slot_id), UNIQUE INDEX unique_report_idx (library_ad_tag_id, library_ad_slot_id, ref_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        //// library_dynamic_ad_slot (id, default_ad_slot_id, native)
        $this->addSql('CREATE TABLE library_dynamic_ad_slot (id INT NOT NULL, default_ad_slot_id INT DEFAULT NULL, native TINYINT(1) DEFAULT \'0\' NOT NULL, INDEX IDX_353CFBDCDC8CAC7B (default_ad_slot_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        //// library_native_ad_slot (id)
        $this->addSql('CREATE TABLE library_native_ad_slot (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        //// library_ad_slot (id, publisher_id, name, visible, deleted_at, type)
        $this->addSql('CREATE TABLE library_ad_slot (id INT AUTO_INCREMENT NOT NULL, publisher_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, visible TINYINT(1) NOT NULL, deleted_at DATE DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_6E00CA3240C86FCE (publisher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');

        // 2.all constraints from display-native-dynamic library to publisher, core_ad_slot, adNetwork and together
        //// library_display_ad_slot to library_ad_slot
        $this->addSql('ALTER TABLE library_display_ad_slot ADD CONSTRAINT FK_DCAFF75CBF396750 FOREIGN KEY (id) REFERENCES library_ad_slot (id) ON DELETE CASCADE');
        //// library_ad_tag to core_ad_network
        $this->addSql('ALTER TABLE library_ad_tag ADD CONSTRAINT FK_DA9C453FCB9BD82B FOREIGN KEY (ad_network_id) REFERENCES core_ad_network (id)');
        //// library_slot_tag to library_ad_tag
        $this->addSql('ALTER TABLE library_slot_tag ADD CONSTRAINT FK_B71E147A3DC10368 FOREIGN KEY (library_ad_tag_id) REFERENCES library_ad_tag (id)');
        //// library_slot_tag to library_ad_slot
        $this->addSql('ALTER TABLE library_slot_tag ADD CONSTRAINT FK_B71E147A70BBCB64 FOREIGN KEY (library_ad_slot_id) REFERENCES library_ad_slot (id)');
        //// library_dynamic_ad_slot to core_ad_slot
        $this->addSql('ALTER TABLE library_dynamic_ad_slot ADD CONSTRAINT FK_353CFBDCDC8CAC7B FOREIGN KEY (default_ad_slot_id) REFERENCES core_ad_slot (id)');
        //// library_dynamic_ad_slot to library_ad_slot
        $this->addSql('ALTER TABLE library_dynamic_ad_slot ADD CONSTRAINT FK_353CFBDCBF396750 FOREIGN KEY (id) REFERENCES library_ad_slot (id) ON DELETE CASCADE');
        //// library_native_ad_slot to library_ad_slot
        $this->addSql('ALTER TABLE library_native_ad_slot ADD CONSTRAINT FK_2F487A78BF396750 FOREIGN KEY (id) REFERENCES library_ad_slot (id) ON DELETE CASCADE');
        //// library_ad_slot to core_user
        $this->addSql('ALTER TABLE library_ad_slot ADD CONSTRAINT FK_6E00CA3240C86FCE FOREIGN KEY (publisher_id) REFERENCES core_user (id)');

        // 3.alter table adTag: move reference adTag-adNetwork to adTag-libraryAdTag (libraryAdTag already reference to adNetwork as above)
        // MODIFY: clone old-data from adTag to libraryAdTag, details as:
        // MODIFY: from adTag.id, adTag.adNetworkId, adTag.name, adTag.html, adTag.ad_type, adTag.descriptor
        // MODIFY: to libraryAdTag.id, libraryAdTag.adNetworkId, libraryAdTag.name, libraryAdTag.html, visible=false, libraryAdTag.ad_type, libraryAdTag.descriptor
        // note: libraryAdTag_id set = adTag_id as simplest, not need condition 'where' if need query...
        $this->addSql('INSERT INTO library_ad_tag (id, ad_network_id, name, html, visible, ad_type, descriptor)
                            SELECT t.id, t.ad_network_id, t.name, t.html, false, t.ad_type, t.descriptor
                            FROM core_ad_tag t');
        // MODIFY: end

        // drop constraint from adTag to adNetwork
        $this->addSql('ALTER TABLE core_ad_tag DROP FOREIGN KEY FK_D122BEEDCB9BD82B');
        $this->addSql('DROP INDEX IDX_D122BEEDCB9BD82B ON core_ad_tag');

        // add refId for adTag, drop name/html/ad_type
        $this->addSql('ALTER TABLE core_ad_tag ADD ref_id VARCHAR(255) NOT NULL, DROP name, DROP html, DROP ad_type, DROP descriptor, CHANGE ad_network_id library_ad_tag_id INT DEFAULT NULL');
        // MODIFY: update libraryAdTag_id = id, also update ref_id for adTag
        $this->addSql('UPDATE core_ad_tag SET library_ad_tag_id = id');
        // TODO add later: $this->addSql('UPDATE core_ad_tag SET library_ad_tag_id = id, ref_id = ?', array($this->createRefId()));
        $this->addSql('UPDATE core_ad_tag SET library_ad_tag_id = id, ref_id = REPLACE(UUID(),\'-\',\'\')');
        // MODIFY: end

        // add constraint from adTag to libraryAdTag
        $this->addSql('ALTER TABLE core_ad_tag ADD CONSTRAINT FK_D122BEED3DC10368 FOREIGN KEY (library_ad_tag_id) REFERENCES library_ad_tag (id)');
        $this->addSql('CREATE INDEX IDX_D122BEED3DC10368 ON core_ad_tag (library_ad_tag_id)');

        // 4.alter table dynamicAdSlot: move reference dynamicAdSlot-coreAdSlot to dynamicAdSlot-libraryDynamicAdSlot (libraryDynamicAdSlot already reference to core_ad_slot as above)
        // MODIFY: for using *dynamicAdSlot.default_ad_slot_id* (reference to core_ad_slot), here we clone all data from core_ad_slot ot library_core_ad_slot (instead of do it at the end, after alter library_ad_slot), details as:
        // MODIFY: from adSlot.id, adSlot.site.publisher_id, adSlot.name, visible=false, adSlot.deleted_at, adSlot.type
        // MODIFY: to libraryAdSlot.id, libraryAdSlot.publisher_id, libraryAdSlot.name, libraryAdSlot.visible, libraryAdSlot.deleted_at, libraryAdSlot.type
        $this->addSql('INSERT INTO library_ad_slot (id, publisher_id, name, visible, deleted_at, type)
                            SELECT s.id, site.publisher_id, s.name, false, s.deleted_at, s.type
                            FROM core_ad_slot s, core_site site
                            WHERE s.site_id = site.id');
        // MODIFY: end

        // MODIFY: clone old-data from dynamicAdSlot to libraryDynamicAdSlot, details as:
        // MODIFY: from dynamicAdSlot.id, dynamicAdSlot.default_ad_slot_id, dynamicAdSlot.native
        // MODIFY: to libraryDynamicAdSlot.id, libraryDynamicAdSlot.default_ad_slot_id, libraryDynamicAdSlot.native
        $this->addSql('INSERT INTO library_dynamic_ad_slot (id, default_ad_slot_id, native)
                            SELECT da.id, da.default_ad_slot_id, da.native
                            FROM core_dynamic_ad_slot da');
        // MODIFY: end

        // drop constraint from dynamicAdSlot to coreAdSlot
        $this->addSql('ALTER TABLE core_dynamic_ad_slot DROP FOREIGN KEY FK_B7415E41DC8CAC7B');
        $this->addSql('DROP INDEX IDX_B7415E41DC8CAC7B ON core_dynamic_ad_slot');
        // drop defaultAdSlot_id and native for dynamicAdSlot. defaultAdSlot already in abstractAdSlot, native already in libraryDynamicAdSlot
        $this->addSql('ALTER TABLE core_dynamic_ad_slot DROP default_ad_slot_id, DROP native');

        // 5.alter table expression: move reference expression-dynamicAdSlot to expression-libraryDynamicAdSlot
        // drop constraint to dynamicAdSlot
        $this->addSql('ALTER TABLE core_expression DROP FOREIGN KEY FK_E47CD2E01D925722');
        $this->addSql('DROP INDEX IDX_E47CD2E01D925722 ON core_expression');

        // reference to libraryDynamicAdSlot
        $this->addSql('ALTER TABLE core_expression CHANGE dynamic_ad_slot_id library_dynamic_ad_slot_id INT DEFAULT NULL');
        // MODIFY: also update libraryDynamicAdSlot_id = dynamicAdSlot_id for core_expression, but they are already same values as above
        $this->addSql('ALTER TABLE core_expression ADD CONSTRAINT FK_E47CD2E03AF54DA0 FOREIGN KEY (library_dynamic_ad_slot_id) REFERENCES library_dynamic_ad_slot (id)');
        $this->addSql('CREATE INDEX IDX_E47CD2E03AF54DA0 ON core_expression (library_dynamic_ad_slot_id)');

        // 6.alter table displayAdSlot: drop width/height
        // MODIFY: clone old-data from displayAdSlot to libraryDisplayAdSlot, details as:
        // MODIFY: from displayAdSlot.id, displayAdSlot.width, displayAdSlot.height
        // MODIFY: to libraryDisplayAdSlot.id, libraryDisplayAdSlot.width, libraryDisplayAdSlot.height
        $this->addSql('INSERT INTO library_display_ad_slot (id, width, height)
                            SELECT ds.id, ds.width, ds.height
                            FROM core_display_ad_slot ds');
        // MODIFY: end

        // drop width/height
        $this->addSql('ALTER TABLE core_display_ad_slot DROP width, DROP height');

        // 7.alter table coreAdSlot: add library_ad_slot_id which references from coreAdSlot to libraryAdSlot
        // add library_ad_slot_id/slotType fields, drop name (name already in library_ad_slot)
        $this->addSql('ALTER TABLE core_ad_slot ADD library_ad_slot_id INT DEFAULT NULL, DROP name, CHANGE type slotType VARCHAR(255) NOT NULL');

        // MODIFY: update all library_ad_slot_id = id for core_ad_slot because all library_display/native/dynamic_adSlots.ids set = display/native/dynamic_adSlots.ids before (see above)
        // new_id = id as simplest!!!
        $this->addSql('UPDATE core_ad_slot SET library_ad_slot_id = id');
        // MODIFY: end

        // create reference coreAdSlot-libraryAdSlot
        $this->addSql('ALTER TABLE core_ad_slot ADD CONSTRAINT FK_6D6C73170BBCB64 FOREIGN KEY (library_ad_slot_id) REFERENCES library_ad_slot (id)');
        $this->addSql('CREATE INDEX IDX_6D6C73170BBCB64 ON core_ad_slot (library_ad_slot_id)');

        // 8.clone data for libraryNativeAdSlot
        // MODIFY: clone all all id = core_ad_slot.id for libraryNativeAdSlot
        $this->addSql('INSERT INTO library_native_ad_slot (id)
                            SELECT ns.id
                            FROM core_native_ad_slot ns');
        // MODIFY: end
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // 1.drop all constrains to library
        $this->addSql('ALTER TABLE library_slot_tag DROP FOREIGN KEY FK_B71E147A3DC10368');
        $this->addSql('ALTER TABLE core_ad_tag DROP FOREIGN KEY FK_D122BEED3DC10368');
        $this->addSql('ALTER TABLE core_expression DROP FOREIGN KEY FK_E47CD2E03AF54DA0');
        $this->addSql('ALTER TABLE library_display_ad_slot DROP FOREIGN KEY FK_DCAFF75CBF396750');
        $this->addSql('ALTER TABLE library_slot_tag DROP FOREIGN KEY FK_B71E147A70BBCB64');
        $this->addSql('ALTER TABLE library_dynamic_ad_slot DROP FOREIGN KEY FK_353CFBDCBF396750');
        $this->addSql('ALTER TABLE library_native_ad_slot DROP FOREIGN KEY FK_2F487A78BF396750');
        $this->addSql('ALTER TABLE core_ad_slot DROP FOREIGN KEY FK_6D6C73170BBCB64');

        // DOCTRINE: drop all library tables
        //$this->addSql('DROP TABLE library_display_ad_slot');
        //$this->addSql('DROP TABLE library_ad_tag');
        //$this->addSql('DROP TABLE library_slot_tag');
        //$this->addSql('DROP TABLE library_dynamic_ad_slot');
        //$this->addSql('DROP TABLE library_native_ad_slot');
        //$this->addSql('DROP TABLE library_ad_slot');
        // end-DOCTRINE
        // but NEED rollback all data from libraries to core_tables, then drop library tables later

        // 2.alter table core_ad_slot
        // drop index
        $this->addSql('DROP INDEX IDX_6D6C73170BBCB64 ON core_ad_slot');

        // DOCTRINE: add name, drop library_ad_slot_id, change slottype to type
        // $this->addSql('ALTER TABLE core_ad_slot ADD name VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci, DROP library_ad_slot_id, CHANGE slottype type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');
        // end-DOCTRINE
        // add name: need clone from library_ad_slot ,drop library_ad_slot_id: not need backup, change slottype to type: value not changed
        // but NEED rollback all data from libraries to core_tables (on column "name"), then drop/change library tables later

        // MODIFY: add "name" and rollback data
        $this->addSql('ALTER TABLE core_ad_slot ADD name VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci');

        // MODIFY: rollback data on column "name"
        $this->addSql('UPDATE core_ad_slot s, library_ad_slot ls
                            SET s.name = ls.name
                            WHERE s.library_ad_slot_id = ls.id');

        // MODIFY: then drop/change library tables later BUT execute at the end because library_ad_slot_id used to rollback core_display_ad_slot
        // $this->addSql('ALTER TABLE core_ad_slot DROP library_ad_slot_id, CHANGE slottype type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');

        // 3.alter table core_ad_tag: rollback data from library_ad_tag
        // drop index
        $this->addSql('DROP INDEX IDX_D122BEED3DC10368 ON core_ad_tag');

        // DOCTRINE: add name/html/ad_type/descriptor and ad_network_id, then rollback data, finally DROP ad_tag_library_id INT DEFAULT NULL
        //$this->addSql('ALTER TABLE core_ad_tag ADD name VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci, ADD html LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, ADD ad_type INT DEFAULT 0 NOT NULL, ADD descriptor LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\', DROP ref_id, CHANGE library_ad_tag_id ad_network_id INT DEFAULT NULL');
        // end-DOCTRINE
        // MODIFY: but we will execute step by step:
        // MODIFY: add name/html/ad_type/descriptor and ad_network_id, then rollback data, finally DROP ref_id, library_ad_tag_id
        $this->addSql('ALTER TABLE core_ad_tag ADD name VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci, ADD html LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, ADD ad_type INT DEFAULT 0 NOT NULL, ADD descriptor LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:json_array)\', ADD ad_network_id INT DEFAULT NULL');

        // MODIFY: then rollback data
        // MODIFY: clone old-data from libraryAdTag to adTag, details as:
        // MODIFY: from libraryAdTag.name, libraryAdTag.html, libraryAdTag.ad_type, libraryAdTag.descriptor, libraryAdTag.adNetworkId
        // MODIFY: to adTag.name, adTag.html, adTag.ad_type, adTag.descriptor, adTag.adNetworkId
        $this->addSql('UPDATE core_ad_tag t, library_ad_tag lt
                            SET t.name = lt.name, t.html = lt.html, t.ad_type = lt.ad_type, t.descriptor = lt.descriptor, t.ad_network_id = lt.ad_network_id
                            WHERE t.library_ad_tag_id = lt.id');

        // MODIFY: finally DROP ref_id, library_ad_tag_id
        $this->addSql('ALTER TABLE core_ad_tag DROP ref_id, DROP library_ad_tag_id');
        // MODIFY: end

        // add constraint to ad_net_work
        $this->addSql('ALTER TABLE core_ad_tag ADD CONSTRAINT FK_D122BEEDCB9BD82B FOREIGN KEY (ad_network_id) REFERENCES core_ad_network (id)');
        $this->addSql('CREATE INDEX IDX_D122BEEDCB9BD82B ON core_ad_tag (ad_network_id)');

        // 4.alter table core_display_ad_slot: rollback data from library_display_ad_slot
        $this->addSql('ALTER TABLE core_display_ad_slot ADD width INT NOT NULL, ADD height INT NOT NULL');

        // MODIFY: clone old-data from libraryDisplayAdSlot to displayAdSlot, details as:
        // MODIFY: from libraryDisplayAdSlot.width, libraryDisplayAdSlot.height
        // MODIFY: to displayAdSlot.width, displayAdSlot.height
        $this->addSql('UPDATE core_display_ad_slot ds, library_display_ad_slot lds, core_ad_slot s
                            SET ds.width = lds.width, ds.height = lds.height
                            WHERE ds.id = s.id AND s.library_ad_slot_id = lds.id');

        // 5.alter table core_dynamic_ad_slot: rollback data from library_dynamic_ad_slot
        $this->addSql('ALTER TABLE core_dynamic_ad_slot ADD default_ad_slot_id INT DEFAULT NULL, ADD native TINYINT(1) DEFAULT \'0\' NOT NULL');

        // MODIFY: clone old-data from libraryDynamicAdSlot to dynamicAdSlot, details as:
        // MODIFY: from libraryDynamicAdSlot.native, libraryAdSlot.default_ad_slot_id
        // MODIFY: to dynamicAdSlot.native, dynamicAdSlot.default_ad_slot_id
        $this->addSql('UPDATE core_dynamic_ad_slot ds, library_dynamic_ad_slot lds, core_ad_slot s
                            SET ds.native = lds.native, ds.default_ad_slot_id = lds.default_ad_slot_id
                            WHERE ds.id = lds.id AND s.library_ad_slot_id = lds.id');
        // MODIFY: end

        // add constraint to core_ad_slot
        $this->addSql('ALTER TABLE core_dynamic_ad_slot ADD CONSTRAINT FK_B7415E41DC8CAC7B FOREIGN KEY (default_ad_slot_id) REFERENCES core_ad_slot (id)');
        $this->addSql('CREATE INDEX IDX_B7415E41DC8CAC7B ON core_dynamic_ad_slot (default_ad_slot_id)');

        // 6.alter table core_expression
        // drop index
        $this->addSql('DROP INDEX IDX_E47CD2E03AF54DA0 ON core_expression');

        // rollback data
        // DOCTRINE: change library_dynamic_ad_slot_id to dynamic_ad_slot_id
        //$this->addSql('ALTER TABLE core_expression CHANGE library_dynamic_ad_slot_id dynamic_ad_slot_id INT DEFAULT NULL');
        // but NEED add dynamic_ad_slot_id, then rollback data, finally drop library_dynamic_ad_slot_id

        // MODIFY: NEED add dynamic_ad_slot_id
        $this->addSql('ALTER TABLE core_expression ADD dynamic_ad_slot_id INT DEFAULT NULL');

        // MODIFY: then rollback data
        // MODIFY: clone old-data from dynamicAdSlot to expression, details as:
        // MODIFY: from dynamicAdSlot.id
        // MODIFY: to expression.dynamic_ad_slot_id
        // MODIFY: where expression.library_dynamic_ad_slot_id = coreAdSlot.libraryAdSlot.id AND coreAdSlot.id = dynamicAdSlot.id
        $this->addSql('UPDATE core_expression e, core_dynamic_ad_slot ds, core_ad_slot s
                            SET e.dynamic_ad_slot_id = ds.id
                            WHERE e.library_dynamic_ad_slot_id = s.library_ad_slot_id AND s.id = ds.id');

        // MODIFY: finally drop library_dynamic_ad_slot_id
        $this->addSql('ALTER TABLE core_expression DROP library_dynamic_ad_slot_id');
        // MODIFY: end

        // add constraint to core_dynamic_ad_slot
        $this->addSql('ALTER TABLE core_expression ADD CONSTRAINT FK_E47CD2E01D925722 FOREIGN KEY (dynamic_ad_slot_id) REFERENCES core_dynamic_ad_slot (id)');
        $this->addSql('CREATE INDEX IDX_E47CD2E01D925722 ON core_expression (dynamic_ad_slot_id)');

        // 7.FINALLY
        // 7.1.DROP library_ad_slot_id for core_ad_slot
        // MODIFY: execute at the end, after rollback core_display_ad_slot
        $this->addSql('ALTER TABLE core_ad_slot DROP library_ad_slot_id, CHANGE slottype type VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci');

        // 7.2.FINALLY, drop all library tables
        // MODIFY: FINALLY, drop all library tables
        $this->addSql('DROP TABLE library_display_ad_slot');
        $this->addSql('DROP TABLE library_ad_tag');
        $this->addSql('DROP TABLE library_slot_tag');
        $this->addSql('DROP TABLE library_dynamic_ad_slot');
        $this->addSql('DROP TABLE library_native_ad_slot');
        $this->addSql('DROP TABLE library_ad_slot');
        // MODIFY: end
    }
}