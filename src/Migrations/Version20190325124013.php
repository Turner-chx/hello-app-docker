<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190325124013 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE production ADD supplier_id INT DEFAULT NULL, ADD user_id INT DEFAULT NULL, ADD arrival_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE production ADD CONSTRAINT FK_D3EDB1E02ADD6D8C FOREIGN KEY (supplier_id) REFERENCES supplier (id)');
        $this->addSql('ALTER TABLE production ADD CONSTRAINT FK_D3EDB1E0A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE production ADD CONSTRAINT FK_D3EDB1E062789708 FOREIGN KEY (arrival_id) REFERENCES arrival (id)');
        $this->addSql('CREATE INDEX IDX_D3EDB1E02ADD6D8C ON production (supplier_id)');
        $this->addSql('CREATE INDEX IDX_D3EDB1E0A76ED395 ON production (user_id)');
        $this->addSql('CREATE INDEX IDX_D3EDB1E062789708 ON production (arrival_id)');
        $this->addSql('ALTER TABLE production_history ADD production_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE production_history ADD CONSTRAINT FK_32AFFD96ECC6147F FOREIGN KEY (production_id) REFERENCES production (id)');
        $this->addSql('CREATE INDEX IDX_32AFFD96ECC6147F ON production_history (production_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE production DROP FOREIGN KEY FK_D3EDB1E02ADD6D8C');
        $this->addSql('ALTER TABLE production DROP FOREIGN KEY FK_D3EDB1E0A76ED395');
        $this->addSql('ALTER TABLE production DROP FOREIGN KEY FK_D3EDB1E062789708');
        $this->addSql('DROP INDEX IDX_D3EDB1E02ADD6D8C ON production');
        $this->addSql('DROP INDEX IDX_D3EDB1E0A76ED395 ON production');
        $this->addSql('DROP INDEX IDX_D3EDB1E062789708 ON production');
        $this->addSql('ALTER TABLE production DROP supplier_id, DROP user_id, DROP arrival_id');
        $this->addSql('ALTER TABLE production_history DROP FOREIGN KEY FK_32AFFD96ECC6147F');
        $this->addSql('DROP INDEX IDX_32AFFD96ECC6147F ON production_history');
        $this->addSql('ALTER TABLE production_history DROP production_id');
    }
}
