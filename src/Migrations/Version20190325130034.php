<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190325130034 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE messaging_file');
        $this->addSql('ALTER TABLE sav_status_setting MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE sav_status_setting DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE sav_status_setting ADD sav_id INT NOT NULL, ADD status_setting_id INT NOT NULL, DROP id');
        $this->addSql('ALTER TABLE sav_status_setting ADD CONSTRAINT FK_A04F51A24F726353 FOREIGN KEY (sav_id) REFERENCES sav (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sav_status_setting ADD CONSTRAINT FK_A04F51A2EF6659F4 FOREIGN KEY (status_setting_id) REFERENCES status_setting (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_A04F51A24F726353 ON sav_status_setting (sav_id)');
        $this->addSql('CREATE INDEX IDX_A04F51A2EF6659F4 ON sav_status_setting (status_setting_id)');
        $this->addSql('ALTER TABLE sav_status_setting ADD PRIMARY KEY (sav_id, status_setting_id)');
        $this->addSql('ALTER TABLE sav_nature_setting MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE sav_nature_setting DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE sav_nature_setting ADD sav_id INT NOT NULL, ADD nature_setting_id INT NOT NULL, DROP id');
        $this->addSql('ALTER TABLE sav_nature_setting ADD CONSTRAINT FK_B01C6DF24F726353 FOREIGN KEY (sav_id) REFERENCES sav (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sav_nature_setting ADD CONSTRAINT FK_B01C6DF224B0EE5 FOREIGN KEY (nature_setting_id) REFERENCES nature_setting (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_B01C6DF24F726353 ON sav_nature_setting (sav_id)');
        $this->addSql('CREATE INDEX IDX_B01C6DF224B0EE5 ON sav_nature_setting (nature_setting_id)');
        $this->addSql('ALTER TABLE sav_nature_setting ADD PRIMARY KEY (sav_id, nature_setting_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE messaging_file (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE sav_nature_setting DROP FOREIGN KEY FK_B01C6DF24F726353');
        $this->addSql('ALTER TABLE sav_nature_setting DROP FOREIGN KEY FK_B01C6DF224B0EE5');
        $this->addSql('DROP INDEX IDX_B01C6DF24F726353 ON sav_nature_setting');
        $this->addSql('DROP INDEX IDX_B01C6DF224B0EE5 ON sav_nature_setting');
        $this->addSql('ALTER TABLE sav_nature_setting DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE sav_nature_setting ADD id INT AUTO_INCREMENT NOT NULL, DROP sav_id, DROP nature_setting_id');
        $this->addSql('ALTER TABLE sav_nature_setting ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE sav_status_setting DROP FOREIGN KEY FK_A04F51A24F726353');
        $this->addSql('ALTER TABLE sav_status_setting DROP FOREIGN KEY FK_A04F51A2EF6659F4');
        $this->addSql('DROP INDEX IDX_A04F51A24F726353 ON sav_status_setting');
        $this->addSql('DROP INDEX IDX_A04F51A2EF6659F4 ON sav_status_setting');
        $this->addSql('ALTER TABLE sav_status_setting DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE sav_status_setting ADD id INT AUTO_INCREMENT NOT NULL, DROP sav_id, DROP status_setting_id');
        $this->addSql('ALTER TABLE sav_status_setting ADD PRIMARY KEY (id)');
    }
}
