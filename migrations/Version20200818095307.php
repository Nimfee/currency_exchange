<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200818095307 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE currency (id INT AUTO_INCREMENT NOT NULL, iso_code VARCHAR(3) NOT NULL, name VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_6956883F62B6A45E (iso_code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE currency_exchange_rate (id INT AUTO_INCREMENT NOT NULL, currency_from_id INT NOT NULL, currency_to_id INT NOT NULL, rate DOUBLE PRECISION NOT NULL, rate_date DATE NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_B9F60EECA56723E4 (currency_from_id), INDEX IDX_B9F60EEC67D74803 (currency_to_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE source (id INT AUTO_INCREMENT NOT NULL, currency_id INT NOT NULL, url VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_5F8A7F7338248176 (currency_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE currency_exchange_rate ADD CONSTRAINT FK_B9F60EECA56723E4 FOREIGN KEY (currency_from_id) REFERENCES currency (id)');
        $this->addSql('ALTER TABLE currency_exchange_rate ADD CONSTRAINT FK_B9F60EEC67D74803 FOREIGN KEY (currency_to_id) REFERENCES currency (id)');
        $this->addSql('ALTER TABLE source ADD CONSTRAINT FK_5F8A7F7338248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');
        $this->addSql('CREATE INDEX currency_from_to_rate ON currency_exchange_rate (currency_from_id, currency_to_id, rate_date)');
        $this->addSql("INSERT INTO `currency` VALUES (1,'USD','U S D','2020-08-19 13:32:19','2020-08-19 13:32:19'),(2,'EUR','E U R','2020-08-19 13:32:35','2020-08-19 13:32:35')");
        $this->addSql("INSERT INTO `source` VALUES (1,2,'https://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml','ECB','2020-08-19 13:34:32','2020-08-19 13:34:32'),(2,1,'https://api.coindesk.com/v1/bpi/historical/close.json','USD','2020-08-19 13:35:01','2020-08-20 12:02:48')");
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE currency_exchange_rate DROP FOREIGN KEY FK_B9F60EECA56723E4');
        $this->addSql('ALTER TABLE currency_exchange_rate DROP FOREIGN KEY FK_B9F60EEC67D74803');
        $this->addSql('ALTER TABLE source DROP FOREIGN KEY FK_5F8A7F7338248176');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE currency_exchange_rate');
        $this->addSql('DROP TABLE source');
        $this->addSql('DROP INDEX currency_from_to_rate ON currency_exchange_rate');
    }
}
