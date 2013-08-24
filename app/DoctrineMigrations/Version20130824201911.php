<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130824201911 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE users ADD image_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE users ADD CONSTRAINT FK_1483A5E93DA5256D FOREIGN KEY (image_id) REFERENCES Image (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_1483A5E93DA5256D ON users (image_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE users DROP image_id");
        $this->addSql("ALTER TABLE users DROP CONSTRAINT FK_1483A5E93DA5256D");
        $this->addSql("DROP INDEX UNIQ_1483A5E93DA5256D");
    }
}
