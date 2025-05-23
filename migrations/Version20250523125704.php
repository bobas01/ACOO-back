<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250523125704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE `admin` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE contact_club (id INT AUTO_INCREMENT NOT NULL, mail VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, phone_number VARCHAR(12) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE events (id INT AUTO_INCREMENT NOT NULL, id_sport_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, event_type VARCHAR(255) NOT NULL, location VARCHAR(255) NOT NULL, is_cancelled TINYINT(1) NOT NULL, start_datetime DATETIME NOT NULL, end_datetime DATETIME DEFAULT NULL, INDEX IDX_5387574AFCA3506D (id_sport_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE events_teams (events_id INT NOT NULL, teams_id INT NOT NULL, INDEX IDX_267EEA7E9D6A1065 (events_id), INDEX IDX_267EEA7ED6365F12 (teams_id), PRIMARY KEY(events_id, teams_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE gallery (id INT AUTO_INCREMENT NOT NULL, theme VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, introduction_id INT DEFAULT NULL, social_medias_id INT DEFAULT NULL, prize_list_id INT DEFAULT NULL, sports_id INT DEFAULT NULL, events_id INT DEFAULT NULL, pictures_id INT DEFAULT NULL, news_id INT DEFAULT NULL, partners_id INT DEFAULT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_E01FBE6A87D2B3A9 (introduction_id), INDEX IDX_E01FBE6AF5A2FA3C (social_medias_id), INDEX IDX_E01FBE6A8D3374F5 (prize_list_id), INDEX IDX_E01FBE6A54BBBFB7 (sports_id), INDEX IDX_E01FBE6A9D6A1065 (events_id), INDEX IDX_E01FBE6ABC415685 (pictures_id), INDEX IDX_E01FBE6AB5A459A0 (news_id), INDEX IDX_E01FBE6ABDE7F1C6 (partners_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE introduction (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE news (id INT AUTO_INCREMENT NOT NULL, event_id INT DEFAULT NULL, id_admin_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, img_url VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_1DD3995071F7E88B (event_id), INDEX IDX_1DD3995034F06E85 (id_admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE partners (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE pictures (id INT AUTO_INCREMENT NOT NULL, id_gallery_id INT DEFAULT NULL, description LONGTEXT NOT NULL, INDEX IDX_8F7C2FC0A16D2F58 (id_gallery_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE prize_list (id INT AUTO_INCREMENT NOT NULL, athlete_name VARCHAR(255) NOT NULL, competition VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, sport VARCHAR(255) NOT NULL, gender VARCHAR(255) NOT NULL, result VARCHAR(255) NOT NULL, year INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE questions (id INT AUTO_INCREMENT NOT NULL, question LONGTEXT NOT NULL, answer LONGTEXT NOT NULL, category VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE recurring_schedule (id INT AUTO_INCREMENT NOT NULL, id_sport_id INT DEFAULT NULL, id_team_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, location VARCHAR(255) NOT NULL, start_time DATETIME NOT NULL, duration INT DEFAULT NULL, frequency VARCHAR(255) DEFAULT NULL, end_date DATETIME NOT NULL, day_of_week VARCHAR(255) NOT NULL, INDEX IDX_CE84DCEAFCA3506D (id_sport_id), INDEX IDX_CE84DCEAF7F171DE (id_team_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE schedule_exeption (id INT AUTO_INCREMENT NOT NULL, id_reccuring_schedule_id INT DEFAULT NULL, exeption_date DATETIME NOT NULL, start_time DATETIME NOT NULL, end_time DATETIME DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, is_cancelled TINYINT(1) DEFAULT NULL, reason LONGTEXT DEFAULT NULL, INDEX IDX_D49A84B9D4965674 (id_reccuring_schedule_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE social_medias (id INT AUTO_INCREMENT NOT NULL, platform VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, icon_url VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE sports (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, contact VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE teams (id INT AUTO_INCREMENT NOT NULL, id_sport_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_96C22258FCA3506D (id_sport_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE events ADD CONSTRAINT FK_5387574AFCA3506D FOREIGN KEY (id_sport_id) REFERENCES sports (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE events_teams ADD CONSTRAINT FK_267EEA7E9D6A1065 FOREIGN KEY (events_id) REFERENCES events (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE events_teams ADD CONSTRAINT FK_267EEA7ED6365F12 FOREIGN KEY (teams_id) REFERENCES teams (id) ON DELETE CASCADE
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
            ALTER TABLE news ADD CONSTRAINT FK_1DD3995071F7E88B FOREIGN KEY (event_id) REFERENCES events (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE news ADD CONSTRAINT FK_1DD3995034F06E85 FOREIGN KEY (id_admin_id) REFERENCES `admin` (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pictures ADD CONSTRAINT FK_8F7C2FC0A16D2F58 FOREIGN KEY (id_gallery_id) REFERENCES gallery (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recurring_schedule ADD CONSTRAINT FK_CE84DCEAFCA3506D FOREIGN KEY (id_sport_id) REFERENCES sports (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recurring_schedule ADD CONSTRAINT FK_CE84DCEAF7F171DE FOREIGN KEY (id_team_id) REFERENCES teams (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE schedule_exeption ADD CONSTRAINT FK_D49A84B9D4965674 FOREIGN KEY (id_reccuring_schedule_id) REFERENCES recurring_schedule (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE teams ADD CONSTRAINT FK_96C22258FCA3506D FOREIGN KEY (id_sport_id) REFERENCES sports (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE events DROP FOREIGN KEY FK_5387574AFCA3506D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE events_teams DROP FOREIGN KEY FK_267EEA7E9D6A1065
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE events_teams DROP FOREIGN KEY FK_267EEA7ED6365F12
        SQL);
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
            ALTER TABLE news DROP FOREIGN KEY FK_1DD3995071F7E88B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE news DROP FOREIGN KEY FK_1DD3995034F06E85
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE pictures DROP FOREIGN KEY FK_8F7C2FC0A16D2F58
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recurring_schedule DROP FOREIGN KEY FK_CE84DCEAFCA3506D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE recurring_schedule DROP FOREIGN KEY FK_CE84DCEAF7F171DE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE schedule_exeption DROP FOREIGN KEY FK_D49A84B9D4965674
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE teams DROP FOREIGN KEY FK_96C22258FCA3506D
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE `admin`
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE contact
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE contact_club
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE events
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE events_teams
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE gallery
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE images
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE introduction
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE news
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE partners
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE pictures
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE prize_list
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE questions
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE recurring_schedule
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE schedule_exeption
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE social_medias
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE sports
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE teams
        SQL);
    }
}
