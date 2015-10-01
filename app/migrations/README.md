Doctrine Migrations
===================

1. Installation
---------------
Doctrine migrations for Symfony are maintained in the [DoctrineMigrationsBundle](https://github.com/doctrine/DoctrineMigrationsBundle). The bundle uses external [Doctrine Database Migrations](https://github.com/doctrine/migrations) library.

Follow these steps to install the bundle and the library in the Symfony Standard edition. Add the following to your composer.json file:

```
{
    "require": {
        "doctrine/migrations": "1.0.*@dev",
        "doctrine/doctrine-migrations-bundle": "1.0.*"
    }
}
```

Update the vendor libraries:
```
$ php composer.phar update
```

If everything worked, the DoctrineMigrationsBundle can now be found at vendor/doctrine/doctrine-migrations-bundle.

Finally, be sure to enable the bundle in AppKernel.php by including the following:
```
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        //...
        new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
    );
}
```

2. Config
---------
You can configure the path, namespace, table_name and name in config.yml for Tagcade as:
```
// app/config/config.yml
doctrine_migrations:
    dir_name: %kernel.root_dir%/migrations
    namespace: Tagcade\Migration
    table_name: doctrine_migration_versions
    name: Tagcade Migrations
```

3. Create migrations
--------------------
For the first using of Doctrine Migration, we create the empty migration associates to master branch by command:
```
php app/console doctrine:migrations:generate
```

The output migration file will be located as doctrine_migrations config in app/config/config.yml. The name of migration will be formatted as 'VersionYYYYMMDDHHmmss.php' (year month date hour min sec).
For example: Version20150615173322.php
```
/** MASTER-version */
class Version20150615173322 extends AbstractMigration
{
    /** @param Schema $schema */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
    }

    /** @param Schema $schema */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
```

Then now we can create other migrations for other branches (develop, feature, ...) by using command:
```
php app/console doctrine:migrations:diff
```

This will automatically generate differences between master and schema config of current version branch which put in 'up' and 'down' methods.
The 'down' method is used for migrating to new version. And the 'up' method is used for returning master version.

The location and name of output migration file will be as above ('VersionYYYYMMDDHHmmss.php').
For example: Version20150615173543.php
```
/** DEVELOP-version */
class Version20150615173543 extends AbstractMigration
{
    /** @param Schema $schema */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE core_native_ad_slot (id INT NOT NULL, site_id INT DEFAULT NULL, INDEX IDX_5A19262EF6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE core_native_ad_slot ADD CONSTRAINT FK_5A19262EF6BD1646 FOREIGN KEY (site_id) REFERENCES core_site (id)');
        $this->addSql('ALTER TABLE core_native_ad_slot ADD CONSTRAINT FK_5A19262EBF396750 FOREIGN KEY (id) REFERENCES core_ad_slot (id) ON DELETE CASCADE');
        ...more sql...

        /** manual sql: change value from 'static' to 'display' for Static_AdSlot table */
        $this->addSql('UPDATE core_ad_slot SET type=\'display\' WHERE type=\'static\'');
    }

    /** @param Schema $schema */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE core_native_ad_slot');
        ...more sql...

        /** manual sql: change value from 'display' to 'static' for Static_AdSlot table */
        $this->addSql('UPDATE core_ad_slot SET type=\'static\' WHERE type=\'display\'');
    }
}
```

* NOTE:
- don't using ```php app/console doctrine:schema:update --force```, the doctrine-migrations will do this. You can do ```php app/console doctrine:schema:update --dump-sql``` for checking change before/after using doctrine-migration.
- we can add some sql into up/down functions for alter table (data, structure, ...) manually;
For example, because MASTER-version using core_ad_slot's type = 'static' while DEVELOP-version using core_ad_slot's type = 'display', we added sql as following:
```
/** manual sql: change value from 'static' to 'display' for Static_AdSlot table */
$this->addSql('UPDATE core_ad_slot SET type=\'display\' WHERE type=\'static\'');
```

and

```
/** manual sql: change value from 'display' to 'static' for Static_AdSlot table */
$this->addSql('UPDATE core_ad_slot SET type=\'static\' WHERE type=\'display\'');
```

4. Migrate to version
---------------------
* To migrate 'continuously' to a version, using:
```
php app/console doctrine:migrations:migrate [migrate version in YYYYMMDDHHmmss]'
```
This command migrates sequence up (if migrate up) or down (if migrate down) steps from current version, through all versions between and to destination version need migrated.
So, this using for migration between stable versions for product.

* To execute special migration version, use
```
php app/console doctrine:migrations:execute [migrate version in YYYYMMDDHHmmss] --up/--down
```
This command will execute only up (if migrate up) or down (if migrate down) step of destination version need migrated.
So, this using for migration between develop versions when create/edit migration versions (for testing, force migrate...).

For example, we have all version as:
```
20150610000000_Master
20150611105251_Master0
20150613140449_NativeAdSlot
20150619142407_RenameStaticAdSlot
20150629144546_ReportSettings
20150702170158_RenameCoreAdSlotAbstract
20150703000000_RenameCoreAdSlot
20150716152759_RemoveActiveInAdNetwork
20150721091310_AdSlotLibrary
```

associated to php class files (in migrations folder):

```
Version20150610000000_Master.php
Version20150611105251_Master0.php
Version20150613140449_NativeAdSlot.php
Version20150619142407_RenameStaticAdSlot.php
Version20150629144546_ReportSettings.php
Version20150702170158_RenameCoreAdSlotAbstract.php
Version20150703000000_RenameCoreAdSlot.php
Version20150716152759_RemoveActiveInAdNetwork.php
Version20150721091310_AdSlotLibrary.php
```
(all version we added '_<description>' after 'Version[datetime]' for more detail about version, for example, Version20150721091310_AdSlotLibrary means version was created at 2015/07/21 09:13:10 for Ad Slot Library feature).

Now we migrate between migration versions as following:

- migrate from 'ZERO' to 'master':
```
php app/console doctrine:migrations:migrate master
```
=> current version is 'master' with simplest tables created.


- migrate from 'master' to '20150629144546_ReportSettings':
```
php app/console doctrine:migrations:migrate 20150629144546_ReportSettings
```
=> migrate from 20150610000000_Master

             to 20150611105251_Master0

      then   to 20150613140449_NativeAdSlot

      then   to 20150619142407_RenameStaticAdSlot

     finally to 20150629144546_ReportSettings

=> current version is '20150629144546_ReportSettings' with feature 'ReportSettings'.


- migrate from '20150629144546_ReportSettings' to 'previous version' (here is '20150619142407_RenameStaticAdSlot'):
```
php app/console doctrine:migrations:migrate prev
```
=> current version is '20150619142407_RenameStaticAdSlot' with feature 'RenameStaticAdSlot'.


- migrate from '20150619142407_RenameStaticAdSlot' to 'latest version' (here is '20150721091310_AdSlotLibrary'):
```
php app/console doctrine:migrations:migrate
```
=> migrate from 20150619142407_RenameStaticAdSlot

             to 20150629144546_ReportSettings

      then   to 20150702170158_RenameCoreAdSlotAbstract

      then   to 20150703000000_RenameCoreAdSlot

      then   to 20150716152759_RemoveActiveInAdNetwork

     finally to 20150721091310_AdSlotLibrary

=> current version is '20150721091310_AdSlotLibrary' with feature 'AdSlotLibrary'.


- execute migration version from 'latest version' (here is '20150721091310_AdSlotLibrary') down:
```
php app/console doctrine:migrations:execute 20150721091310_AdSlotLibrary --down
```
=> will execute function 'down()' in file Version20150721091310_AdSlotLibrary.php

=> current version is '20150716152759_RemoveActiveInAdNetwork' with feature 'RemoveActiveInAdNetwork'.


- execute migration version from '20150716152759_RemoveActiveInAdNetwork' up:
```
php app/console doctrine:migrations:execute 20150716152759_RemoveActiveInAdNetwork --up
```
=> will execute function 'up()' in file 20150716152759_RemoveActiveInAdNetwork

=> current version is '20150721091310_AdSlotLibrary' with feature 'AdSlotLibrary'.


- BUT, now execute migration version from NOT current version, as '20150629144546_ReportSettings', (current is '20150721091310_AdSlotLibrary') down:
```
php app/console doctrine:migrations:execute 20150629144546_ReportSettings --down
```
=> will execute function 'down()' in file 20150629144546_ReportSettings.php
=> will get errors because some tables not exit, or some fields not exist or references invalid... Be careful!


* Note: clear cache after doing new config:
```
php app/console cache:clear
```


5. Show Migrate versions
------------------------
- Show version status:
```
php app/console doctrine:migrations:status
```
For example we have result with detail Previous/Current/Next/Latest Versions:
```
== Configuration

    >> Name:                                               Tagcade Migrations
    >> Database Driver:                                    pdo_mysql
    >> Database Name:                                      tagcade_api
    >> Configuration Source:                               manually configured
    >> Version Table Name:                                 doctrine_migration_versions
    >> Migrations Namespace:                               Tagcade\Migration
    >> Migrations Directory:                               /var/www/api.tagcade.dev/app/migrations
    >> Previous Version:                                    (20150703000000_RenameCoreAdSlot)
    >> Current Version:                                     (20150716152759_RemoveActiveInAdNetwork)
    >> Next Version:                                        (20150721091310_AdSlotLibrary)
    >> Latest Version:                                      (20150721091310_AdSlotLibrary)
    >> Executed Migrations:                                8
    >> Executed Unavailable Migrations:                    0
    >> Available Migrations:                               9
    >> New Migrations:                                     1
```

- Show all versions with status and name:
```
php app/console doctrine:migrations:status --show-versions
```
For example we have result with detail Versions with status and name:
```
== Configuration

    >> Name:                                               Tagcade Migrations
    >> Database Driver:                                    pdo_mysql
    >> Database Name:                                      tagcade_api
    >> Configuration Source:                               manually configured
    >> Version Table Name:                                 doctrine_migration_versions
    >> Migrations Namespace:                               Tagcade\Migration
    >> Migrations Directory:                               /var/www/api.tagcade.dev/app/migrations
    >> Previous Version:                                    (20150703000000_RenameCoreAdSlot)
    >> Current Version:                                     (20150716152759_RemoveActiveInAdNetwork)
    >> Next Version:                                        (20150721091310_AdSlotLibrary)
    >> Latest Version:                                      (20150721091310_AdSlotLibrary)
    >> Executed Migrations:                                8
    >> Executed Unavailable Migrations:                    0
    >> Available Migrations:                               9
    >> New Migrations:                                     1

 == Available Migration Versions

    >>  (20150610000000_Master)                            migrated
    >>  (20150611105251_Master0)                           migrated
    >>  (20150613140449_NativeAdSlot)                      migrated
    >>  (20150619142407_RenameStaticAdSlot)                migrated
    >>  (20150629144546_ReportSettings)                    migrated
    >>  (20150702170158_RenameCoreAdSlotAbstract)          migrated
    >>  (20150703000000_RenameCoreAdSlot)                  migrated
    >>  (20150716152759_RemoveActiveInAdNetwork)           migrated
    >>  (20150721091310_AdSlotLibrary)                     not migrated

 == Previously Executed Unavailable Migration Versions

    >>  (20150610000000_Master)                            migrated
    >>  (20150611105251_Master0)                           migrated
    >>  (20150613140449_NativeAdSlot)                      migrated
    >>  (20150619142407_RenameStaticAdSlot)                migrated
    >>  (20150629144546_ReportSettings)                    migrated
    >>  (20150702170158_RenameCoreAdSlotAbstract)          migrated
    >>  (20150703000000_RenameCoreAdSlot)                  migrated
    >>  (20150716152759_RemoveActiveInAdNetwork)           migrated
    >>  (20150721091310_AdSlotLibrary)                     not migrated
```

6. Manager versions in database
-------------------------------
All migrated versions will be inserted to current database in table 'doctrine_migration_versions', using version name as primary key (so the format 'VersionYYYYMMDDHHmmss' is very important for recognizing the order of all versions!).
Doctrine-migrations uses all versions in database to know which versions are migrated / not migrated, then displays status of all versions when using command 'doctrine:migrations:status';
BUT note that managing migration versions is used only if you have existing DB or you run ```doctrine:schema:update``` instead, so this command makes doctrine_migration_versions table is synchronized with current database structure.

We can manager versions with following commands:

- Add one version:
```
php app/console doctrine:migrations:version --add <version name which existed as 'status --show-versions'>
```

- Delete one version:
```
php app/console doctrine:migrations:version --delete <version name which existed as 'status --show-versions'>
```

- Add all versions:
```
php app/console doctrine:migrations:version --add --all
```
Use above command if you have empty doctrine_migration_versions table, the command will insert all versions existed from code (in 'migrations' folder) into doctrine_migration_versions tables.

- Delete all versions:
```
php app/console doctrine:migrations:version --delete --all
```
Use above command if you want to empty doctrine_migration_versions table before using '--add --all' versions command or migrating from beginning.

* Be careful: use above commands when create/edit migration versions by adding/deleting versions manually, not recommended for stable version.

-----------
Great work!
-----------

(For more details, go to [DoctrineMigrationsBundle](http://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html).)