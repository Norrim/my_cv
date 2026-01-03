<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260103175919 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE personal_info ALTER name DROP NOT NULL');
        $this->addSql('ALTER TABLE personal_info ALTER firstname DROP NOT NULL');
        $this->addSql('ALTER TABLE personal_info ALTER title DROP NOT NULL');
        $this->addSql('ALTER TABLE personal_info ALTER phone_number DROP NOT NULL');
        $this->addSql('ALTER TABLE personal_info ALTER localisation DROP NOT NULL');
        $this->addSql('ALTER TABLE personal_info ALTER about DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE personal_info ALTER name SET NOT NULL');
        $this->addSql('ALTER TABLE personal_info ALTER firstname SET NOT NULL');
        $this->addSql('ALTER TABLE personal_info ALTER title SET NOT NULL');
        $this->addSql('ALTER TABLE personal_info ALTER phone_number SET NOT NULL');
        $this->addSql('ALTER TABLE personal_info ALTER localisation SET NOT NULL');
        $this->addSql('ALTER TABLE personal_info ALTER about SET NOT NULL');
    }
}
