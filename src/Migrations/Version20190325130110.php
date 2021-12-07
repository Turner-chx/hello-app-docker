<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190325130110 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE messaging_file (messaging_id INT NOT NULL, file_id INT NOT NULL, INDEX IDX_DD3709F15CA3C610 (messaging_id), INDEX IDX_DD3709F193CB796C (file_id), PRIMARY KEY(messaging_id, file_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE messaging_file ADD CONSTRAINT FK_DD3709F15CA3C610 FOREIGN KEY (messaging_id) REFERENCES messaging (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE messaging_file ADD CONSTRAINT FK_DD3709F193CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE messaging_file');
    }
}
