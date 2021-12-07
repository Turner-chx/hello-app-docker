<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190325104510 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sub_product_type_feature_sub_product_type (sub_product_type_id INT NOT NULL, feature_sub_product_type_id INT NOT NULL, INDEX IDX_682D038FE2EE70C (sub_product_type_id), INDEX IDX_682D038F9589E58D (feature_sub_product_type_id), PRIMARY KEY(sub_product_type_id, feature_sub_product_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sub_product_type_feature_sub_product_type ADD CONSTRAINT FK_682D038FE2EE70C FOREIGN KEY (sub_product_type_id) REFERENCES sub_product_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sub_product_type_feature_sub_product_type ADD CONSTRAINT FK_682D038F9589E58D FOREIGN KEY (feature_sub_product_type_id) REFERENCES feature_sub_product_type (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sub_product_type_feature_sub_product_type');
    }
}
