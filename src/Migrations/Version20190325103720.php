<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190325103720 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sub_product_type ADD product_type_id INT NOT NULL');
        $this->addSql('ALTER TABLE sub_product_type ADD CONSTRAINT FK_166432BE14959723 FOREIGN KEY (product_type_id) REFERENCES product_type (id)');
        $this->addSql('CREATE INDEX IDX_166432BE14959723 ON sub_product_type (product_type_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sub_product_type DROP FOREIGN KEY FK_166432BE14959723');
        $this->addSql('DROP INDEX IDX_166432BE14959723 ON sub_product_type');
        $this->addSql('ALTER TABLE sub_product_type DROP product_type_id');
    }
}
