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
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE qr (id INT AUTO_INCREMENT NOT NULL, tenant_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', modified_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_by VARCHAR(255) DEFAULT \'\' NOT NULL, modified_by VARCHAR(255) DEFAULT \'\' NOT NULL, uuid BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', title VARCHAR(255) NOT NULL, department VARCHAR(255) NOT NULL, description VARCHAR(2500) DEFAULT NULL, mode VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_C9F64A58D17F50A6 (uuid), INDEX IDX_C9F64A589033212A (tenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE qr_visual_config (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, size INT NOT NULL, margin INT NOT NULL, background_color VARCHAR(10) NOT NULL, foreground_color VARCHAR(10) NOT NULL, label_text VARCHAR(20) DEFAULT NULL, label_size INT DEFAULT NULL, label_text_color VARCHAR(10) NOT NULL, label_margin_top VARCHAR(5) NOT NULL, label_margin_bottom VARCHAR(5) NOT NULL, logo LONGBLOB DEFAULT NULL, error_correction_level VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tenant (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', modified_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_by VARCHAR(255) DEFAULT \'\' NOT NULL, modified_by VARCHAR(255) DEFAULT \'\' NOT NULL, title VARCHAR(255) DEFAULT \'\' NOT NULL, description VARCHAR(255) DEFAULT \'\' NOT NULL, tenant_key VARCHAR(25) NOT NULL, fallback_image_url VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_4E59C4623A6F39CD (tenant_key), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE url (id INT AUTO_INCREMENT NOT NULL, qr_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', modified_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_by VARCHAR(255) DEFAULT \'\' NOT NULL, modified_by VARCHAR(255) DEFAULT \'\' NOT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_F47645AE5AA64A57 (qr_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', modified_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_by VARCHAR(255) DEFAULT \'\' NOT NULL, modified_by VARCHAR(255) DEFAULT \'\' NOT NULL, provider_id VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, full_name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, provider VARCHAR(255) NOT NULL, user_type VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649A53A8AA (provider_id), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_role_tenant (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, tenant_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', modified_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_by VARCHAR(255) DEFAULT \'\' NOT NULL, modified_by VARCHAR(255) DEFAULT \'\' NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_4C64EC46A76ED395 (user_id), INDEX IDX_4C64EC469033212A (tenant_id), UNIQUE INDEX user_tenant_unique (user_id, tenant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE qr ADD CONSTRAINT FK_C9F64A589033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
        $this->addSql('ALTER TABLE url ADD CONSTRAINT FK_F47645AE5AA64A57 FOREIGN KEY (qr_id) REFERENCES qr (id)');
        $this->addSql('ALTER TABLE user_role_tenant ADD CONSTRAINT FK_4C64EC46A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_role_tenant ADD CONSTRAINT FK_4C64EC469033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE qr DROP FOREIGN KEY FK_C9F64A589033212A');
        $this->addSql('ALTER TABLE url DROP FOREIGN KEY FK_F47645AE5AA64A57');
        $this->addSql('ALTER TABLE user_role_tenant DROP FOREIGN KEY FK_4C64EC46A76ED395');
        $this->addSql('ALTER TABLE user_role_tenant DROP FOREIGN KEY FK_4C64EC469033212A');
        $this->addSql('DROP TABLE qr');
        $this->addSql('DROP TABLE qr_visual_config');
        $this->addSql('DROP TABLE tenant');
        $this->addSql('DROP TABLE url');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_role_tenant');
    }
}
