<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230829103919 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Changement du type d\'export pour les emails (A faire à la fin)';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_export ADD emails LONGTEXT NOT NULL, DROP email');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user_export ADD email VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP emails');
    }
}
