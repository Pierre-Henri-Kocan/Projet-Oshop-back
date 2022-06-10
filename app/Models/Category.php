<?php

namespace App\Models;

use App\Utils\Database;
use PDO;

class Category extends CoreModel
{
    /** @var string */
    protected $name = '';

    /** @var string|null */
    protected $subtitle;

    /** @var string|null */
    protected $picture;

    /** @var int */
    protected $home_order = 0;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): self
    {
        $this->subtitle = $subtitle;
        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;
        return $this;
    }

    public function getHomeOrder(): int
    {
        return $this->home_order;
    }

    public function setHomeOrder(int $home_order): self
    {
        $this->home_order = $home_order;
        return $this;
    }

    protected static function tableName(): string
    {
        return 'category';
    }

    /**
     * Récupérer les X premières catégories avec un un positionnement sur la home page
     *
     * @param int $limit Nombre max de résultat attendu (> 1 | par défaut 5)
     *
     * @return Category[]
     */
    public static function findWithLimit(int $limit = 5): array
    {
        if ($limit < 1) {
            $limit = 1;
        }

        $table = self::tableName();

        $pdo = Database::getPDO();
        $sql = "
            SELECT *
            FROM $table
            WHERE home_order > 0
            ORDER BY home_order ASC
            LIMIT $limit
        ";
        $pdoStatement = $pdo->query($sql);
        $categories = $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);

        return $categories;
    }

    /**
     * Enregistre l'ordre des catégories sur la homepage
     *
     * @param int[] $categoriesIdInOrder Liste des id de catégories, dans l'ordre
     *                                   qu'on veut enregistrer pour la homepage
     *
     * @return bool
     */
    public static function defineHomepage(array $categoriesIdInOrder): bool
    {
        // Récupération de l'objet PDO représentant la connexion à la DB
        $pdo = Database::getPDO();

        // Notre variable sql contient 6 UPDATE
        // On peut faire plusieurs requêtes en une seule fois en les séparant par
        // des point-virgules «;»
        // Contrairement à toutes les requêtes qu'on a faites en S06, on n'utilise
        // pas de paramètres nommés mais plutot des paramètres identifiés par un
        // simple point d'interrogation «?»
        $sql = "
            UPDATE `category` SET home_order = 0;
            UPDATE `category` SET home_order = 1 WHERE id = ?;
            UPDATE `category` SET home_order = 2 WHERE id = ?;
            UPDATE `category` SET home_order = 3 WHERE id = ?;
            UPDATE `category` SET home_order = 4 WHERE id = ?;
            UPDATE `category` SET home_order = 5 WHERE id = ?;
        ";

        $pdoStatement = $pdo->prepare($sql);

        return $pdoStatement->execute(array_values($categoriesIdInOrder));
    }
}
