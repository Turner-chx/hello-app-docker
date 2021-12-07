<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190325124650 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE supplier_arrival MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE supplier_arrival DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE supplier_arrival ADD supplier_id INT NOT NULL, ADD arrival_id INT NOT NULL, DROP id');
        $this->addSql('ALTER TABLE supplier_arrival ADD CONSTRAINT FK_DA8028AB2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE supplier_arrival ADD CONSTRAINT FK_DA8028AB62789708 FOREIGN KEY (arrival_id) REFERENCES arrival (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_DA8028AB2ADD6D8C ON supplier_arrival (supplier_id)');
        $this->addSql('CREATE INDEX IDX_DA8028AB62789708 ON supplier_arrival (arrival_id)');
        $this->addSql('ALTER TABLE supplier_arrival ADD PRIMARY KEY (supplier_id, arrival_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE supplier_arrival DROP FOREIGN KEY FK_DA8028AB2ADD6D8C');
        $this->addSql('ALTER TABLE supplier_arrival DROP FOREIGN KEY FK_DA8028AB62789708');
        $this->addSql('DROP INDEX IDX_DA8028AB2ADD6D8C ON supplier_arrival');
        $this->addSql('DROP INDEX IDX_DA8028AB62789708 ON supplier_arrival');
        $this->addSql('ALTER TABLE supplier_arrival DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE supplier_arrival ADD id INT AUTO_INCREMENT NOT NULL, DROP supplier_id, DROP arrival_id');
        $this->addSql('ALTER TABLE supplier_arrival ADD PRIMARY KEY (id)');
    }
}
