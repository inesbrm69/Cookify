<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250213155504 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, uploaded_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE quantity_food DROP INDEX UNIQ_935547D58E255BBD, ADD INDEX IDX_935547D58E255BBD (food_id_id)');
        $this->addSql('ALTER TABLE quantity_food CHANGE food_id_id food_id_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE image');
        $this->addSql('ALTER TABLE quantity_food DROP INDEX IDX_935547D58E255BBD, ADD UNIQUE INDEX UNIQ_935547D58E255BBD (food_id_id)');
        $this->addSql('ALTER TABLE quantity_food CHANGE food_id_id food_id_id INT NOT NULL');
    }
}
