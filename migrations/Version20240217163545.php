<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240217163545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE facture ADD trajet_id INT NOT NULL');
        $this->addSql('ALTER TABLE facture ADD CONSTRAINT FK_FE866410D12A823 FOREIGN KEY (trajet_id) REFERENCES trajet (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FE866410D12A823 ON facture (trajet_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE facture DROP FOREIGN KEY FK_FE866410D12A823');
        $this->addSql('DROP INDEX UNIQ_FE866410D12A823 ON facture');
        $this->addSql('ALTER TABLE facture DROP trajet_id');
    }
}
