<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240221181046 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD food_category_id INT NOT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1B3F04B2C FOREIGN KEY (food_category_id) REFERENCES food (id)');
        $this->addSql('CREATE INDEX IDX_64C19C1B3F04B2C ON category (food_category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1B3F04B2C');
        $this->addSql('DROP INDEX IDX_64C19C1B3F04B2C ON category');
        $this->addSql('ALTER TABLE category DROP food_category_id');
    }
}
