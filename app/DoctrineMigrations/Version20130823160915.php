<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130823160915 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE users ADD facebook_username VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE users ADD facebook_real_name VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE users ADD twitter_username VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE users ADD twitter_real_name VARCHAR(255) DEFAULT NULL");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_1483A5E93FFF95DF ON users (facebook_username)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_1483A5E9E69385EB ON users (twitter_username)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE users DROP facebook_username");
        $this->addSql("ALTER TABLE users DROP facebook_real_name");
        $this->addSql("ALTER TABLE users DROP twitter_username");
        $this->addSql("ALTER TABLE users DROP twitter_real_name");
        $this->addSql("DROP INDEX UNIQ_1483A5E93FFF95DF");
        $this->addSql("DROP INDEX UNIQ_1483A5E9E69385EB");
    }
}
