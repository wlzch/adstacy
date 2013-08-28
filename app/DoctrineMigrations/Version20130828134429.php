<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130828134429 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE followed_walls DROP CONSTRAINT fk_dc54807fc33923f1");
        $this->addSql("ALTER TABLE ad DROP CONSTRAINT fk_e264c9fac33923f1");
        $this->addSql("DROP SEQUENCE wall_id_seq CASCADE");
        $this->addSql("DROP TABLE wall");
        $this->addSql("DROP TABLE followed_walls");
        $this->addSql("ALTER TABLE ad DROP wall_id");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE wall_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE wall (id INT NOT NULL, user_id INT NOT NULL, name VARCHAR(50) NOT NULL, description VARCHAR(255) DEFAULT NULL, tags TEXT DEFAULT NULL, ads_count INT NOT NULL, followers_count INT NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX idx_b3c740c8a76ed395 ON wall (user_id)");
        $this->addSql("COMMENT ON COLUMN wall.tags IS '(DC2Type:simple_array)'");
        $this->addSql("CREATE TABLE followed_walls (wall_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(wall_id, user_id))");
        $this->addSql("CREATE INDEX idx_dc54807fc33923f1 ON followed_walls (wall_id)");
        $this->addSql("CREATE INDEX idx_dc54807fa76ed395 ON followed_walls (user_id)");
        $this->addSql("ALTER TABLE wall ADD CONSTRAINT fk_b3c740c8a76ed395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE followed_walls ADD CONSTRAINT fk_dc54807fc33923f1 FOREIGN KEY (wall_id) REFERENCES wall (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE followed_walls ADD CONSTRAINT fk_dc54807fa76ed395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE Ad ADD wall_id INT NOT NULL");
        $this->addSql("ALTER TABLE Ad ADD CONSTRAINT fk_e264c9fac33923f1 FOREIGN KEY (wall_id) REFERENCES wall (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
    }
}
