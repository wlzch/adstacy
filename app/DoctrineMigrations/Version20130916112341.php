<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20130916112341 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE promotes_ad DROP CONSTRAINT FK_3BE15F1B4F34D596");
        $this->addSql("ALTER TABLE promotes_ad DROP CONSTRAINT FK_3BE15F1BA76ED395");
        $this->addSql("ALTER TABLE promotes_ad ADD CONSTRAINT FK_3BE15F1B4F34D596 FOREIGN KEY (ad_id) REFERENCES Ad (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE promotes_ad ADD CONSTRAINT FK_3BE15F1BA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE ad_comment DROP CONSTRAINT FK_593EBBE84F34D596");
        $this->addSql("ALTER TABLE ad_comment ADD CONSTRAINT FK_593EBBE84F34D596 FOREIGN KEY (ad_id) REFERENCES Ad (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "postgresql", "Migration can only be executed safely on 'postgresql'.");
        
        $this->addSql("ALTER TABLE ad_comment DROP CONSTRAINT fk_593ebbe84f34d596");
        $this->addSql("ALTER TABLE ad_comment ADD CONSTRAINT fk_593ebbe84f34d596 FOREIGN KEY (ad_id) REFERENCES ad (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE promotes_ad DROP CONSTRAINT fk_3be15f1b4f34d596");
        $this->addSql("ALTER TABLE promotes_ad DROP CONSTRAINT fk_3be15f1ba76ed395");
        $this->addSql("ALTER TABLE promotes_ad ADD CONSTRAINT fk_3be15f1b4f34d596 FOREIGN KEY (ad_id) REFERENCES ad (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
        $this->addSql("ALTER TABLE promotes_ad ADD CONSTRAINT fk_3be15f1ba76ed395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE");
    }
}
