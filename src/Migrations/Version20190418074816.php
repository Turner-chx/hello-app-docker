<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190418074816 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE production_article');
        $this->addSql('ALTER TABLE production ADD article_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE production ADD CONSTRAINT FK_D3EDB1E07294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('CREATE INDEX IDX_D3EDB1E07294869C ON production (article_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE production_article (production_id INT NOT NULL, article_id INT NOT NULL, INDEX IDX_172F83BBECC6147F (production_id), INDEX IDX_172F83BB7294869C (article_id), PRIMARY KEY(production_id, article_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE production_article ADD CONSTRAINT FK_172F83BB7294869C FOREIGN KEY (article_id) REFERENCES article (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE production_article ADD CONSTRAINT FK_172F83BBECC6147F FOREIGN KEY (production_id) REFERENCES production (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE production DROP FOREIGN KEY FK_D3EDB1E07294869C');
        $this->addSql('DROP INDEX IDX_D3EDB1E07294869C ON production');
        $this->addSql('ALTER TABLE production DROP article_id');
    }
}
