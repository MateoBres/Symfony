<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210430072601 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE sports_player (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, updated_by_id INT DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, full_name_canonical VARCHAR(255) NOT NULL, height DOUBLE PRECISION DEFAULT NULL, weight DOUBLE PRECISION DEFAULT NULL, nationality VARCHAR(255) NOT NULL, type INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, discriminator VARCHAR(255) NOT NULL, speciality VARCHAR(255) DEFAULT NULL, personal_record DOUBLE PRECISION DEFAULT NULL, role VARCHAR(255) DEFAULT NULL, actual_team VARCHAR(255) DEFAULT NULL, goals INT DEFAULT NULL, INDEX IDX_6E46A655B03A8386 (created_by_id), INDEX IDX_6E46A655896DBBDE (updated_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sports_player ADD CONSTRAINT FK_6E46A655B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sports_player ADD CONSTRAINT FK_6E46A655896DBBDE FOREIGN KEY (updated_by_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE sports_player');
    }
}
