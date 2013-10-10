<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130928124924 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE temp_ad_image ADD user_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE temp_ad_image ADD CONSTRAINT FK_98551A3BA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_98551A3BA76ED395 ON temp_ad_image (user_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE temp_ad_image DROP user_id");
        $this->addSql("ALTER TABLE temp_ad_image DROP CONSTRAINT FK_98551A3BA76ED395");
        $this->addSql("DROP INDEX IDX_98551A3BA76ED395");
    }
}
