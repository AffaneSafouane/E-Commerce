<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260412193256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE media_product (media_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_B29D82E4EA9FDD75 (media_id), INDEX IDX_B29D82E44584665A (product_id), PRIMARY KEY (media_id, product_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE media_category (media_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_92D3773EA9FDD75 (media_id), INDEX IDX_92D377312469DE2 (category_id), PRIMARY KEY (media_id, category_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE media_product ADD CONSTRAINT FK_B29D82E4EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media_product ADD CONSTRAINT FK_B29D82E44584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media_category ADD CONSTRAINT FK_92D3773EA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media_category ADD CONSTRAINT FK_92D377312469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY `FK_6A2CA10C12469DE2`');
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY `FK_6A2CA10C4584665A`');
        $this->addSql('DROP INDEX IDX_6A2CA10C12469DE2 ON media');
        $this->addSql('DROP INDEX IDX_6A2CA10C4584665A ON media');
        $this->addSql('ALTER TABLE media DROP product_id, DROP category_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media_product DROP FOREIGN KEY FK_B29D82E4EA9FDD75');
        $this->addSql('ALTER TABLE media_product DROP FOREIGN KEY FK_B29D82E44584665A');
        $this->addSql('ALTER TABLE media_category DROP FOREIGN KEY FK_92D3773EA9FDD75');
        $this->addSql('ALTER TABLE media_category DROP FOREIGN KEY FK_92D377312469DE2');
        $this->addSql('DROP TABLE media_product');
        $this->addSql('DROP TABLE media_category');
        $this->addSql('ALTER TABLE media ADD product_id INT DEFAULT NULL, ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT `FK_6A2CA10C12469DE2` FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT `FK_6A2CA10C4584665A` FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_6A2CA10C12469DE2 ON media (category_id)');
        $this->addSql('CREATE INDEX IDX_6A2CA10C4584665A ON media (product_id)');
    }
}
