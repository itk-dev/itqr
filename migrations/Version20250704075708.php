<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250704075708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE qr_visual_config ADD tenant_id INT NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD modified_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD created_by VARCHAR(255) DEFAULT \'\' NOT NULL, ADD modified_by VARCHAR(255) DEFAULT \'\' NOT NULL');
        $this->addSql('ALTER TABLE qr_visual_config ADD CONSTRAINT FK_E633C5F79033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('CREATE INDEX IDX_E633C5F79033212A ON qr_visual_config (tenant_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE qr_visual_config DROP FOREIGN KEY FK_E633C5F79033212A');
        $this->addSql('DROP INDEX IDX_E633C5F79033212A ON qr_visual_config');
        $this->addSql('ALTER TABLE qr_visual_config DROP tenant_id, DROP created_at, DROP modified_at, DROP created_by, DROP modified_by');
    }
}
