<?php

namespace App\Models;

use App\Utils\Database;
use PDO;

/**
 * Un modèle représente une table (un entité) dans notre base
 *
 * Un objet issu de cette classe réprésente un enregistrement dans cette table
 */
class Type extends CoreModel
{
    // Les propriétés représentent les champs
    // Attention il faut que les propriétés aient le même nom (précisément) que les colonnes de la table

    /**
     * @var string
     */
    private $name;

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
     * Méthode permettant de récupérer un enregistrement de la table Type en fonction d'un id donné
     *
     * @param int $id ID du type
     * @return Type|null
     */
    public static function find(int $id): ?self
    {
        // se connecter à la BDD
        $pdo = Database::getPDO();

        // écrire notre requête
        $sql = 'SELECT * FROM `type` WHERE `id` =' . $id;

        // exécuter notre requête
        $pdoStatement = $pdo->query($sql);

        // un seul résultat => fetchObject
        $type = $pdoStatement->fetchObject(self::class);

        // retourner le résultat
        return $type ?: null;
    }

    /**
     * Méthode permettant de récupérer tous les enregistrements de la table type
     *
     * @return Type[]
     */
    public static function findAll(): array
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT * FROM `type`';
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
        ];

        // On prépare notre requête SQL en lui mettant des espèces de points
        // d'ancrage qu'on va vouloir remplacer par des valeurs
        $sql = "INSERT INTO `type` (" . implode(', ', array_keys($matchingArray)) . ")
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
            UPDATE `type`
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

        $pdoStatement = $pdo->prepare("DELETE FROM `type` WHERE id = :id");

        return $pdoStatement->execute(['id' => $this->id]);
    }
}
