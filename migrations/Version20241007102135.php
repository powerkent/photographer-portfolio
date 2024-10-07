<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241007102135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add google api key for map';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE settings ADD google_api_key VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE settings DROP google_api_key');
    }
}
