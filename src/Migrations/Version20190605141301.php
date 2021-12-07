<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190605141301 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE production_feature_sub_product_type (production_id INT NOT NULL, feature_sub_product_type_id INT NOT NULL, INDEX IDX_87141211ECC6147F (production_id), INDEX IDX_871412119589E58D (feature_sub_product_type_id), PRIMARY KEY(production_id, feature_sub_product_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE production_feature_sub_product_type ADD CONSTRAINT FK_87141211ECC6147F FOREIGN KEY (production_id) REFERENCES production (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE production_feature_sub_product_type ADD CONSTRAINT FK_871412119589E58D FOREIGN KEY (feature_sub_product_type_id) REFERENCES feature_sub_product_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customer CHANGE country country VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE production_feature_sub_product_type');
        $this->addSql('ALTER TABLE customer CHANGE country country VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
