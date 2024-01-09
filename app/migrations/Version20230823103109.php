<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230823103109 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Update de la table user_export';
    }

    //Complémentaire avec le fichier : Version20230823102400.php 
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_export DROP FOREIGN KEY FK_27DE0DA9A76ED395');
        $this->addSql('DROP INDEX IDX_27DE0DA9A76ED395 ON user_export');
        $this->addSql('ALTER TABLE user_export ADD formations_filter LONGTEXT DEFAULT NULL, DROP groupes, CHANGE user_id user_filter_id INT DEFAULT NULL, CHANGE begin_date begin_export_date DATETIME NOT NULL, CHANGE formations groups_filter LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE user_export ADD CONSTRAINT FK_27DE0DA9D3753FBA FOREIGN KEY (user_filter_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_27DE0DA9D3753FBA ON user_export (user_filter_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_export DROP FOREIGN KEY FK_27DE0DA9D3753FBA');
        $this->addSql('DROP INDEX IDX_27DE0DA9D3753FBA ON user_export');
        $this->addSql('ALTER TABLE user_export ADD groupes LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD formations LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP groups_filter, DROP formations_filter, CHANGE user_filter_id user_id INT DEFAULT NULL, CHANGE begin_export_date begin_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE user_export ADD CONSTRAINT FK_27DE0DA9A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_27DE0DA9A76ED395 ON user_export (user_id)');
    }
}
