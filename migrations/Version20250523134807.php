<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250523134807 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create qr_visual_config table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE qr_visual_config (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, size INT NOT NULL, margin INT NOT NULL, background_color VARCHAR(10) NOT NULL, foreground_color VARCHAR(10) NOT NULL, label_text VARCHAR(20) DEFAULT NULL, label_size INT DEFAULT NULL, label_text_color VARCHAR(10) NOT NULL, label_margin_top VARCHAR(5) NOT NULL, label_margin_bottom VARCHAR(5) NOT NULL, logo LONGBLOB DEFAULT NULL, error_correction_level VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE qr_visual_config');
    }
}
