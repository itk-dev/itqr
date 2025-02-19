<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218134959 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE qr ADD qr_config_size VARCHAR(255) DEFAULT NULL, ADD qr_config_margin VARCHAR(255) DEFAULT NULL, ADD qr_config_code_background VARCHAR(255) DEFAULT NULL, ADD qr_config_code_color VARCHAR(255) DEFAULT NULL, ADD qr_config_text VARCHAR(255) DEFAULT NULL, ADD qr_config_text_color VARCHAR(255) DEFAULT NULL, ADD qr_config_text_margin_top VARCHAR(255) DEFAULT NULL, ADD qr_config_text_margin_bottom VARCHAR(255) DEFAULT NULL, ADD qr_config_error_correction_level VARCHAR(255) DEFAULT NULL, CHANGE created_at created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE url CHANGE qr_id qr_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE url CHANGE qr_id qr_id INT NOT NULL');
        $this->addSql('ALTER TABLE qr DROP qr_config_size, DROP qr_config_margin, DROP qr_config_code_background, DROP qr_config_code_color, DROP qr_config_text, DROP qr_config_text_color, DROP qr_config_text_margin_top, DROP qr_config_text_margin_bottom, DROP qr_config_error_correction_level, CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
    }
}
