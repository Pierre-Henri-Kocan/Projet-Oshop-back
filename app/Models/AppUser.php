<?php

namespace App\Models;

use App\Utils\Database;

/**
 * Un modèle représente une table (un entité) dans notre base
 *
 * Un objet issu de cette classe réprésente un enregistrement dans cette table
 */
class AppUser extends CoreModel
{
    /** @var string */
    protected $email = '';

    /** @var string */
    protected $password = '';

    /** @var string|null */
    protected $firstname;

    /** @var string|null */
    protected $lastname;

    /** @var string */
    protected $role = '';

    /** @var int */
    protected $status = 0;

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

    protected static function tableName(): string
    {
        return 'app_user';
    }

    /**
     * Retourne un user à partir de son email
     *
     * @param string $email
     *
     * @return $this|null
     */
    public static function findByEmail(string $email): ?self
    {
        $pdo = Database::getPDO();

        $table = self::tableName();

        $pdoStatement = $pdo->prepare("SELECT * FROM `$table` WHERE `email` = :email");
        $pdoStatement->execute(['email' => $email]);

        return $pdoStatement->fetchObject(self::class) ?: null;
    }
}
