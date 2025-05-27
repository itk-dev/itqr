<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250526070924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE qr_visual_config CHANGE label_margin_top label_margin_top INT NOT NULL, CHANGE label_margin_bottom label_margin_bottom INT NOT NULL, CHANGE logo logo VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE qr_visual_config CHANGE label_margin_top label_margin_top VARCHAR(5) NOT NULL, CHANGE label_margin_bottom label_margin_bottom VARCHAR(5) NOT NULL, CHANGE logo logo LONGBLOB DEFAULT NULL');
    }
}
