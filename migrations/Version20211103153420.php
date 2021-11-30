<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211103153420 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE flags (id INT AUTO_INCREMENT NOT NULL, post_id INT NOT NULL, category_id INT NOT NULL, topic_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_B0541BA4B89032C (post_id), INDEX IDX_B0541BA12469DE2 (category_id), INDEX IDX_B0541BA1F55203D (topic_id), INDEX IDX_B0541BAA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE flags ADD CONSTRAINT FK_B0541BA4B89032C FOREIGN KEY (post_id) REFERENCES posts (id)');
        $this->addSql('ALTER TABLE flags ADD CONSTRAINT FK_B0541BA12469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE flags ADD CONSTRAINT FK_B0541BA1F55203D FOREIGN KEY (topic_id) REFERENCES topics (id)');
        $this->addSql('ALTER TABLE flags ADD CONSTRAINT FK_B0541BAA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE flags');
    }
}
