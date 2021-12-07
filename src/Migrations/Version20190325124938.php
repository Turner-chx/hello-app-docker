<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190325124938 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sav_production ADD production_id INT DEFAULT NULL, ADD sav_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sav_production ADD CONSTRAINT FK_848638E2ECC6147F FOREIGN KEY (production_id) REFERENCES production (id)');
        $this->addSql('ALTER TABLE sav_production ADD CONSTRAINT FK_848638E24F726353 FOREIGN KEY (sav_id) REFERENCES sav (id)');
        $this->addSql('CREATE INDEX IDX_848638E2ECC6147F ON sav_production (production_id)');
        $this->addSql('CREATE INDEX IDX_848638E24F726353 ON sav_production (sav_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE sav_production DROP FOREIGN KEY FK_848638E2ECC6147F');
        $this->addSql('ALTER TABLE sav_production DROP FOREIGN KEY FK_848638E24F726353');
        $this->addSql('DROP INDEX IDX_848638E2ECC6147F ON sav_production');
        $this->addSql('DROP INDEX IDX_848638E24F726353 ON sav_production');
        $this->addSql('ALTER TABLE sav_production DROP production_id, DROP sav_id');
    }
}
