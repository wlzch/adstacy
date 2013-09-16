<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130916110217 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE notification DROP CONSTRAINT FK_A765AD3278CED90B");
        $this->addSql("ALTER TABLE notification DROP CONSTRAINT FK_A765AD3230354A65");
        $this->addSql("ALTER TABLE notification DROP CONSTRAINT FK_A765AD329474526C");
        $this->addSql("ALTER TABLE notification DROP CONSTRAINT FK_A765AD3277E0ED58");
        $this->addSql("ALTER TABLE notification ADD CONSTRAINT FK_A765AD3278CED90B FOREIGN KEY (from_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE notification ADD CONSTRAINT FK_A765AD3230354A65 FOREIGN KEY (to_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE notification ADD CONSTRAINT FK_A765AD329474526C FOREIGN KEY (comment) REFERENCES ad_comment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE notification ADD CONSTRAINT FK_A765AD3277E0ED58 FOREIGN KEY (ad) REFERENCES Ad (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE Notification DROP CONSTRAINT fk_a765ad3278ced90b");
        $this->addSql("ALTER TABLE Notification DROP CONSTRAINT fk_a765ad3230354a65");
        $this->addSql("ALTER TABLE Notification DROP CONSTRAINT fk_a765ad329474526c");
        $this->addSql("ALTER TABLE Notification DROP CONSTRAINT fk_a765ad3277e0ed58");
        $this->addSql("ALTER TABLE Notification ADD CONSTRAINT fk_a765ad3278ced90b FOREIGN KEY (from_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE Notification ADD CONSTRAINT fk_a765ad3230354a65 FOREIGN KEY (to_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE Notification ADD CONSTRAINT fk_a765ad329474526c FOREIGN KEY (comment) REFERENCES ad_comment (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE Notification ADD CONSTRAINT fk_a765ad3277e0ed58 FOREIGN KEY (ad) REFERENCES ad (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
    }
}
