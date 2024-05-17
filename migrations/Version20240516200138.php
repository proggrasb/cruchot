<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240516200138 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial migration';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE currency_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE rates_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE history_id_seq INCREMENT BY 1 MINVALUE 1 START 1');

        $this->addSql('CREATE TABLE currency(id INT NOT NULL, code VARCHAR(3) NOT NULL, scode VARCHAR(3) NOT NULL, name VARCHAR(128), PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE rates (id INT NOT NULL, rated_at timestamp without time zone, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE history (id INT NOT NULL, rate_id INT NOT NULL, currency_id INT NOT NULL, value_rate VARCHAR(32), nominal INT, unit_rate VARCHAR(32), PRIMARY KEY(id))');

        $this->addSql('CREATE INDEX IDX_HISTORY_RATE_ID ON history (rate_id)');
        $this->addSql('CREATE INDEX IDX_HISTORY_CURRENCY_ID ON history (currency_id)');

        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_HISTORY_RATE_ID FOREIGN KEY (rate_id) REFERENCES "rates" (id) ON DELETE CASCADE NOT DEFERRABLE');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_HISTORY_CURRENCY_ID FOREIGN KEY (currency_id) REFERENCES "currency" (id) ON DELETE CASCADE NOT DEFERRABLE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE history DROP CONSTRAINT FK_HISTORY_CURRENCY_ID');
        $this->addSql('ALTER TABLE history DROP CONSTRAINT FK_HISTORY_RATE_ID');

        $this->addSql('DROP INDEX IDX_HISTORY_CURRENCY_ID');
        $this->addSql('DROP INDEX IDX_HISTORY_RATE_ID');

        $this->addSql('DROP SEQUENCE history_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE rates_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE currency_id_seq CASCADE');

        $this->addSql('DROP TABLE history');
        $this->addSql('DROP TABLE rates');
        $this->addSql('DROP TABLE currency');
    }
}
