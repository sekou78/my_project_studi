<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240221181759 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking ADD booking_restaurant_id INT NOT NULL');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDEBADE30AE FOREIGN KEY (booking_restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('CREATE INDEX IDX_E00CEDDEBADE30AE ON booking (booking_restaurant_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE booking DROP FOREIGN KEY FK_E00CEDDEBADE30AE');
        $this->addSql('DROP INDEX IDX_E00CEDDEBADE30AE ON booking');
        $this->addSql('ALTER TABLE booking DROP booking_restaurant_id');
    }
}
