<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130821171701 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE TABLE promotes_post (user_id INT NOT NULL, post_id INT NOT NULL, PRIMARY KEY(user_id, post_id))");
        $this->addSql("CREATE INDEX IDX_A4B1EEEAA76ED395 ON promotes_post (user_id)");
        $this->addSql("CREATE INDEX IDX_A4B1EEEA4B89032C ON promotes_post (post_id)");
        $this->addSql("ALTER TABLE promotes_post ADD CONSTRAINT FK_A4B1EEEAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE promotes_post ADD CONSTRAINT FK_A4B1EEEA4B89032C FOREIGN KEY (post_id) REFERENCES Post (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE post ADD promotees_count INT NOT NULL");
        $this->addSql("ALTER TABLE users ADD interests TEXT NOT NULL");
        $this->addSql("ALTER TABLE users ADD promotes_count INT NOT NULL");
        $this->addSql("COMMENT ON COLUMN users.interests IS '(DC2Type:simple_array)'");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("DROP TABLE promotes_post");
        $this->addSql("ALTER TABLE Post DROP promotees_count");
        $this->addSql("ALTER TABLE users DROP interests");
        $this->addSql("ALTER TABLE users DROP promotes_count");
    }
}
