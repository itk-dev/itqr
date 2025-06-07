<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250607105337 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE url ADD tenant_id INT NOT NULL AFTER id
        SQL);
        $this->addSql(<<<'SQL'
            UPDATE url
            SET tenant_id = (
                SELECT qr.tenant_id 
                FROM qr 
                WHERE qr.id = url.qr_id
            )
        SQL);

        $this->addSql(<<<'SQL'
            ALTER TABLE url ADD CONSTRAINT FK_F47645AE9033212A FOREIGN KEY (tenant_id) REFERENCES tenant (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_F47645AE9033212A ON url (tenant_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE url DROP FOREIGN KEY FK_F47645AE9033212A
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_F47645AE9033212A ON url
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE url DROP tenant_id
        SQL);
    }
}
