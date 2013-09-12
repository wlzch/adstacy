<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130911225201 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE users ALTER real_name TYPE VARCHAR(100)");
        $this->addSql("ALTER TABLE users ALTER facebook_id TYPE VARCHAR(75)");
        $this->addSql("ALTER TABLE users ALTER facebook_username TYPE VARCHAR(75)");
        $this->addSql("ALTER TABLE users ALTER facebook_real_name TYPE VARCHAR(100)");
        $this->addSql("ALTER TABLE users ALTER twitter_id TYPE VARCHAR(75)");
        $this->addSql("ALTER TABLE users ALTER twitter_username TYPE VARCHAR(75)");
        $this->addSql("ALTER TABLE users ALTER twitter_real_name TYPE VARCHAR(100)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE users ALTER real_name TYPE VARCHAR(255)");
        $this->addSql("ALTER TABLE users ALTER facebook_id TYPE VARCHAR(255)");
        $this->addSql("ALTER TABLE users ALTER facebook_username TYPE VARCHAR(255)");
        $this->addSql("ALTER TABLE users ALTER facebook_real_name TYPE VARCHAR(255)");
        $this->addSql("ALTER TABLE users ALTER twitter_id TYPE VARCHAR(255)");
        $this->addSql("ALTER TABLE users ALTER twitter_username TYPE VARCHAR(255)");
        $this->addSql("ALTER TABLE users ALTER twitter_real_name TYPE VARCHAR(255)");
    }
}
