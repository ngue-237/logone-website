<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220319234210 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE candidature (id INT AUTO_INCREMENT NOT NULL, status TINYINT(1) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, cv VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offre_emploi (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(32) NOT NULL, nombre_poste SMALLINT NOT NULL, description VARCHAR(255) DEFAULT NULL, date_debut DATE DEFAULT NULL, date_expiration DATE DEFAULT NULL, max_salary SMALLINT DEFAULT NULL, min_salary SMALLINT DEFAULT NULL, location VARCHAR(32) DEFAULT NULL, niveau_scolaire VARCHAR(125) DEFAULT NULL, file VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offre_emploi_candidature (offre_emploi_id INT NOT NULL, candidature_id INT NOT NULL, INDEX IDX_B8F21DD3B08996ED (offre_emploi_id), INDEX IDX_B8F21DD3B6121583 (candidature_id), PRIMARY KEY(offre_emploi_id, candidature_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE offre_emploi_candidature ADD CONSTRAINT FK_B8F21DD3B08996ED FOREIGN KEY (offre_emploi_id) REFERENCES offre_emploi (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offre_emploi_candidature ADD CONSTRAINT FK_B8F21DD3B6121583 FOREIGN KEY (candidature_id) REFERENCES candidature (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE offre_emploi_candidature DROP FOREIGN KEY FK_B8F21DD3B6121583');
        $this->addSql('ALTER TABLE offre_emploi_candidature DROP FOREIGN KEY FK_B8F21DD3B08996ED');
        $this->addSql('DROP TABLE candidature');
        $this->addSql('DROP TABLE offre_emploi');
        $this->addSql('DROP TABLE offre_emploi_candidature');
        $this->addSql('ALTER TABLE contact CHANGE last_name last_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE first_name first_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE company company VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE phone_number phone_number VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE country country VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE msg msg LONGTEXT DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE last_name last_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE first_name first_name VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE email email VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
