<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240217151356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE signalement_user DROP FOREIGN KEY FK_8522E79565C5E57E');
        $this->addSql('ALTER TABLE signalement_user DROP FOREIGN KEY FK_8522E795A76ED395');
        $this->addSql('DROP TABLE signalement_user');
        $this->addSql('ALTER TABLE signalement ADD auteur_id INT NOT NULL, ADD destinataire_id INT NOT NULL');
        $this->addSql('ALTER TABLE signalement ADD CONSTRAINT FK_F4B5511460BB6FE6 FOREIGN KEY (auteur_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE signalement ADD CONSTRAINT FK_F4B55114A4F84F6E FOREIGN KEY (destinataire_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F4B5511460BB6FE6 ON signalement (auteur_id)');
        $this->addSql('CREATE INDEX IDX_F4B55114A4F84F6E ON signalement (destinataire_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE signalement_user (signalement_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8522E79565C5E57E (signalement_id), INDEX IDX_8522E795A76ED395 (user_id), PRIMARY KEY(signalement_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE signalement_user ADD CONSTRAINT FK_8522E79565C5E57E FOREIGN KEY (signalement_id) REFERENCES signalement (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE signalement_user ADD CONSTRAINT FK_8522E795A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE signalement DROP FOREIGN KEY FK_F4B5511460BB6FE6');
        $this->addSql('ALTER TABLE signalement DROP FOREIGN KEY FK_F4B55114A4F84F6E');
        $this->addSql('DROP INDEX IDX_F4B5511460BB6FE6 ON signalement');
        $this->addSql('DROP INDEX IDX_F4B55114A4F84F6E ON signalement');
        $this->addSql('ALTER TABLE signalement DROP auteur_id, DROP destinataire_id');
    }
}
