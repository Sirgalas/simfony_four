<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210525052544 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_networks (id UUID NOT NULL, user_id UUID NOT NULL, network VARCHAR(32) DEFAULT NULL, identity VARCHAR(32) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3934502BA76ED395 ON user_networks (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3934502B608487BC6A95E9C4 ON user_networks (network, identity)');
        $this->addSql('CREATE TABLE
                            user_users (
                                id UUID NOT NULL, 
                                email VARCHAR(255) DEFAULT NULL, 
                                password_hash VARCHAR(255) DEFAULT NULL, 
                                confirm_token VARCHAR(255) DEFAULT NULL, 
                                status VARCHAR(16) NOT NULL, 
                                role VARCHAR(16) NOT NULL, 
                                created_at DATE NOT NULL, 
                                reset_token_token VARCHAR(255) DEFAULT NULL, 
                                reset_token_expires DATE DEFAULT NULL, 
                                PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F6415EB1E7927C74 ON user_users (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F6415EB186EC69F0 ON user_users (reset_token_token)');
        $this->addSql('COMMENT ON COLUMN user_users.created_at IS \'(DC2Type:date_immutable)\'');
        $this->addSql('COMMENT ON COLUMN user_users.reset_token_expires IS \'(DC2Type:date_immutable)\'');
        $this->addSql('ALTER TABLE user_networks ADD CONSTRAINT FK_3934502BA76ED395 FOREIGN KEY (user_id) REFERENCES user_users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_networks DROP CONSTRAINT FK_3934502BA76ED395');
        $this->addSql('DROP TABLE user_networks');
        $this->addSql('DROP TABLE user_users');
    }
}
