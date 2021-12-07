<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190329094933 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sub_product_type_nature_setting (sub_product_type_id INT NOT NULL, nature_setting_id INT NOT NULL, INDEX IDX_530A9F10E2EE70C (sub_product_type_id), INDEX IDX_530A9F1024B0EE5 (nature_setting_id), PRIMARY KEY(sub_product_type_id, nature_setting_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sub_product_type_nature_setting ADD CONSTRAINT FK_530A9F10E2EE70C FOREIGN KEY (sub_product_type_id) REFERENCES sub_product_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sub_product_type_nature_setting ADD CONSTRAINT FK_530A9F1024B0EE5 FOREIGN KEY (nature_setting_id) REFERENCES nature_setting (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sub_product_type_nature_setting');
    }
}
