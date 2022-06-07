<?php

namespace App\Models;

use App\Utils\Database;
use PDO;

class Category extends CoreModel
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $subtitle;
    /**
     * @var string
     */
    private $picture;
    /**
     * @var int
     */
    private $home_order;

    /**
     * Get the value of name
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param  string  $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * Get the value of subtitle
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set the value of subtitle
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
    }

    /**
     * Get the value of picture
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set the value of picture
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    /**
     * Get the value of home_order
     */
    public function getHomeOrder()
    {
        return $this->home_order;
    }

    /**
     * Set the value of home_order
     */
    public function setHomeOrder($home_order)
    {
        $this->home_order = $home_order;
    }

    /**
     * Méthode permettant de récupérer un enregistrement de la table Category en fonction d'un id donné
     *
     * @param int $id ID de la catégorie
     * @return Category|null
     */
    public static function find(int $id): ?self
    {
        // se connecter à la BDD
        $pdo = Database::getPDO();

        // écrire notre requête
        $sql = 'SELECT * FROM `category` WHERE `id` =' . $id;

        // exécuter notre requête
        $pdoStatement = $pdo->query($sql);

        // un seul résultat => fetchObject
        $category = $pdoStatement->fetchObject(self::class);

        // retourner le résultat
        // return $category ? $category : null;
        return $category ?: null;
    }

    /**
     * Méthode permettant de récupérer tous les enregistrements de la table category
     *
     * @return Category[]
     */
    public static function findAll(): array
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT * FROM `category`';
        $pdoStatement = $pdo->query($sql);
        $results = $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);

        return $results;
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

        $pdo = Database::getPDO();
        $sql = "
            SELECT *
            FROM category
            WHERE home_order > 0
            ORDER BY home_order ASC
            LIMIT $limit
        ";
        $pdoStatement = $pdo->query($sql);
        $categories = $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);

        return $categories;
    }

    /**
     * Méthode permettant d'ajouter un enregistrement dans la table category
     * L'objet courant doit contenir toutes les données à ajouter : 1 propriété => 1 colonne dans la table
     *
     * @return bool
     */
    public function insert(): bool
    {
        // Récupération de l'objet PDO représentant la connexion à la DB
        $pdo = Database::getPDO();

        // Ecriture de la requête INSERT INTO
        // $sql = "
        //     INSERT INTO `category` (name, subtitle, picture)
        //     VALUES (?, ?, ?)
        // ";

        // $pdoStatement = $pdo->prepare($sql);
        // $pdoStatement->bindValue(1, $this->name);
        // $pdoStatement->bindValue(2, $this->subtitle);
        // $pdoStatement->bindValue(3, $this->picture);

        // On prépare notre requête SQL en lui mettant des espèces de points
        // d'ancrage qu'on va vouloir remplacer par des valeurs
        $sql = "
            INSERT INTO `category` (name, subtitle, picture)
            VALUES (:name, :subtitle, :picture)
        ";

        // PdoStatement va venir binder les valeurs avec les points d'ancrage dans
        // la requete préparé au dessus, en faisant tout les traitement nécessaires
        // pour sécuriser notre requete contre les injections SQL
        $pdoStatement = $pdo->prepare($sql);
        $pdoStatement->bindValue(':name', $this->name);
        $pdoStatement->bindValue(':subtitle', $this->subtitle);
        $pdoStatement->bindValue(':picture', $this->picture);

        if ($pdoStatement->execute()) {
            // Alors on récupère l'id auto-incrémenté généré par MySQL
            $this->id = $pdo->lastInsertId();

            return true;
        }

        return false;
    }

    /**
     * Méthode permettant de mettre à jour un enregistrement dans la table category
     * L'objet courant doit contenir l'id, et toutes les données à ajouter : 1 propriété => 1 colonne dans la table
     *
     * @return bool
     */
    public function update(): bool
    {
        $matchingArray = [
            'id' => $this->id,
            'name' => $this->name,
            'subtitle' => $this->subtitle,
            'picture' => $this->picture,
        ];

        // Récupération de l'objet PDO représentant la connexion à la DB
        $pdo = Database::getPDO();

        $sqlSet = [];
        foreach (array_keys($matchingArray) as $field) {
            if ($field !== 'id') {
                $sqlSet[] = "`$field` = :$field";
            }
        }

        // Ecriture de la requête UPDATE
        $sql = "
            UPDATE `category`
            SET " . implode(", ", $sqlSet) . ", updated_at = NOW()
            WHERE id = :id
        ";

        // Execution de la requête de mise à jour (exec, pas query)
        $pdoStatement = $pdo->prepare($sql);

        $pdoStatement->execute($matchingArray);

        // On retourne VRAI, si au moins une ligne modifié
        return $pdoStatement->rowCount() > 0;
    }

    public function delete(): bool
    {
        $pdo = Database::getPDO();

        $pdoStatement = $pdo->prepare("DELETE FROM `category` WHERE id = :id");

        return $pdoStatement->execute(['id' => $this->id]);
    }
}
