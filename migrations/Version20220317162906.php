<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220317162906 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, category_article_id INT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, image VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL, author VARCHAR(255) DEFAULT NULL, created_at DATE DEFAULT NULL, UNIQUE INDEX UNIQ_23A0E66989D9B62 (slug), INDEX IDX_23A0E66548AD6E2 (category_article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidat (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, tel INT NOT NULL, adresse VARCHAR(255) NOT NULL, date_naissance DATE NOT NULL, cv VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_article (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, image VARCHAR(255) NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_C5E24E18989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_service (id INT AUTO_INCREMENT NOT NULL, designation VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_2645DAAC989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE comments (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, article_id INT NOT NULL, content LONGTEXT NOT NULL, is_published TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, rgpd TINYINT(1) NOT NULL, INDEX IDX_5F9E962AA76ED395 (user_id), INDEX IDX_5F9E962A7294869C (article_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, company VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, msg LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE devis (id INT AUTO_INCREMENT NOT NULL, categories_id INT NOT NULL, services_id INT NOT NULL, lastname VARCHAR(255) NOT NULL, firstname VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, phone_number VARCHAR(255) NOT NULL, company VARCHAR(255) DEFAULT NULL, country VARCHAR(255) NOT NULL, subject LONGTEXT NOT NULL, created_at DATE NOT NULL, INDEX IDX_8B27C52BA21214B7 (categories_id), INDEX IDX_8B27C52BAEF5A6C1 (services_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, category_service_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_E01FBE6ACB42F998 (category_service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE niveau_scolaire (id INT AUTO_INCREMENT NOT NULL, niveau VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offre_emploi (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, nb_poste INT NOT NULL, nb_annee_experience INT NOT NULL, location VARCHAR(255) NOT NULL, file VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offre_emploi_candidat (offre_emploi_id INT NOT NULL, candidat_id INT NOT NULL, INDEX IDX_AF9BC490B08996ED (offre_emploi_id), INDEX IDX_AF9BC4908D0EB82 (candidat_id), PRIMARY KEY(offre_emploi_id, candidat_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE offre_emploi_niveau_scolaire (offre_emploi_id INT NOT NULL, niveau_scolaire_id INT NOT NULL, INDEX IDX_941F394BB08996ED (offre_emploi_id), INDEX IDX_941F394BC3584218 (niveau_scolaire_id), PRIMARY KEY(offre_emploi_id, niveau_scolaire_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, designation VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_E19D9AD2989D9B62 (slug), INDEX IDX_E19D9AD212469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, last_name VARCHAR(255) NOT NULL, first_name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, created_at DATE NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66548AD6E2 FOREIGN KEY (category_article_id) REFERENCES category_article (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A7294869C FOREIGN KEY (article_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52BA21214B7 FOREIGN KEY (categories_id) REFERENCES category_service (id)');
        $this->addSql('ALTER TABLE devis ADD CONSTRAINT FK_8B27C52BAEF5A6C1 FOREIGN KEY (services_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6ACB42F998 FOREIGN KEY (category_service_id) REFERENCES category_service (id)');
        $this->addSql('ALTER TABLE offre_emploi_candidat ADD CONSTRAINT FK_AF9BC490B08996ED FOREIGN KEY (offre_emploi_id) REFERENCES offre_emploi (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offre_emploi_candidat ADD CONSTRAINT FK_AF9BC4908D0EB82 FOREIGN KEY (candidat_id) REFERENCES candidat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offre_emploi_niveau_scolaire ADD CONSTRAINT FK_941F394BB08996ED FOREIGN KEY (offre_emploi_id) REFERENCES offre_emploi (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE offre_emploi_niveau_scolaire ADD CONSTRAINT FK_941F394BC3584218 FOREIGN KEY (niveau_scolaire_id) REFERENCES niveau_scolaire (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD212469DE2 FOREIGN KEY (category_id) REFERENCES category_service (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A7294869C');
        $this->addSql('ALTER TABLE offre_emploi_candidat DROP FOREIGN KEY FK_AF9BC4908D0EB82');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E66548AD6E2');
        $this->addSql('ALTER TABLE devis DROP FOREIGN KEY FK_8B27C52BA21214B7');
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6ACB42F998');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD212469DE2');
        $this->addSql('ALTER TABLE offre_emploi_niveau_scolaire DROP FOREIGN KEY FK_941F394BC3584218');
        $this->addSql('ALTER TABLE offre_emploi_candidat DROP FOREIGN KEY FK_AF9BC490B08996ED');
        $this->addSql('ALTER TABLE offre_emploi_niveau_scolaire DROP FOREIGN KEY FK_941F394BB08996ED');
        $this->addSql('ALTER TABLE devis DROP FOREIGN KEY FK_8B27C52BAEF5A6C1');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962AA76ED395');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE candidat');
        $this->addSql('DROP TABLE category_article');
        $this->addSql('DROP TABLE category_service');
        $this->addSql('DROP TABLE comments');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE devis');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE niveau_scolaire');
        $this->addSql('DROP TABLE offre_emploi');
        $this->addSql('DROP TABLE offre_emploi_candidat');
        $this->addSql('DROP TABLE offre_emploi_niveau_scolaire');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE user');
    }
}
