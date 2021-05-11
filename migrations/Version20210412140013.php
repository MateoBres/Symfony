<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210412140013 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE professore ADD created_by_id INT DEFAULT NULL, ADD updated_by_id INT DEFAULT NULL, ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE professore ADD CONSTRAINT FK_D6A0C44FB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE professore ADD CONSTRAINT FK_D6A0C44F896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D6A0C44FB03A8386 ON professore (created_by_id)');
        $this->addSql('CREATE INDEX IDX_D6A0C44F896DBBDE ON professore (updated_by_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE professore DROP FOREIGN KEY FK_D6A0C44FB03A8386');
        $this->addSql('ALTER TABLE professore DROP FOREIGN KEY FK_D6A0C44F896DBBDE');
        $this->addSql('DROP INDEX IDX_D6A0C44FB03A8386 ON professore');
        $this->addSql('DROP INDEX IDX_D6A0C44F896DBBDE ON professore');
        $this->addSql('ALTER TABLE professore DROP created_by_id, DROP updated_by_id, DROP created_at, DROP updated_at');
    }
}
