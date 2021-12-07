<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190416071003 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE messaging ADD user_id INT DEFAULT NULL, ADD customer_id INT DEFAULT NULL, ADD dealer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE messaging ADD CONSTRAINT FK_EE15BA61A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE messaging ADD CONSTRAINT FK_EE15BA619395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE messaging ADD CONSTRAINT FK_EE15BA61249E6EA1 FOREIGN KEY (dealer_id) REFERENCES dealer (id)');
        $this->addSql('CREATE INDEX IDX_EE15BA61A76ED395 ON messaging (user_id)');
        $this->addSql('CREATE INDEX IDX_EE15BA619395C3F3 ON messaging (customer_id)');
        $this->addSql('CREATE INDEX IDX_EE15BA61249E6EA1 ON messaging (dealer_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE messaging DROP FOREIGN KEY FK_EE15BA61A76ED395');
        $this->addSql('ALTER TABLE messaging DROP FOREIGN KEY FK_EE15BA619395C3F3');
        $this->addSql('ALTER TABLE messaging DROP FOREIGN KEY FK_EE15BA61249E6EA1');
        $this->addSql('DROP INDEX IDX_EE15BA61A76ED395 ON messaging');
        $this->addSql('DROP INDEX IDX_EE15BA619395C3F3 ON messaging');
        $this->addSql('DROP INDEX IDX_EE15BA61249E6EA1 ON messaging');
        $this->addSql('ALTER TABLE messaging DROP user_id, DROP customer_id, DROP dealer_id');
    }
}
