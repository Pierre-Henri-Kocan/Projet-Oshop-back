<?php

namespace App\Models;

use PDO;
use App\Utils\Database;
use App\Models\CoreModel;

/**
 * Un modèle représente une table (un entité) dans notre base
 *
 * Un objet issu de cette classe réprésente un enregistrement dans cette table
 */
class AppUser extends CoreModel
{
    /** @var string */
    private $email;

    /** @var string */
    private $password;

    /** @var string|null */
    private $firstname;

    /** @var string|null */
    private $lastname;

    /** @var string */
    private $role;

    /** @var int */
    private $status;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;
        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;
        return $this;
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;
        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Méthode permettant de récupérer un enregistrement de la table app_user en fonction d'un id donné
     *
     * @param int $id ID de la catégorie
     * @return AppUser|null
     */
    public static function find(int $id): ?self
    {
        // se connecter à la BDD
        $pdo = Database::getPDO();

        // écrire notre requête
        $sql = 'SELECT * FROM `app_user` WHERE `id` =' . $id;

        // exécuter notre requête
        $pdoStatement = $pdo->query($sql);

        // un seul résultat => fetchObject
        $user = $pdoStatement->fetchObject(self::class);

        // retourner le résultat
        // return $user ? $user : null;
        return $user ?: null;
    }

    /**
     * Méthode permettant de récupérer tous les enregistrements de la table user
     *
     * @return AppUser[]
     */
    public static function findAll(): array
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT * FROM `app_user`';
        $pdoStatement = $pdo->query($sql);
        $results = $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);

        return $results;
    }

    /**
     * Méthode permettant d'ajouter un enregistrement dans la table user
     * L'objet courant doit contenir toutes les données à ajouter : 1 propriété => 1 colonne dans la table
     *
     * @return bool
     */
    public function insert(): bool
    {
        // Récupération de l'objet PDO représentant la connexion à la DB
        $pdo = Database::getPDO();

        $matchingArray = [
            'email' => $this->email,
            'password' => $this->password,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'role' => $this->role,
            'status' => $this->status,
        ];

        // On prépare notre requête SQL en lui mettant des espèces de points
        // d'ancrage qu'on va vouloir remplacer par des valeurs
        $sql = "INSERT INTO `app_user` (" . implode(', ', array_keys($matchingArray)) . ")
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
     * Méthode permettant de mettre à jour un enregistrement dans la table user
     * L'objet courant doit contenir l'id, et toutes les données à ajouter : 1 propriété => 1 colonne dans la table
     *
     * @return bool
     */
    public function update(): bool
    {
        $matchingArray = [
            'id' => $this->id,
            'email' => $this->email,
            'password' => $this->password,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'role' => $this->role,
            'status' => $this->status,
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
            UPDATE `app_user`
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

        $pdoStatement = $pdo->prepare("DELETE FROM `app_user` WHERE id = :id");

        return $pdoStatement->execute(['id' => $this->id]);
    }
}
