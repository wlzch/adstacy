<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130821192637 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE post DROP CONSTRAINT FK_FAB8C3B3C33923F1");
        $this->addSql("ALTER TABLE post ADD CONSTRAINT FK_FAB8C3B3C33923F1 FOREIGN KEY (wall_id) REFERENCES Wall (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE Post DROP CONSTRAINT fk_fab8c3b3c33923f1");
        $this->addSql("ALTER TABLE Post ADD CONSTRAINT fk_fab8c3b3c33923f1 FOREIGN KEY (wall_id) REFERENCES wall (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
    }
}
