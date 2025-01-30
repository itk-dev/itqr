<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250129105309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE qr (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, department VARCHAR(255) NOT NULL, description VARCHAR(2500) DEFAULT NULL, mode VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE url (id INT AUTO_INCREMENT NOT NULL, qr_id INT DEFAULT NULL, short_uri VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_F47645AE5AA64A57 (qr_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE url ADD CONSTRAINT FK_F47645AE5AA64A57 FOREIGN KEY (qr_id) REFERENCES qr (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE url DROP FOREIGN KEY FK_F47645AE5AA64A57');
        $this->addSql('DROP TABLE qr');
        $this->addSql('DROP TABLE url');
    }
}
