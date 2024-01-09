<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230823102400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création de la table user_export';
    }

    // Complémentaire avoir le fichier : Version20230823103109.php
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_export (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, user_client_id INT NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, type VARCHAR(10) NOT NULL, begin_date DATETIME NOT NULL, groupes LONGTEXT NOT NULL, formations LONGTEXT DEFAULT NULL, graphics LONGTEXT DEFAULT NULL, is_enabled TINYINT(1) NOT NULL, INDEX IDX_27DE0DA9A76ED395 (user_id), INDEX IDX_27DE0DA9190BE4C5 (user_client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_export ADD CONSTRAINT FK_27DE0DA9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_export ADD CONSTRAINT FK_27DE0DA9190BE4C5 FOREIGN KEY (user_client_id) REFERENCES user_client (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_export');
    }
}
