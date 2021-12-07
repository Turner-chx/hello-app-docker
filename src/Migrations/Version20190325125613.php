<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190325125613 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sav ADD customer_id INT DEFAULT NULL, ADD user_id INT DEFAULT NULL, ADD customer_divalto_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sav ADD CONSTRAINT FK_6C7681F49395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE sav ADD CONSTRAINT FK_6C7681F4A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sav ADD CONSTRAINT FK_6C7681F4F850521E FOREIGN KEY (customer_divalto_id) REFERENCES customer_divalto (id)');
        $this->addSql('CREATE INDEX IDX_6C7681F49395C3F3 ON sav (customer_id)');
        $this->addSql('CREATE INDEX IDX_6C7681F4A76ED395 ON sav (user_id)');
        $this->addSql('CREATE INDEX IDX_6C7681F4F850521E ON sav (customer_divalto_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sav DROP FOREIGN KEY FK_6C7681F49395C3F3');
        $this->addSql('ALTER TABLE sav DROP FOREIGN KEY FK_6C7681F4A76ED395');
        $this->addSql('ALTER TABLE sav DROP FOREIGN KEY FK_6C7681F4F850521E');
        $this->addSql('DROP INDEX IDX_6C7681F49395C3F3 ON sav');
        $this->addSql('DROP INDEX IDX_6C7681F4A76ED395 ON sav');
        $this->addSql('DROP INDEX IDX_6C7681F4F850521E ON sav');
        $this->addSql('ALTER TABLE sav DROP customer_id, DROP user_id, DROP customer_divalto_id');
    }
}
