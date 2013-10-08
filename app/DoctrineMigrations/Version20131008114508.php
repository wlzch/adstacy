<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131008114508 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE TABLE user_detail (user_id INT NOT NULL, facebook_friends TEXT NOT NULL, twitter_friends TEXT NOT NULL, PRIMARY KEY(user_id))");
        $this->addSql("COMMENT ON COLUMN user_detail.facebook_friends IS '(DC2Type:simple_array)'");
        $this->addSql("COMMENT ON COLUMN user_detail.twitter_friends IS '(DC2Type:simple_array)'");
        $this->addSql("ALTER TABLE user_detail ADD CONSTRAINT FK_4B5464AEA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("DROP TABLE user_detail");
    }
}
