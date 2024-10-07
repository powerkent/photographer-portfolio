<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241007074939 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Change relationship between photo and category';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE photo_category (photo_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_9570064B7E9E4C8C (photo_id), INDEX IDX_9570064B12469DE2 (category_id), PRIMARY KEY(photo_id, category_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE photo_category ADD CONSTRAINT FK_9570064B7E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE photo_category ADD CONSTRAINT FK_9570064B12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE photo DROP FOREIGN KEY FK_14B7841812469DE2');
        $this->addSql('DROP INDEX IDX_14B7841812469DE2 ON photo');
        $this->addSql('ALTER TABLE photo DROP category_id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE photo_category DROP FOREIGN KEY FK_9570064B7E9E4C8C');
        $this->addSql('ALTER TABLE photo_category DROP FOREIGN KEY FK_9570064B12469DE2');
        $this->addSql('DROP TABLE photo_category');
        $this->addSql('ALTER TABLE photo ADD category_id INT NOT NULL');
        $this->addSql('ALTER TABLE photo ADD CONSTRAINT FK_14B7841812469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_14B7841812469DE2 ON photo (category_id)');
    }
}
