<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130912155110 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE Notification_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE Notification (id INT NOT NULL, from_id INT DEFAULT NULL, to_id INT DEFAULT NULL, comment INT DEFAULT NULL, ad INT DEFAULT NULL, type VARCHAR(50) NOT NULL, read BOOLEAN NOT NULL, created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_A765AD3278CED90B ON Notification (from_id)");
        $this->addSql("CREATE INDEX IDX_A765AD3230354A65 ON Notification (to_id)");
        $this->addSql("CREATE INDEX IDX_A765AD329474526C ON Notification (comment)");
        $this->addSql("CREATE INDEX IDX_A765AD3277E0ED58 ON Notification (ad)");
        $this->addSql("ALTER TABLE Notification ADD CONSTRAINT FK_A765AD3278CED90B FOREIGN KEY (from_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE Notification ADD CONSTRAINT FK_A765AD3230354A65 FOREIGN KEY (to_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE Notification ADD CONSTRAINT FK_A765AD329474526C FOREIGN KEY (comment) REFERENCES ad_comment (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE Notification ADD CONSTRAINT FK_A765AD3277E0ED58 FOREIGN KEY (ad) REFERENCES Ad (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("DROP SEQUENCE Notification_id_seq CASCADE");
        $this->addSql("DROP TABLE Notification");
    }
}
