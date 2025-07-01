<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250701111923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__link AS SELECT id, original_url, short_url, number_of_clicks, create_at, use_at, is_available FROM link');
        $this->addSql('DROP TABLE link');
        $this->addSql('CREATE TABLE link (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, original_url VARCHAR(255) NOT NULL, short_url VARCHAR(255) NOT NULL, number_of_clicks SMALLINT NOT NULL, create_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , use_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        , is_available BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO link (id, original_url, short_url, number_of_clicks, create_at, use_at, is_available) SELECT id, original_url, short_url, number_of_clicks, create_at, use_at, is_available FROM __temp__link');
        $this->addSql('DROP TABLE __temp__link');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__link AS SELECT id, original_url, short_url, number_of_clicks, create_at, use_at, is_available FROM link');
        $this->addSql('DROP TABLE link');
        $this->addSql('CREATE TABLE link (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, original_url VARCHAR(255) NOT NULL, short_url VARCHAR(255) NOT NULL, number_of_clicks SMALLINT NOT NULL, create_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , use_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , is_available BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO link (id, original_url, short_url, number_of_clicks, create_at, use_at, is_available) SELECT id, original_url, short_url, number_of_clicks, create_at, use_at, is_available FROM __temp__link');
        $this->addSql('DROP TABLE __temp__link');
    }
}
