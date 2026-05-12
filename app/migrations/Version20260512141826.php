<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260512141826 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY `FK_DA88B13769CCBE9A`');
        $this->addSql('DROP INDEX IDX_DA88B13769CCBE9A ON recipe');
        $this->addSql('ALTER TABLE recipe CHANGE author_id_id author_id INT NOT NULL');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B137F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_DA88B137F675F31B ON recipe (author_id)');
        $this->addSql('ALTER TABLE recipe_ingredients DROP FOREIGN KEY `FK_9F925F2B6676F996`');
        $this->addSql('ALTER TABLE recipe_ingredients DROP FOREIGN KEY `FK_9F925F2B69574A48`');
        $this->addSql('DROP INDEX IDX_9F925F2B69574A48 ON recipe_ingredients');
        $this->addSql('DROP INDEX IDX_9F925F2B6676F996 ON recipe_ingredients');
        $this->addSql('ALTER TABLE recipe_ingredients ADD ingredient_id INT NOT NULL, ADD recipe_id INT NOT NULL, DROP ingredient_id_id, DROP recipe_id_id');
        $this->addSql('ALTER TABLE recipe_ingredients ADD CONSTRAINT FK_9F925F2B933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id)');
        $this->addSql('ALTER TABLE recipe_ingredients ADD CONSTRAINT FK_9F925F2B59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('CREATE INDEX IDX_9F925F2B933FE08C ON recipe_ingredients (ingredient_id)');
        $this->addSql('CREATE INDEX IDX_9F925F2B59D8A214 ON recipe_ingredients (recipe_id)');
        $this->addSql('ALTER TABLE recipe_step DROP FOREIGN KEY `FK_3CA2A4E3D9ED1E33`');
        $this->addSql('DROP INDEX IDX_3CA2A4E3D9ED1E33 ON recipe_step');
        $this->addSql('ALTER TABLE recipe_step CHANGE id_recipe_id recipe_id INT NOT NULL');
        $this->addSql('ALTER TABLE recipe_step ADD CONSTRAINT FK_3CA2A4E359D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('CREATE INDEX IDX_3CA2A4E359D8A214 ON recipe_step (recipe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B137F675F31B');
        $this->addSql('DROP INDEX IDX_DA88B137F675F31B ON recipe');
        $this->addSql('ALTER TABLE recipe CHANGE author_id author_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT `FK_DA88B13769CCBE9A` FOREIGN KEY (author_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_DA88B13769CCBE9A ON recipe (author_id_id)');
        $this->addSql('ALTER TABLE recipe_ingredients DROP FOREIGN KEY FK_9F925F2B933FE08C');
        $this->addSql('ALTER TABLE recipe_ingredients DROP FOREIGN KEY FK_9F925F2B59D8A214');
        $this->addSql('DROP INDEX IDX_9F925F2B933FE08C ON recipe_ingredients');
        $this->addSql('DROP INDEX IDX_9F925F2B59D8A214 ON recipe_ingredients');
        $this->addSql('ALTER TABLE recipe_ingredients ADD ingredient_id_id INT NOT NULL, ADD recipe_id_id INT NOT NULL, DROP ingredient_id, DROP recipe_id');
        $this->addSql('ALTER TABLE recipe_ingredients ADD CONSTRAINT `FK_9F925F2B6676F996` FOREIGN KEY (ingredient_id_id) REFERENCES ingredient (id)');
        $this->addSql('ALTER TABLE recipe_ingredients ADD CONSTRAINT `FK_9F925F2B69574A48` FOREIGN KEY (recipe_id_id) REFERENCES recipe (id)');
        $this->addSql('CREATE INDEX IDX_9F925F2B69574A48 ON recipe_ingredients (recipe_id_id)');
        $this->addSql('CREATE INDEX IDX_9F925F2B6676F996 ON recipe_ingredients (ingredient_id_id)');
        $this->addSql('ALTER TABLE recipe_step DROP FOREIGN KEY FK_3CA2A4E359D8A214');
        $this->addSql('DROP INDEX IDX_3CA2A4E359D8A214 ON recipe_step');
        $this->addSql('ALTER TABLE recipe_step CHANGE recipe_id id_recipe_id INT NOT NULL');
        $this->addSql('ALTER TABLE recipe_step ADD CONSTRAINT `FK_3CA2A4E3D9ED1E33` FOREIGN KEY (id_recipe_id) REFERENCES recipe (id)');
        $this->addSql('CREATE INDEX IDX_3CA2A4E3D9ED1E33 ON recipe_step (id_recipe_id)');
    }
}
