<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131010221432 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("DROP SEQUENCE image_id_seq CASCADE");
        $this->addSql("CREATE SEQUENCE ad_image_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE ad_image (id INT NOT NULL, ad_id INT DEFAULT NULL, imagename VARCHAR(255) NOT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_F85D5EDA4F34D596 ON ad_image (ad_id)");
        $this->addSql("ALTER TABLE ad_image ADD CONSTRAINT FK_F85D5EDA4F34D596 FOREIGN KEY (ad_id) REFERENCES Ad (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("DROP TABLE image");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("DROP SEQUENCE ad_image_id_seq CASCADE");
        $this->addSql("CREATE SEQUENCE image_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE image (id INT NOT NULL, ad_id INT DEFAULT NULL, imagename VARCHAR(255) NOT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX idx_c53d045f4f34d596 ON image (ad_id)");
        $this->addSql("ALTER TABLE image ADD CONSTRAINT fk_c53d045f4f34d596 FOREIGN KEY (ad_id) REFERENCES ad (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("DROP TABLE ad_image");
    }
}
