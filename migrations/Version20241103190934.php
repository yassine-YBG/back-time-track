<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241103190934 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE emploi_du_temps DROP FOREIGN KEY FK_F86B32C154E6585E');
        $this->addSql('DROP INDEX IDX_F86B32C154E6585E ON emploi_du_temps');
        $this->addSql('ALTER TABLE emploi_du_temps ADD enseignant_id INT NOT NULL, DROP enseignant_id_id');
        $this->addSql('ALTER TABLE emploi_du_temps ADD CONSTRAINT FK_F86B32C1E455FCC0 FOREIGN KEY (enseignant_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F86B32C1E455FCC0 ON emploi_du_temps (enseignant_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE emploi_du_temps DROP FOREIGN KEY FK_F86B32C1E455FCC0');
        $this->addSql('DROP INDEX IDX_F86B32C1E455FCC0 ON emploi_du_temps');
        $this->addSql('ALTER TABLE emploi_du_temps ADD enseignant_id_id INT DEFAULT NULL, DROP enseignant_id');
        $this->addSql('ALTER TABLE emploi_du_temps ADD CONSTRAINT FK_F86B32C154E6585E FOREIGN KEY (enseignant_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_F86B32C154E6585E ON emploi_du_temps (enseignant_id_id)');
    }
}
