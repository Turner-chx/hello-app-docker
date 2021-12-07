<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190325125731 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sav_file MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE sav_file DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE sav_file ADD sav_id INT NOT NULL, ADD file_id INT NOT NULL, DROP id');
        $this->addSql('ALTER TABLE sav_file ADD CONSTRAINT FK_A0DF51814F726353 FOREIGN KEY (sav_id) REFERENCES sav (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sav_file ADD CONSTRAINT FK_A0DF518193CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_A0DF51814F726353 ON sav_file (sav_id)');
        $this->addSql('CREATE INDEX IDX_A0DF518193CB796C ON sav_file (file_id)');
        $this->addSql('ALTER TABLE sav_file ADD PRIMARY KEY (sav_id, file_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sav_file DROP FOREIGN KEY FK_A0DF51814F726353');
        $this->addSql('ALTER TABLE sav_file DROP FOREIGN KEY FK_A0DF518193CB796C');
        $this->addSql('DROP INDEX IDX_A0DF51814F726353 ON sav_file');
        $this->addSql('DROP INDEX IDX_A0DF518193CB796C ON sav_file');
        $this->addSql('ALTER TABLE sav_file DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE sav_file ADD id INT AUTO_INCREMENT NOT NULL, DROP sav_id, DROP file_id');
        $this->addSql('ALTER TABLE sav_file ADD PRIMARY KEY (id)');
    }
}
