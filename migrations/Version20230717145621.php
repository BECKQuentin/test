<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230717145621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE installer ADD contractor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE installer ADD CONSTRAINT FK_AC75BF49B0265DC7 FOREIGN KEY (contractor_id) REFERENCES contractor (id)');
        $this->addSql('CREATE INDEX IDX_AC75BF49B0265DC7 ON installer (contractor_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE installer DROP FOREIGN KEY FK_AC75BF49B0265DC7');
        $this->addSql('DROP INDEX IDX_AC75BF49B0265DC7 ON installer');
        $this->addSql('ALTER TABLE installer DROP contractor_id');
    }
}
