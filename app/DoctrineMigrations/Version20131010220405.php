<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131010220405 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE image RENAME COLUMN user_id TO ad_id");
        $this->addSql("ALTER TABLE image DROP CONSTRAINT fk_c53d045fa76ed395");
        $this->addSql("DROP INDEX idx_c53d045fa76ed395");
        $this->addSql("ALTER TABLE image ADD CONSTRAINT FK_C53D045F4F34D596 FOREIGN KEY (ad_id) REFERENCES Ad (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX IDX_C53D045F4F34D596 ON image (ad_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE image RENAME COLUMN ad_id TO user_id");
        $this->addSql("ALTER TABLE image DROP CONSTRAINT FK_C53D045F4F34D596");
        $this->addSql("DROP INDEX IDX_C53D045F4F34D596");
        $this->addSql("ALTER TABLE image ADD CONSTRAINT fk_c53d045fa76ed395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("CREATE INDEX idx_c53d045fa76ed395 ON image (user_id)");
    }
}
