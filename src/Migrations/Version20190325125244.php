<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190325125244 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sav_history ADD sav_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sav_history ADD CONSTRAINT FK_870B1E504F726353 FOREIGN KEY (sav_id) REFERENCES sav (id)');
        $this->addSql('CREATE INDEX IDX_870B1E504F726353 ON sav_history (sav_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sav_history DROP FOREIGN KEY FK_870B1E504F726353');
        $this->addSql('DROP INDEX IDX_870B1E504F726353 ON sav_history');
        $this->addSql('ALTER TABLE sav_history DROP sav_id');
    }
}
