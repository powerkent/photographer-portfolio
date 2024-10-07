<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241006164548 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add jooderock image_path in Settings table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE settings ADD image_path VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE settings DROP image_path');
    }
}
