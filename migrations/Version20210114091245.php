<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210114091245 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE source_product_type (source_id INT NOT NULL, product_type_id INT NOT NULL, INDEX IDX_D7DCA930953C1C61 (source_id), INDEX IDX_D7DCA93014959723 (product_type_id), PRIMARY KEY(source_id, product_type_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE source_product_type ADD CONSTRAINT FK_D7DCA930953C1C61 FOREIGN KEY (source_id) REFERENCES source (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE source_product_type ADD CONSTRAINT FK_D7DCA93014959723 FOREIGN KEY (product_type_id) REFERENCES product_type (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE api_token');
        $this->addSql('ALTER TABLE sav_article ADD serial_number VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE source ADD dealer_id INT DEFAULT NULL, ADD image_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE source ADD CONSTRAINT FK_5F8A7F73249E6EA1 FOREIGN KEY (dealer_id) REFERENCES dealer (id)');
        $this->addSql('ALTER TABLE source ADD CONSTRAINT FK_5F8A7F733DA5256D FOREIGN KEY (image_id) REFERENCES files (id)');
        $this->addSql('CREATE INDEX IDX_5F8A7F73249E6EA1 ON source (dealer_id)');
        $this->addSql('CREATE INDEX IDX_5F8A7F733DA5256D ON source (image_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE api_token (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, token VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, expires_at DATETIME NOT NULL, INDEX IDX_7BA2F5EBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE api_token ADD CONSTRAINT FK_7BA2F5EBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE source_product_type');
        $this->addSql('ALTER TABLE sav_article DROP serial_number');
        $this->addSql('ALTER TABLE source DROP FOREIGN KEY FK_5F8A7F73249E6EA1');
        $this->addSql('ALTER TABLE source DROP FOREIGN KEY FK_5F8A7F733DA5256D');
        $this->addSql('DROP INDEX IDX_5F8A7F73249E6EA1 ON source');
        $this->addSql('DROP INDEX IDX_5F8A7F733DA5256D ON source');
        $this->addSql('ALTER TABLE source DROP dealer_id, DROP image_id');
    }
}
