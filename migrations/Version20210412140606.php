<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210412140606 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE professore_alumno (professore_id INT NOT NULL, alumno_id INT NOT NULL, INDEX IDX_A624124F64C7BEB9 (professore_id), INDEX IDX_A624124FFC28E5EE (alumno_id), PRIMARY KEY(professore_id, alumno_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE professore_alumno ADD CONSTRAINT FK_A624124F64C7BEB9 FOREIGN KEY (professore_id) REFERENCES professore (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE professore_alumno ADD CONSTRAINT FK_A624124FFC28E5EE FOREIGN KEY (alumno_id) REFERENCES alumno (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE professore_alumno');
    }
}
