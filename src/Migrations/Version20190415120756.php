<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190415120756 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE production ADD serial_number_lmeco_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE production ADD CONSTRAINT FK_D3EDB1E0AF4115AF FOREIGN KEY (serial_number_lmeco_id) REFERENCES serial_number_lmeco (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D3EDB1E0AF4115AF ON production (serial_number_lmeco_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE production DROP FOREIGN KEY FK_D3EDB1E0AF4115AF');
        $this->addSql('DROP INDEX UNIQ_D3EDB1E0AF4115AF ON production');
        $this->addSql('ALTER TABLE production DROP serial_number_lmeco_id');
    }
}
