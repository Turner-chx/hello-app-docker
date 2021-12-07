<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190416095555 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE supplier_arrival');
        $this->addSql('ALTER TABLE arrival ADD supplier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE arrival ADD CONSTRAINT FK_5BE55CB42ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('CREATE INDEX IDX_5BE55CB42ADD6D8C ON arrival (supplier_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE supplier_arrival (supplier_id INT NOT NULL, arrival_id INT NOT NULL, INDEX IDX_DA8028AB2ADD6D8C (supplier_id), INDEX IDX_DA8028AB62789708 (arrival_id), PRIMARY KEY(supplier_id, arrival_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE supplier_arrival ADD CONSTRAINT FK_DA8028AB2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE supplier_arrival ADD CONSTRAINT FK_DA8028AB62789708 FOREIGN KEY (arrival_id) REFERENCES arrival (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE arrival DROP FOREIGN KEY FK_5BE55CB42ADD6D8C');
        $this->addSql('DROP INDEX IDX_5BE55CB42ADD6D8C ON arrival');
        $this->addSql('ALTER TABLE arrival DROP supplier_id');
    }
}
