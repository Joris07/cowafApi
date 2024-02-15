<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240215205422 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE animal_description_animal (animal_id INT NOT NULL, description_animal_id INT NOT NULL, INDEX IDX_8F39D96C8E962C16 (animal_id), INDEX IDX_8F39D96CB220FB25 (description_animal_id), PRIMARY KEY(animal_id, description_animal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE description_animal (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE marque_vehicule (id INT AUTO_INCREMENT NOT NULL, marque VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE modele_vehicule (id INT AUTO_INCREMENT NOT NULL, marque_id INT DEFAULT NULL, modele VARCHAR(255) NOT NULL, INDEX IDX_41F7C11F4827B9B2 (marque_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE animal_description_animal ADD CONSTRAINT FK_8F39D96C8E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE animal_description_animal ADD CONSTRAINT FK_8F39D96CB220FB25 FOREIGN KEY (description_animal_id) REFERENCES description_animal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE modele_vehicule ADD CONSTRAINT FK_41F7C11F4827B9B2 FOREIGN KEY (marque_id) REFERENCES marque_vehicule (id)');
        $this->addSql('ALTER TABLE animal DROP description');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal_description_animal DROP FOREIGN KEY FK_8F39D96C8E962C16');
        $this->addSql('ALTER TABLE animal_description_animal DROP FOREIGN KEY FK_8F39D96CB220FB25');
        $this->addSql('ALTER TABLE modele_vehicule DROP FOREIGN KEY FK_41F7C11F4827B9B2');
        $this->addSql('DROP TABLE animal_description_animal');
        $this->addSql('DROP TABLE description_animal');
        $this->addSql('DROP TABLE marque_vehicule');
        $this->addSql('DROP TABLE modele_vehicule');
        $this->addSql('ALTER TABLE animal ADD description VARCHAR(255) DEFAULT NULL');
    }
}
