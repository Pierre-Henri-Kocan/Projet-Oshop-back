<?php

namespace App\Models;

use App\Utils\Database;
use PDO;

/**
 * Une instance de Product = un produit dans la base de données
 * Product hérite de CoreModel
 */
class Product extends CoreModel
{

    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $description;
    /**
     * @var string
     */
    private $picture;
    /**
     * @var float
     */
    private $price;
    /**
     * @var int
     */
    private $rate;
    /**
     * @var int
     */
    private $status;
    /**
     * @var int
     */
    private $brand_id;
    /**
     * @var int
     */
    private $category_id;
    /**
     * @var int
     */
    private $type_id;

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
     * Get the value of description
     *
     * @return  string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @param  string  $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * Get the value of picture
     *
     * @return  string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set the value of picture
     *
     * @param  string  $picture
     */
    public function setPicture(string $picture)
    {
        $this->picture = $picture;
    }

    /**
     * Get the value of price
     *
     * @return  float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set the value of price
     *
     * @param  float  $price
     */
    public function setPrice(float $price)
    {
        $this->price = $price;
    }

    /**
     * Get the value of rate
     *
     * @return  int
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Set the value of rate
     *
     * @param  int  $rate
     */
    public function setRate(int $rate)
    {
        $this->rate = $rate;
    }

    /**
     * Get the value of status
     *
     * @return  int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @param  int  $status
     */
    public function setStatus(int $status)
    {
        $this->status = $status;
    }

    /**
     * Get the value of brand_id
     *
     * @return  int
     */
    public function getBrandId()
    {
        return $this->brand_id;
    }

    /**
     * Set the value of brand_id
     *
     * @param  int  $brand_id
     */
    public function setBrandId(int $brand_id)
    {
        $this->brand_id = $brand_id;
    }

    /**
     * Get the value of category_id
     *
     * @return  int
     */
    public function getCategoryId()
    {
        return $this->category_id;
    }

    /**
     * Set the value of category_id
     *
     * @param  int  $category_id
     */
    public function setCategoryId(int $category_id)
    {
        $this->category_id = $category_id;
    }

    /**
     * Get the value of type_id
     *
     * @return  int
     */
    public function getTypeId()
    {
        return $this->type_id;
    }

    /**
     * Set the value of type_id
     *
     * @param  int  $type_id
     */
    public function setTypeId(int $type_id)
    {
        $this->type_id = $type_id;
    }

    /**
     * Méthode permettant de récupérer un enregistrement de la table Product en fonction d'un id donné
     *
     * @param int $id ID du produit
     * @return Product|null
     */
    public static function find(int $id): ?self
    {
        // récupérer un objet PDO = connexion à la BDD
        $pdo = Database::getPDO();

        // on écrit la requête SQL pour récupérer le produit
        $sql = '
            SELECT *
            FROM product
            WHERE id = ' . $id;

        // query ? exec ?
        // On fait de la LECTURE = une récupration => query()
        // si on avait fait une modification, suppression, ou un ajout => exec
        $pdoStatement = $pdo->query($sql);

        // fetchObject() pour récupérer un seul résultat
        // si j'en avais eu plusieurs => fetchAll
        $result = $pdoStatement->fetchObject(self::class);

        return $result ?: null;
    }

    /**
     * Méthode permettant de récupérer tous les enregistrements de la table product
     *
     * @return Product[]
     */
    public static function findAll(): array
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT * FROM `product`';
        $pdoStatement = $pdo->query($sql);
        $results = $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);

        return $results;
    }

    /**
     * Récupérer les X premiers produits dispo
     *
     * @param int $limit Nombre max de résultat attendu (> 1 | par défaut 5)
     *
     * @return Product[]
     */
    public static function findWithLimit(int $limit = 5): array
    {
        if ($limit < 1) {
            $limit = 1;
        }

        $pdo = Database::getPDO();
        $sql = "
            SELECT *
            FROM product
            WHERE status = 1
            LIMIT $limit
        ";
        $pdoStatement = $pdo->query($sql);
        $results = $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);

        return $results;
    }

    /**
     * Méthode permettant d'ajouter un enregistrement dans la table product
     * L'objet courant doit contenir toutes les données à ajouter : 1 propriété => 1 colonne dans la table
     *
     * @return bool
     */
    public function insert(): bool
    {
        // Récupération de l'objet PDO représentant la connexion à la DB
        $pdo = Database::getPDO();

        $matchingArray = [
            'name' => $this->name,
            'description' => $this->description,
            'picture' => $this->picture,
            'price' => $this->price,
            'rate' => $this->rate,
            'status' => $this->status,
            'brand_id' => $this->brand_id,
            'category_id' => $this->category_id,
            'type_id' => $this->type_id,
        ];

        // On prépare notre requête SQL en lui mettant des espèces de points
        // d'ancrage qu'on va vouloir remplacer par des valeurs
        $sql = "INSERT INTO `product` (" . implode(', ', array_keys($matchingArray)) . ")
                VALUES (:" . implode(', :', array_keys($matchingArray)) . ")";

        // PdoStatement va venir binder les valeurs avec les points d'ancrage dans
        // la requete préparé au dessus, en faisant tout les traitement nécessaires
        // pour sécuriser notre requete contre les injections SQL
        $pdoStatement = $pdo->prepare($sql);

        $isInsert = $pdoStatement->execute($matchingArray);

        if ($isInsert) {
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
            'description' => $this->description,
            'picture' => $this->picture,
            'price' => $this->price,
            'rate' => $this->rate,
            'status' => $this->status,
            'brand_id' => $this->brand_id,
            'category_id' => $this->category_id,
            'type_id' => $this->type_id,
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
            UPDATE `product`
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

        $pdoStatement = $pdo->prepare("DELETE FROM `product` WHERE id = :id");

        return $pdoStatement->execute(['id' => $this->id]);
    }
}
