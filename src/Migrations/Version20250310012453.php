<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250310012453 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Convertit la colonne author (string) en relation author_id (clé étrangère vers users)';
    }

    public function up(Schema $schema): void
    {
        // 1. Ajouter la colonne author_id (nullable initialement)
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD author_id INT DEFAULT NULL
        SQL);

        // 2. Mettre à jour tous les articles pour définir author_id=1
        $this->addSql(<<<'SQL'
            UPDATE article SET author_id = 1
        SQL);

        // 3. Rendre author_id NOT NULL
        $this->addSql(<<<'SQL'
            ALTER TABLE article MODIFY author_id INT NOT NULL
        SQL);

        // 4. Ajouter la contrainte de clé étrangère
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD CONSTRAINT FK_23A0E66F675F31B FOREIGN KEY (author_id) REFERENCES users (id)
        SQL);

        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_23A0E66F675F31B ON article (author_id)
        SQL);

        // 5. Supprimer l'ancienne colonne author
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP author
        SQL);
    }

    public function down(Schema $schema): void
    {
        // Supprimer la contrainte de clé étrangère
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP FOREIGN KEY FK_23A0E66F675F31B
        SQL);

        // Supprimer l'index
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_23A0E66F675F31B ON article
        SQL);

        // Ajouter la colonne author
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD author VARCHAR(255) DEFAULT 'Unknown' NOT NULL
        SQL);

        // Mise à jour des auteurs (si possible, récupérer le nom d'utilisateur)
        $this->addSql(<<<'SQL'
            UPDATE article a JOIN users u ON a.author_id = u.id SET a.author = u.username
        SQL);
        
        // Supprimer la colonne author_id
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP author_id
        SQL);
    }
}
