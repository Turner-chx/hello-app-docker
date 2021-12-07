<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190325125336 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE messaging ADD sav_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE messaging ADD CONSTRAINT FK_EE15BA614F726353 FOREIGN KEY (sav_id) REFERENCES sav (id)');
        $this->addSql('CREATE INDEX IDX_EE15BA614F726353 ON messaging (sav_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE messaging DROP FOREIGN KEY FK_EE15BA614F726353');
        $this->addSql('DROP INDEX IDX_EE15BA614F726353 ON messaging');
        $this->addSql('ALTER TABLE messaging DROP sav_id');
    }
}
