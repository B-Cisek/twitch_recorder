<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250621112924 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            CREATE TABLE channel_info (id UUID NOT NULL, channel_id VARCHAR(255) NOT NULL, login VARCHAR(255) NOT NULL, display_name VARCHAR(255) NOT NULL, channel_type VARCHAR(255) NOT NULL, broadcaster_type VARCHAR(255) NOT NULL, description TEXT NOT NULL, profile_image_url VARCHAR(255) NOT NULL, offline_image_url VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_57DD922972F5A1AA ON channel_info (channel_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_57DD9229AA08CB10 ON channel_info (login)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN channel_info.id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN channel_info.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE channel ADD channel_info_id UUID DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN channel.channel_info_id IS '(DC2Type:uuid)'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE channel ADD CONSTRAINT FK_A2F98E47EAC8FEF9 FOREIGN KEY (channel_info_id) REFERENCES channel_info (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_A2F98E47EAC8FEF9 ON channel (channel_info_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        $this->addSql(<<<'SQL'
            ALTER TABLE channel DROP CONSTRAINT FK_A2F98E47EAC8FEF9
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE channel_info
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_A2F98E47EAC8FEF9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE channel DROP channel_info_id
        SQL);
    }
}
