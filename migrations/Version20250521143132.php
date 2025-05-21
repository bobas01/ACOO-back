<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250521143132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, introduction_id INT DEFAULT NULL, social_medias_id INT DEFAULT NULL, prize_list_id INT DEFAULT NULL, sports_id INT DEFAULT NULL, events_id INT DEFAULT NULL, pictures_id INT DEFAULT NULL, news_id INT DEFAULT NULL, partners_id INT DEFAULT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_E01FBE6A87D2B3A9 (introduction_id), INDEX IDX_E01FBE6AF5A2FA3C (social_medias_id), INDEX IDX_E01FBE6A8D3374F5 (prize_list_id), INDEX IDX_E01FBE6A54BBBFB7 (sports_id), INDEX IDX_E01FBE6A9D6A1065 (events_id), INDEX IDX_E01FBE6ABC415685 (pictures_id), INDEX IDX_E01FBE6AB5A459A0 (news_id), INDEX IDX_E01FBE6ABDE7F1C6 (partners_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A87D2B3A9 FOREIGN KEY (introduction_id) REFERENCES introduction (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE images ADD CONSTRAINT FK_E01FBE6AF5A2FA3C FOREIGN KEY (social_medias_id) REFERENCES social_medias (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A8D3374F5 FOREIGN KEY (prize_list_id) REFERENCES prize_list (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A54BBBFB7 FOREIGN KEY (sports_id) REFERENCES sports (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE images ADD CONSTRAINT FK_E01FBE6A9D6A1065 FOREIGN KEY (events_id) REFERENCES events (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE images ADD CONSTRAINT FK_E01FBE6ABC415685 FOREIGN KEY (pictures_id) REFERENCES pictures (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE images ADD CONSTRAINT FK_E01FBE6AB5A459A0 FOREIGN KEY (news_id) REFERENCES news (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE images ADD CONSTRAINT FK_E01FBE6ABDE7F1C6 FOREIGN KEY (partners_id) REFERENCES partners (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE introduction DROP image_url
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE partners DROP image_url
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pictures DROP image_url
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sports DROP image_url
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A87D2B3A9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6AF5A2FA3C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A8D3374F5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A54BBBFB7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6A9D6A1065
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6ABC415685
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6AB5A459A0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6ABDE7F1C6
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE images
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE sports ADD image_url VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE partners ADD image_url VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE introduction ADD image_url VARCHAR(255) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pictures ADD image_url VARCHAR(255) NOT NULL
        SQL);
    }
}
