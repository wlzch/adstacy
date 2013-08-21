<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130821202540 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE Post_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE Image_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE Tag_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE Wall_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE Post (id INT NOT NULL, image_id INT DEFAULT NULL, wall_id INT NOT NULL, description VARCHAR(255) NOT NULL, content TEXT DEFAULT NULL, tags TEXT DEFAULT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, promotees_count INT NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_FAB8C3B33DA5256D ON Post (image_id)");
        $this->addSql("CREATE INDEX IDX_FAB8C3B3C33923F1 ON Post (wall_id)");
        $this->addSql("COMMENT ON COLUMN Post.tags IS '(DC2Type:simple_array)'");
        $this->addSql("CREATE TABLE Image (id INT NOT NULL, filename VARCHAR(255) NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE TABLE Tag (id INT NOT NULL, name VARCHAR(25) NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE TABLE Wall (id INT NOT NULL, user_id INT NOT NULL, name VARCHAR(50) NOT NULL, posts_count INT NOT NULL, followers_count INT NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_B3C740C8A76ED395 ON Wall (user_id)");
        $this->addSql("CREATE TABLE users (id INT NOT NULL, username VARCHAR(100) NOT NULL, username_canonical VARCHAR(100) NOT NULL, email VARCHAR(255) DEFAULT NULL, email_canonical VARCHAR(255) DEFAULT NULL, followers_count INT NOT NULL, following_counts INT NOT NULL, interests TEXT DEFAULT NULL, promotes_count INT NOT NULL, profile_picture VARCHAR(255) DEFAULT NULL, facebook_id VARCHAR(255) DEFAULT NULL, facebook_access_token VARCHAR(255) DEFAULT NULL, twitter_id VARCHAR(255) DEFAULT NULL, twitter_access_token VARCHAR(255) DEFAULT NULL, enabled BOOLEAN NOT NULL, salt VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, last_login TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, locked BOOLEAN NOT NULL, expired BOOLEAN NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, roles TEXT NOT NULL, credentials_expired BOOLEAN NOT NULL, credentials_expire_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_1483A5E992FC23A8 ON users (username_canonical)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_1483A5E9A0D96FBF ON users (email_canonical)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_1483A5E99BE8FD98 ON users (facebook_id)");
        $this->addSql("CREATE UNIQUE INDEX UNIQ_1483A5E9C63E6FFF ON users (twitter_id)");
        $this->addSql("COMMENT ON COLUMN users.interests IS '(DC2Type:simple_array)'");
        $this->addSql("COMMENT ON COLUMN users.roles IS '(DC2Type:array)'");
        $this->addSql("CREATE TABLE followed_walls (user_id INT NOT NULL, wall_id INT NOT NULL, PRIMARY KEY(user_id, wall_id))");
        $this->addSql("CREATE INDEX IDX_DC54807FA76ED395 ON followed_walls (user_id)");
        $this->addSql("CREATE INDEX IDX_DC54807FC33923F1 ON followed_walls (wall_id)");
        $this->addSql("CREATE TABLE follow (user_1_id INT NOT NULL, user_2_id INT NOT NULL, PRIMARY KEY(user_1_id, user_2_id))");
        $this->addSql("CREATE INDEX IDX_683444708A521033 ON follow (user_1_id)");
        $this->addSql("CREATE INDEX IDX_6834447098E7BFDD ON follow (user_2_id)");
        $this->addSql("CREATE TABLE promotes_post (user_id INT NOT NULL, post_id INT NOT NULL, PRIMARY KEY(user_id, post_id))");
        $this->addSql("CREATE INDEX IDX_A4B1EEEAA76ED395 ON promotes_post (user_id)");
        $this->addSql("CREATE INDEX IDX_A4B1EEEA4B89032C ON promotes_post (post_id)");
        $this->addSql("ALTER TABLE Post ADD CONSTRAINT FK_FAB8C3B33DA5256D FOREIGN KEY (image_id) REFERENCES Image (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE Post ADD CONSTRAINT FK_FAB8C3B3C33923F1 FOREIGN KEY (wall_id) REFERENCES Wall (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE Wall ADD CONSTRAINT FK_B3C740C8A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE followed_walls ADD CONSTRAINT FK_DC54807FA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE followed_walls ADD CONSTRAINT FK_DC54807FC33923F1 FOREIGN KEY (wall_id) REFERENCES Wall (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE follow ADD CONSTRAINT FK_683444708A521033 FOREIGN KEY (user_1_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE follow ADD CONSTRAINT FK_6834447098E7BFDD FOREIGN KEY (user_2_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE promotes_post ADD CONSTRAINT FK_A4B1EEEAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE promotes_post ADD CONSTRAINT FK_A4B1EEEA4B89032C FOREIGN KEY (post_id) REFERENCES Post (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE promotes_post DROP CONSTRAINT FK_A4B1EEEA4B89032C");
        $this->addSql("ALTER TABLE Post DROP CONSTRAINT FK_FAB8C3B33DA5256D");
        $this->addSql("ALTER TABLE Post DROP CONSTRAINT FK_FAB8C3B3C33923F1");
        $this->addSql("ALTER TABLE followed_walls DROP CONSTRAINT FK_DC54807FC33923F1");
        $this->addSql("ALTER TABLE Wall DROP CONSTRAINT FK_B3C740C8A76ED395");
        $this->addSql("ALTER TABLE followed_walls DROP CONSTRAINT FK_DC54807FA76ED395");
        $this->addSql("ALTER TABLE follow DROP CONSTRAINT FK_683444708A521033");
        $this->addSql("ALTER TABLE follow DROP CONSTRAINT FK_6834447098E7BFDD");
        $this->addSql("ALTER TABLE promotes_post DROP CONSTRAINT FK_A4B1EEEAA76ED395");
        $this->addSql("DROP SEQUENCE Post_id_seq CASCADE");
        $this->addSql("DROP SEQUENCE Image_id_seq CASCADE");
        $this->addSql("DROP SEQUENCE Tag_id_seq CASCADE");
        $this->addSql("DROP SEQUENCE Wall_id_seq CASCADE");
        $this->addSql("DROP SEQUENCE users_id_seq CASCADE");
        $this->addSql("DROP TABLE Post");
        $this->addSql("DROP TABLE Image");
        $this->addSql("DROP TABLE Tag");
        $this->addSql("DROP TABLE Wall");
        $this->addSql("DROP TABLE users");
        $this->addSql("DROP TABLE followed_walls");
        $this->addSql("DROP TABLE follow");
        $this->addSql("DROP TABLE promotes_post");
    }
}
