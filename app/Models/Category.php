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
}
