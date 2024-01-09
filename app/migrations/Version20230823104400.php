<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230823104400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de la période et de la fréquence pour les exports (Doit être fait après lajout de export_period)';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_export DROP FOREIGN KEY FK_27DE0DA9190BE4C5');
        $this->addSql('ALTER TABLE user_export ADD period_id INT NOT NULL, ADD frequency_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_export ADD CONSTRAINT FK_27DE0DA9EC8B7ADE FOREIGN KEY (period_id) REFERENCES export_period (id)');
        $this->addSql('ALTER TABLE user_export ADD CONSTRAINT FK_27DE0DA994879022 FOREIGN KEY (frequency_id) REFERENCES export_period (id)');
        $this->addSql('ALTER TABLE user_export ADD CONSTRAINT FK_27DE0DA9190BE4C5 FOREIGN KEY (user_client_id) REFERENCES user_client (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_27DE0DA9EC8B7ADE ON user_export (period_id)');
        $this->addSql('CREATE INDEX IDX_27DE0DA994879022 ON user_export (frequency_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_export DROP FOREIGN KEY FK_27DE0DA9EC8B7ADE');
        $this->addSql('ALTER TABLE user_export DROP FOREIGN KEY FK_27DE0DA994879022');
        $this->addSql('ALTER TABLE user_export DROP FOREIGN KEY FK_27DE0DA9190BE4C5');
        $this->addSql('DROP INDEX IDX_27DE0DA9EC8B7ADE ON user_export');
        $this->addSql('DROP INDEX IDX_27DE0DA994879022 ON user_export');
        $this->addSql('ALTER TABLE user_export DROP period_id, DROP frequency_id');
        $this->addSql('ALTER TABLE user_export ADD CONSTRAINT FK_27DE0DA9190BE4C5 FOREIGN KEY (user_client_id) REFERENCES user_client (id)');
    }
}
