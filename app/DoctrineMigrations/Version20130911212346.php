<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130911212346 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE ad_comment ADD user_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE ad_comment ADD CONSTRAINT FK_593EBBE8A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_593EBBE8A76ED395 ON ad_comment (user_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE ad_comment DROP user_id");
        $this->addSql("ALTER TABLE ad_comment DROP CONSTRAINT FK_593EBBE8A76ED395");
        $this->addSql("DROP INDEX IDX_593EBBE8A76ED395");
    }
}
