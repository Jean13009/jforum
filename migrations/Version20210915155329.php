<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210915155329 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE topics ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE topics ADD CONSTRAINT FK_91F6463912469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('CREATE INDEX IDX_91F6463912469DE2 ON topics (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE topics DROP FOREIGN KEY FK_91F6463912469DE2');
        $this->addSql('DROP INDEX IDX_91F6463912469DE2 ON topics');
        $this->addSql('ALTER TABLE topics DROP category_id');
    }
}
