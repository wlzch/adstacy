<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20131004143951 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("CREATE SEQUENCE report_ad_id_seq INCREMENT BY 1 MINVALUE 1 START 1");
        $this->addSql("CREATE TABLE report_ad (id INT NOT NULL, ad_id INT DEFAULT NULL, user_id INT DEFAULT NULL, reported_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))");
        $this->addSql("CREATE INDEX IDX_830B2EC84F34D596 ON report_ad (ad_id)");
        $this->addSql("CREATE INDEX IDX_830B2EC8A76ED395 ON report_ad (user_id)");
        $this->addSql("ALTER TABLE report_ad ADD CONSTRAINT FK_830B2EC84F34D596 FOREIGN KEY (ad_id) REFERENCES Ad (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE report_ad ADD CONSTRAINT FK_830B2EC8A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("DROP SEQUENCE report_ad_id_seq CASCADE");
        $this->addSql("DROP TABLE report_ad");
    }
}
