<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250213110543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category_recipes (category_id INT NOT NULL, recipes_id INT NOT NULL, INDEX IDX_64B1A2CB12469DE2 (category_id), INDEX IDX_64B1A2CBFDF2B1FA (recipes_id), PRIMARY KEY(category_id, recipes_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE food (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notice (id INT AUTO_INCREMENT NOT NULL, recipes_id INT DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, rating INT NOT NULL, INDEX IDX_480D45C2FDF2B1FA (recipes_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE preferences (id INT AUTO_INCREMENT NOT NULL, diet VARCHAR(255) DEFAULT NULL, allergy VARCHAR(255) DEFAULT NULL, meal_quantity INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE quantity_food (id INT AUTO_INCREMENT NOT NULL, recipe_id_id INT NOT NULL, food_id_id INT NOT NULL, quantity INT NOT NULL, unity VARCHAR(255) NOT NULL, INDEX IDX_935547D569574A48 (recipe_id_id), UNIQUE INDEX UNIQ_935547D58E255BBD (food_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipes (id INT AUTO_INCREMENT NOT NULL, created_by_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, cooking_time INT NOT NULL, description LONGTEXT DEFAULT NULL, calories INT NOT NULL, quantity INT NOT NULL, preparation_time INT NOT NULL, instructions LONGTEXT NOT NULL, difficulty VARCHAR(255) NOT NULL, is_public TINYINT(1) NOT NULL, INDEX IDX_A369E2B5B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipes_list (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_7F9099517E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recipes_list_recipes (recipes_list_id INT NOT NULL, recipes_id INT NOT NULL, INDEX IDX_5219A2225BB323DF (recipes_list_id), INDEX IDX_5219A222FDF2B1FA (recipes_id), PRIMARY KEY(recipes_list_id, recipes_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_preferences (user_id INT NOT NULL, preferences_id INT NOT NULL, INDEX IDX_402A6F60A76ED395 (user_id), INDEX IDX_402A6F607CCD6FB7 (preferences_id), PRIMARY KEY(user_id, preferences_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE category_recipes ADD CONSTRAINT FK_64B1A2CB12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_recipes ADD CONSTRAINT FK_64B1A2CBFDF2B1FA FOREIGN KEY (recipes_id) REFERENCES recipes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notice ADD CONSTRAINT FK_480D45C2FDF2B1FA FOREIGN KEY (recipes_id) REFERENCES recipes (id)');
        $this->addSql('ALTER TABLE quantity_food ADD CONSTRAINT FK_935547D569574A48 FOREIGN KEY (recipe_id_id) REFERENCES recipes (id)');
        $this->addSql('ALTER TABLE quantity_food ADD CONSTRAINT FK_935547D58E255BBD FOREIGN KEY (food_id_id) REFERENCES food (id)');
        $this->addSql('ALTER TABLE recipes ADD CONSTRAINT FK_A369E2B5B03A8386 FOREIGN KEY (created_by_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE recipes_list ADD CONSTRAINT FK_7F9099517E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE recipes_list_recipes ADD CONSTRAINT FK_5219A2225BB323DF FOREIGN KEY (recipes_list_id) REFERENCES recipes_list (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE recipes_list_recipes ADD CONSTRAINT FK_5219A222FDF2B1FA FOREIGN KEY (recipes_id) REFERENCES recipes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_preferences ADD CONSTRAINT FK_402A6F60A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_preferences ADD CONSTRAINT FK_402A6F607CCD6FB7 FOREIGN KEY (preferences_id) REFERENCES preferences (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category_recipes DROP FOREIGN KEY FK_64B1A2CB12469DE2');
        $this->addSql('ALTER TABLE category_recipes DROP FOREIGN KEY FK_64B1A2CBFDF2B1FA');
        $this->addSql('ALTER TABLE notice DROP FOREIGN KEY FK_480D45C2FDF2B1FA');
        $this->addSql('ALTER TABLE quantity_food DROP FOREIGN KEY FK_935547D569574A48');
        $this->addSql('ALTER TABLE quantity_food DROP FOREIGN KEY FK_935547D58E255BBD');
        $this->addSql('ALTER TABLE recipes DROP FOREIGN KEY FK_A369E2B5B03A8386');
        $this->addSql('ALTER TABLE recipes_list DROP FOREIGN KEY FK_7F9099517E3C61F9');
        $this->addSql('ALTER TABLE recipes_list_recipes DROP FOREIGN KEY FK_5219A2225BB323DF');
        $this->addSql('ALTER TABLE recipes_list_recipes DROP FOREIGN KEY FK_5219A222FDF2B1FA');
        $this->addSql('ALTER TABLE user_preferences DROP FOREIGN KEY FK_402A6F60A76ED395');
        $this->addSql('ALTER TABLE user_preferences DROP FOREIGN KEY FK_402A6F607CCD6FB7');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE category_recipes');
        $this->addSql('DROP TABLE food');
        $this->addSql('DROP TABLE notice');
        $this->addSql('DROP TABLE preferences');
        $this->addSql('DROP TABLE quantity_food');
        $this->addSql('DROP TABLE recipes');
        $this->addSql('DROP TABLE recipes_list');
        $this->addSql('DROP TABLE recipes_list_recipes');
        $this->addSql('DROP TABLE `user`');
        $this->addSql('DROP TABLE user_preferences');
    }
}
