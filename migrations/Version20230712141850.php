<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230712141850 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD contractor_id INT DEFAULT NULL, ADD installer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649B0265DC7 FOREIGN KEY (contractor_id) REFERENCES contractor (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D6495A384417 FOREIGN KEY (installer_id) REFERENCES installer (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649B0265DC7 ON user (contractor_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D6495A384417 ON user (installer_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D649B0265DC7');
        $this->addSql('ALTER TABLE `user` DROP FOREIGN KEY FK_8D93D6495A384417');
        $this->addSql('DROP INDEX UNIQ_8D93D649B0265DC7 ON `user`');
        $this->addSql('DROP INDEX UNIQ_8D93D6495A384417 ON `user`');
        $this->addSql('ALTER TABLE `user` DROP contractor_id, DROP installer_id');
    }
}
