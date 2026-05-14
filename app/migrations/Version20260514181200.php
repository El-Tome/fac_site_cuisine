<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260514181200 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ingredient (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE ingredient_ingredient_categories (ingredient_id INT NOT NULL, ingredient_categories_id INT NOT NULL, INDEX IDX_88663C12933FE08C (ingredient_id), INDEX IDX_88663C127E4D7220 (ingredient_categories_id), PRIMARY KEY (ingredient_id, ingredient_categories_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE ingredient_categories (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE meal_plan (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_C7848889A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE meal_plan_recipe (meal_plan_id INT NOT NULL, recipe_id INT NOT NULL, INDEX IDX_632AAFCD912AB082 (meal_plan_id), INDEX IDX_632AAFCD59D8A214 (recipe_id), PRIMARY KEY (meal_plan_id, recipe_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE recipe (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, prep_time INT NOT NULL, cook_time INT DEFAULT NULL, difficulty VARCHAR(255) NOT NULL, servings INT NOT NULL, image_url VARCHAR(255) DEFAULT NULL, featured TINYINT NOT NULL, author_id INT NOT NULL, INDEX IDX_DA88B137F675F31B (author_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE recipe_ingredients (id INT AUTO_INCREMENT NOT NULL, quantity DOUBLE PRECISION NOT NULL, unit VARCHAR(255) NOT NULL, ingredient_id INT NOT NULL, recipe_id INT NOT NULL, INDEX IDX_9F925F2B933FE08C (ingredient_id), INDEX IDX_9F925F2B59D8A214 (recipe_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE recipe_like (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, recipe_id INT NOT NULL, INDEX IDX_D3781A6CA76ED395 (user_id), INDEX IDX_D3781A6C59D8A214 (recipe_id), UNIQUE INDEX uniq_user_recipe_like (user_id, recipe_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE recipe_step (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, explanation LONGTEXT DEFAULT NULL, step INT NOT NULL, recipe_id INT NOT NULL, INDEX IDX_3CA2A4E359D8A214 (recipe_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, pseudo VARCHAR(63) NOT NULL, pseudo_id SMALLINT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE ingredient_ingredient_categories ADD CONSTRAINT FK_88663C12933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ingredient_ingredient_categories ADD CONSTRAINT FK_88663C127E4D7220 FOREIGN KEY (ingredient_categories_id) REFERENCES ingredient_categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE meal_plan ADD CONSTRAINT FK_C7848889A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE meal_plan_recipe ADD CONSTRAINT FK_632AAFCD912AB082 FOREIGN KEY (meal_plan_id) REFERENCES meal_plan (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE meal_plan_recipe ADD CONSTRAINT FK_632AAFCD59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recipe ADD CONSTRAINT FK_DA88B137F675F31B FOREIGN KEY (author_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE recipe_ingredients ADD CONSTRAINT FK_9F925F2B933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id)');
        $this->addSql('ALTER TABLE recipe_ingredients ADD CONSTRAINT FK_9F925F2B59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
        $this->addSql('ALTER TABLE recipe_like ADD CONSTRAINT FK_D3781A6CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE recipe_like ADD CONSTRAINT FK_D3781A6C59D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recipe_step ADD CONSTRAINT FK_3CA2A4E359D8A214 FOREIGN KEY (recipe_id) REFERENCES recipe (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ingredient_ingredient_categories DROP FOREIGN KEY FK_88663C12933FE08C');
        $this->addSql('ALTER TABLE ingredient_ingredient_categories DROP FOREIGN KEY FK_88663C127E4D7220');
        $this->addSql('ALTER TABLE meal_plan DROP FOREIGN KEY FK_C7848889A76ED395');
        $this->addSql('ALTER TABLE meal_plan_recipe DROP FOREIGN KEY FK_632AAFCD912AB082');
        $this->addSql('ALTER TABLE meal_plan_recipe DROP FOREIGN KEY FK_632AAFCD59D8A214');
        $this->addSql('ALTER TABLE recipe DROP FOREIGN KEY FK_DA88B137F675F31B');
        $this->addSql('ALTER TABLE recipe_ingredients DROP FOREIGN KEY FK_9F925F2B933FE08C');
        $this->addSql('ALTER TABLE recipe_ingredients DROP FOREIGN KEY FK_9F925F2B59D8A214');
        $this->addSql('ALTER TABLE recipe_like DROP FOREIGN KEY FK_D3781A6CA76ED395');
        $this->addSql('ALTER TABLE recipe_like DROP FOREIGN KEY FK_D3781A6C59D8A214');
        $this->addSql('ALTER TABLE recipe_step DROP FOREIGN KEY FK_3CA2A4E359D8A214');
        $this->addSql('DROP TABLE ingredient');
        $this->addSql('DROP TABLE ingredient_ingredient_categories');
        $this->addSql('DROP TABLE ingredient_categories');
        $this->addSql('DROP TABLE meal_plan');
        $this->addSql('DROP TABLE meal_plan_recipe');
        $this->addSql('DROP TABLE recipe');
        $this->addSql('DROP TABLE recipe_ingredients');
        $this->addSql('DROP TABLE recipe_like');
        $this->addSql('DROP TABLE recipe_step');
        $this->addSql('DROP TABLE user');
    }
}
