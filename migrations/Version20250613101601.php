<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250613101601 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE qr_hit_tracker (id INT AUTO_INCREMENT NOT NULL, qr_id INT DEFAULT NULL, timestamp DATETIME NOT NULL, INDEX IDX_E53FF9E35AA64A57 (qr_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE qr_hit_tracker ADD CONSTRAINT FK_E53FF9E35AA64A57 FOREIGN KEY (qr_id) REFERENCES qr (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE qr_hit_tracker DROP FOREIGN KEY FK_E53FF9E35AA64A57');
        $this->addSql('DROP TABLE qr_hit_tracker');
    }
}
