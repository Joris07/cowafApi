<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240216201809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE animal (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, prenom VARCHAR(50) NOT NULL, age INT NOT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', image_name VARCHAR(255) DEFAULT NULL, INDEX IDX_6AAB231FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE animal_description_animal (animal_id INT NOT NULL, description_animal_id INT NOT NULL, INDEX IDX_8F39D96C8E962C16 (animal_id), INDEX IDX_8F39D96CB220FB25 (description_animal_id), PRIMARY KEY(animal_id, description_animal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, auteur_id INT NOT NULL, destinataire_id INT NOT NULL, contenu VARCHAR(255) DEFAULT NULL, note INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_8F91ABF060BB6FE6 (auteur_id), INDEX IDX_8F91ABF0A4F84F6E (destinataire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE description_animal (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE facture (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, montant INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_FE866410A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE marque_vehicule (id INT AUTO_INCREMENT NOT NULL, marque VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE modele_vehicule (id INT AUTO_INCREMENT NOT NULL, marque_id INT DEFAULT NULL, modele VARCHAR(255) NOT NULL, INDEX IDX_41F7C11F4827B9B2 (marque_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE signalement (id INT AUTO_INCREMENT NOT NULL, raison VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE signalement_user (signalement_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8522E79565C5E57E (signalement_id), INDEX IDX_8522E795A76ED395 (user_id), PRIMARY KEY(signalement_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trajet (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, lieu_depart VARCHAR(255) NOT NULL, lieu_destination VARCHAR(255) NOT NULL, date_heure_depart DATETIME NOT NULL, places_disponible INT NOT NULL, prix_par_personne INT NOT NULL, description VARCHAR(255) DEFAULT NULL, date_suppression DATETIME DEFAULT NULL, INDEX IDX_2B5BA98CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE trajet_animal (trajet_id INT NOT NULL, animal_id INT NOT NULL, INDEX IDX_DACDD2D9D12A823 (trajet_id), INDEX IDX_DACDD2D98E962C16 (animal_id), PRIMARY KEY(trajet_id, animal_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, telephone VARCHAR(50) DEFAULT NULL, note INT NOT NULL, is_association TINYINT(1) NOT NULL, image_name VARCHAR(255) DEFAULT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vehicule (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, modele_id INT DEFAULT NULL, immatriculation VARCHAR(9) NOT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_292FFF1DA76ED395 (user_id), INDEX IDX_292FFF1DAC14B70A (modele_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE animal_description_animal ADD CONSTRAINT FK_8F39D96C8E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE animal_description_animal ADD CONSTRAINT FK_8F39D96CB220FB25 FOREIGN KEY (description_animal_id) REFERENCES description_animal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF060BB6FE6 FOREIGN KEY (auteur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0A4F84F6E FOREIGN KEY (destinataire_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE866410A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE modele_vehicule ADD CONSTRAINT FK_41F7C11F4827B9B2 FOREIGN KEY (marque_id) REFERENCES marque_vehicule (id)');
        $this->addSql('ALTER TABLE signalement_user ADD CONSTRAINT FK_8522E79565C5E57E FOREIGN KEY (signalement_id) REFERENCES signalement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE signalement_user ADD CONSTRAINT FK_8522E795A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trajet ADD CONSTRAINT FK_2B5BA98CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE trajet_animal ADD CONSTRAINT FK_DACDD2D9D12A823 FOREIGN KEY (trajet_id) REFERENCES trajet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE trajet_animal ADD CONSTRAINT FK_DACDD2D98E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT FK_292FFF1DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE vehicule ADD CONSTRAINT FK_292FFF1DAC14B70A FOREIGN KEY (modele_id) REFERENCES modele_vehicule (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal DROP FOREIGN KEY FK_6AAB231FA76ED395');
        $this->addSql('ALTER TABLE animal_description_animal DROP FOREIGN KEY FK_8F39D96C8E962C16');
        $this->addSql('ALTER TABLE animal_description_animal DROP FOREIGN KEY FK_8F39D96CB220FB25');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF060BB6FE6');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0A4F84F6E');
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE866410A76ED395');
        $this->addSql('ALTER TABLE modele_vehicule DROP FOREIGN KEY FK_41F7C11F4827B9B2');
        $this->addSql('ALTER TABLE signalement_user DROP FOREIGN KEY FK_8522E79565C5E57E');
        $this->addSql('ALTER TABLE signalement_user DROP FOREIGN KEY FK_8522E795A76ED395');
        $this->addSql('ALTER TABLE trajet DROP FOREIGN KEY FK_2B5BA98CA76ED395');
        $this->addSql('ALTER TABLE trajet_animal DROP FOREIGN KEY FK_DACDD2D9D12A823');
        $this->addSql('ALTER TABLE trajet_animal DROP FOREIGN KEY FK_DACDD2D98E962C16');
        $this->addSql('ALTER TABLE vehicule DROP FOREIGN KEY FK_292FFF1DA76ED395');
        $this->addSql('ALTER TABLE vehicule DROP FOREIGN KEY FK_292FFF1DAC14B70A');
        $this->addSql('DROP TABLE animal');
        $this->addSql('DROP TABLE animal_description_animal');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE description_animal');
        $this->addSql('DROP TABLE facture');
        $this->addSql('DROP TABLE marque_vehicule');
        $this->addSql('DROP TABLE modele_vehicule');
        $this->addSql('DROP TABLE signalement');
        $this->addSql('DROP TABLE signalement_user');
        $this->addSql('DROP TABLE trajet');
        $this->addSql('DROP TABLE trajet_animal');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE vehicule');
    }
}
