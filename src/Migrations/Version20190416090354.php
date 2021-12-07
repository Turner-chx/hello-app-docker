<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190416090354 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sav ADD status_setting_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sav ADD CONSTRAINT FK_6C7681F4EF6659F4 FOREIGN KEY (status_setting_id) REFERENCES status_setting (id)');
        $this->addSql('CREATE INDEX IDX_6C7681F4EF6659F4 ON sav (status_setting_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sav DROP FOREIGN KEY FK_6C7681F4EF6659F4');
        $this->addSql('DROP INDEX IDX_6C7681F4EF6659F4 ON sav');
        $this->addSql('ALTER TABLE sav DROP status_setting_id');
    }
}
