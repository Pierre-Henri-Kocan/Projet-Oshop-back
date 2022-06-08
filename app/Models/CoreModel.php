<?php

namespace App\Models;

use App\Utils\Database;
use PDO;

// Classe mère de tous les Models
// On centralise ici toutes les propriétés et méthodes utiles pour TOUS les Models
abstract class CoreModel
{
    /** @var int|null */
    protected $id;

    /** @var string */
    protected $created_at;

    /** @var string|null */
    protected $updated_at;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }

    /**
     * Retourne le nom de la table en DB qui est associé à notre Model
     *
     * @return string
     */
    abstract protected static function tableName(): string;

    public function save(): bool
    {
        if ($this->getId() === null) {
            return $this->insert();
        }
        else {
            return $this->update();
        }
    }

    /**
     * Retourne l'objet correspondant à l'id en paramètre
     *
     * @param int $id
     *
     * @return $this|null
     */
    public static function find(int $id): ?self
    {
        $table = static::tableName();

        // Se connecter à la BDD
        $pdo = Database::getPDO();

        $pdoStatement = $pdo->prepare("SELECT * FROM `$table` WHERE `id` = :id");
        $pdoStatement->execute(['id' => $id]);

        // Un seul résultat => fetchObject
        // On utilise le mot clé static au lieu de self, parce que self réfère
        // firectement à la classe où il est écrit, alors que static réfère à la
        // classe qu'on est réellement entrain d'utiliser (l'enfant de CoreModel ici)
        $object = $pdoStatement->fetchObject(static::class);

        // Retourner le résultat
        // return $object ? $object : null;
        return $object ?: null;
    }

    /**
     * Retourne la liste complète d'objets
     *
     * @return $this[]
     */
    public static function findAll(): array
    {
        $table = static::tableName();

        $pdo = Database::getPDO();
        $sql = "SELECT * FROM `$table`";
        $pdoStatement = $pdo->query($sql);
        $results = $pdoStatement->fetchAll(PDO::FETCH_CLASS, static::class);

        return $results;
    }

    /**
     * Ajoute l'objet en DB
     *
     * @return bool
     */
    public function insert(): bool
    {
        $table = static::tableName();

        $matchingArray = [];
        foreach ($this as $property => $value) {
            if ($property !== 'id' && $property !== 'created_at' && $property !== 'updated_at') {
                $matchingArray[$property] = $value;
            }
        }

        // Récupération de l'objet PDO représentant la connexion à la DB
        $pdo = Database::getPDO();

        // On prépare notre requête SQL en lui mettant des espèces de points
        // d'ancrage qu'on va vouloir remplacer par des valeurs
        $sql = "INSERT INTO `$table` (" . implode(', ', array_keys($matchingArray)) . ")
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
     * Modifie l'objet en DB
     *
     * @return bool
     */
    public function update(): bool
    {
        $table = static::tableName();

        $matchingArray = [];
        foreach ($this as $property => $value) {
            if ($property !== 'created_at' && $property !== 'updated_at') {
                $matchingArray[$property] = $value;
            }
        }

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
            UPDATE `$table`
            SET " . implode(", ", $sqlSet) . ", updated_at = NOW()
            WHERE id = :id
        ";

        // Execution de la requête de mise à jour (exec, pas query)
        $pdoStatement = $pdo->prepare($sql);

        $pdoStatement->execute($matchingArray);

        // On retourne VRAI, si au moins une ligne modifié
        return $pdoStatement->rowCount() > 0;
    }

    /**
     * Supprime l'objet de la DB
     *
     * @return bool
     */
    public function delete(): bool
    {
        $table = static::tableName();

        $pdo = Database::getPDO();

        $pdoStatement = $pdo->prepare("DELETE FROM `$table` WHERE id = :id");

        return $pdoStatement->execute(['id' => $this->id]);
    }
}
