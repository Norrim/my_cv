<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Create table for Skill entity.
 */
final class Version20260101152700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create table skill (id, name, percentage, position)';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE skill (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, percentage INT NOT NULL, position INT NOT NULL, PRIMARY KEY(id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE skill');
    }
}
