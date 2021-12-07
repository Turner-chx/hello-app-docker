<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190403142220 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sav ADD dealer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sav ADD CONSTRAINT FK_6C7681F4249E6EA1 FOREIGN KEY (dealer_id) REFERENCES customer (id)');
        $this->addSql('CREATE INDEX IDX_6C7681F4249E6EA1 ON sav (dealer_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sav DROP FOREIGN KEY FK_6C7681F4249E6EA1');
        $this->addSql('DROP INDEX IDX_6C7681F4249E6EA1 ON sav');
        $this->addSql('ALTER TABLE sav DROP dealer_id');
    }
}
