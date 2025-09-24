<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250604120523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user_role_tenant DROP FOREIGN KEY FK_4C64EC469033212A
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_role_tenant DROP FOREIGN KEY FK_4C64EC46A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_role_tenant
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_4E59C4623A6F39CD ON tenant
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tenant DROP tenant_key, DROP fallback_image_url, CHANGE title name VARCHAR(255) DEFAULT '' NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX name_unique ON tenant (name)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD tenant_id INT NOT NULL, ADD roles JSON NOT NULL COMMENT '(DC2Type:json)', DROP password
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD CONSTRAINT FK_8D93D6499033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_8D93D6499033212A ON user (tenant_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user_role_tenant (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, tenant_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', modified_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', created_by VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT '' NOT NULL COLLATE `utf8mb3_unicode_ci`, modified_by VARCHAR(255) CHARACTER SET utf8mb3 DEFAULT '' NOT NULL COLLATE `utf8mb3_unicode_ci`, roles JSON NOT NULL COMMENT '(DC2Type:json)', INDEX IDX_4C64EC469033212A (tenant_id), UNIQUE INDEX user_tenant_unique (user_id, tenant_id), INDEX IDX_4C64EC46A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_role_tenant ADD CONSTRAINT FK_4C64EC469033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user_role_tenant ADD CONSTRAINT FK_4C64EC46A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP FOREIGN KEY FK_8D93D6499033212A
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_8D93D6499033212A ON user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user ADD password VARCHAR(255) NOT NULL, DROP tenant_id, DROP roles
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX name_unique ON tenant
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tenant ADD tenant_key VARCHAR(25) NOT NULL, ADD fallback_image_url VARCHAR(255) DEFAULT NULL, CHANGE name title VARCHAR(255) DEFAULT '' NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_4E59C4623A6F39CD ON tenant (tenant_key)
        SQL);
    }
}
