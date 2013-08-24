<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130824130958 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE promotes_post DROP CONSTRAINT fk_a4b1eeea4b89032c");
        $this->addSql("DROP SEQUENCE post_id_seq CASCADE");
        $this->addSql("CREATE SEQUENCE Ad_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE Ad (id INT NOT NULL, image_id INT DEFAULT NULL, wall_id INT NOT NULL, description VARCHAR(255) NOT NULL, content TEXT DEFAULT NULL, tags TEXT DEFAULT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, thumb_height SMALLINT DEFAULT NULL, promotees_count INT NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_E264C9FA3DA5256D ON Ad (image_id)");
        $this->addSql("CREATE INDEX IDX_E264C9FAC33923F1 ON Ad (wall_id)");
        $this->addSql("COMMENT ON COLUMN Ad.tags IS '(DC2Type:simple_array)'");
        $this->addSql("CREATE TABLE promotes_ad (ad_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(ad_id, user_id))");
        $this->addSql("CREATE INDEX IDX_3BE15F1B4F34D596 ON promotes_ad (ad_id)");
        $this->addSql("CREATE INDEX IDX_3BE15F1BA76ED395 ON promotes_ad (user_id)");
        $this->addSql("ALTER TABLE Ad ADD CONSTRAINT FK_E264C9FA3DA5256D FOREIGN KEY (image_id) REFERENCES Image (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE Ad ADD CONSTRAINT FK_E264C9FAC33923F1 FOREIGN KEY (wall_id) REFERENCES Wall (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE promotes_ad ADD CONSTRAINT FK_3BE15F1B4F34D596 FOREIGN KEY (ad_id) REFERENCES Ad (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE promotes_ad ADD CONSTRAINT FK_3BE15F1BA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("DROP TABLE promotes_post");
        $this->addSql("DROP TABLE post");
        $this->addSql("ALTER TABLE wall RENAME COLUMN posts_count TO ads_count");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE promotes_ad DROP CONSTRAINT FK_3BE15F1B4F34D596");
        $this->addSql("DROP SEQUENCE Ad_id_seq CASCADE");
        $this->addSql("CREATE SEQUENCE post_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE promotes_post (user_id INT NOT NULL, post_id INT NOT NULL, PRIMARY KEY(user_id, post_id))");
        $this->addSql("CREATE INDEX idx_a4b1eeeaa76ed395 ON promotes_post (user_id)");
        $this->addSql("CREATE INDEX idx_a4b1eeea4b89032c ON promotes_post (post_id)");
        $this->addSql("CREATE TABLE post (id INT NOT NULL, image_id INT DEFAULT NULL, wall_id INT NOT NULL, description VARCHAR(255) NOT NULL, content TEXT DEFAULT NULL, tags TEXT DEFAULT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, promotees_count INT NOT NULL, thumb_height SMALLINT DEFAULT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE UNIQUE INDEX uniq_fab8c3b33da5256d ON post (image_id)");
        $this->addSql("CREATE INDEX idx_fab8c3b3c33923f1 ON post (wall_id)");
        $this->addSql("COMMENT ON COLUMN post.tags IS '(DC2Type:simple_array)'");
        $this->addSql("ALTER TABLE promotes_post ADD CONSTRAINT fk_a4b1eeeaa76ed395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE promotes_post ADD CONSTRAINT fk_a4b1eeea4b89032c FOREIGN KEY (post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE post ADD CONSTRAINT fk_fab8c3b33da5256d FOREIGN KEY (image_id) REFERENCES image (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE post ADD CONSTRAINT fk_fab8c3b3c33923f1 FOREIGN KEY (wall_id) REFERENCES wall (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("DROP TABLE Ad");
        $this->addSql("DROP TABLE promotes_ad");
        $this->addSql("ALTER TABLE Wall RENAME COLUMN ads_count TO posts_count");
    }
}
