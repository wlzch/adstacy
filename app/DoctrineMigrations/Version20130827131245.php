<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130827131245 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE ad ADD user_id INT NOT NULL");
        $this->addSql("ALTER TABLE ad ADD CONSTRAINT FK_E264C9FAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_E264C9FAA76ED395 ON ad (user_id)");
        $this->addSql("ALTER TABLE users ALTER real_name SET NOT NULL");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE Ad DROP user_id");
        $this->addSql("ALTER TABLE Ad DROP CONSTRAINT FK_E264C9FAA76ED395");
        $this->addSql("DROP INDEX IDX_E264C9FAA76ED395");
        $this->addSql("ALTER TABLE users ALTER real_name DROP NOT NULL");
    }
}
