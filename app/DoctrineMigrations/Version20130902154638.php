<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130902154638 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE ad DROP CONSTRAINT fk_e264c9fa3da5256d");
        $this->addSql("ALTER TABLE users DROP CONSTRAINT fk_1483a5e93da5256d");
        $this->addSql("DROP SEQUENCE image_id_seq CASCADE");
        $this->addSql("DROP TABLE image");
        $this->addSql("ALTER TABLE ad ADD imagename VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE ad DROP image_id");
        $this->addSql("DROP INDEX uniq_cbdf752b4f34d596");
        $this->addSql("CREATE INDEX IDX_CBDF752B4F34D596 ON featured_ad (ad_id)");
        $this->addSql("ALTER TABLE users ADD imagename VARCHAR(255) DEFAULT NULL");
        $this->addSql("ALTER TABLE users DROP image_id");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE image_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE image (id INT NOT NULL, filename VARCHAR(255) NOT NULL, PRIMARY KEY(id))");
        $this->addSql("ALTER TABLE Ad ADD image_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE Ad DROP imagename");
        $this->addSql("ALTER TABLE Ad ADD CONSTRAINT fk_e264c9fa3da5256d FOREIGN KEY (image_id) REFERENCES image (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("DROP INDEX IDX_CBDF752B4F34D596");
        $this->addSql("CREATE UNIQUE INDEX uniq_cbdf752b4f34d596 ON featured_ad (ad_id)");
        $this->addSql("ALTER TABLE users ADD image_id INT DEFAULT NULL");
        $this->addSql("ALTER TABLE users DROP imagename");
        $this->addSql("ALTER TABLE users ADD CONSTRAINT fk_1483a5e93da5256d FOREIGN KEY (image_id) REFERENCES image (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
    }
}
