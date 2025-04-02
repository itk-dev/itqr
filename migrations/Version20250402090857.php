<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250402090857 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE url DROP FOREIGN KEY FK_F47645AE9033212A');
        $this->addSql('DROP INDEX IDX_F47645AE9033212A ON url');
        $this->addSql('ALTER TABLE url DROP tenant_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE url ADD tenant_id INT NOT NULL');
        $this->addSql('ALTER TABLE url ADD CONSTRAINT FK_F47645AE9033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('CREATE INDEX IDX_F47645AE9033212A ON url (tenant_id)');
    }
}
