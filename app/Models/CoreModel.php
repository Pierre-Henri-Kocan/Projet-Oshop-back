<?php

namespace App\Models;

// Classe mère de tous les Models
// On centralise ici toutes les propriétés et méthodes utiles pour TOUS les Models
abstract class CoreModel
{
    /**
     * @var int
     */
    protected $id;
    /**
     * @var string
     */
    protected $created_at;
    /**
     * @var string
     */
    protected $updated_at;

    /**
     * Retourne l'objet correspondant à l'id en paramètre
     *
     * @param int $id
     *
     * @return $this|null
     */
    abstract public static function find(int $id): ?self;

    /**
     * Retourne la liste complète d'objets
     *
     * @return $this[]
     */
    abstract public static function findAll(): array;

    /**
     * Ajoute l'objet en DB
     *
     * @return bool
     */
    abstract public function insert(): bool;

    /**
     * Modifie l'objet en DB
     *
     * @return bool
     */
    abstract public function update(): bool;

    /**
     * Supprime l'objet de la DB
     *
     * @return bool
     */
    abstract public function delete(): bool;

    /**
     * Get the value of id
     *
     * @return  int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Get the value of created_at
     *
     * @return  string
     */
    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    /**
     * Get the value of updated_at
     *
     * @return  string
     */
    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }

    public function save(): bool
    {
        if ($this->getId() === null) {
            return $this->insert();
        }
        else {
            return $this->update();
        }
    }
}
