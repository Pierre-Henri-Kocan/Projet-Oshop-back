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
    /** @var string */
    protected $name = '';

    /** @var string|null */
    protected $description;

    /** @var string|null */
    protected $picture;

    /** @var float */
    protected $price = 0;

    /** @var int */
    protected $rate = 0;

    /** @var int */
    protected $status = 0;

    /** @var int|null */
    protected $brand_id;

    /** @var int|null */
    protected $category_id;

    /** @var int|null */
    protected $type_id;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
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

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getRate(): int
    {
        return $this->rate;
    }

    public function setRate(int $rate): self
    {
        $this->rate = $rate;
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

    public function getBrandId(): ?int
    {
        return $this->brand_id;
    }

    public function setBrandId(int $brand_id): self
    {
        $this->brand_id = $brand_id;
        return $this;
    }

    public function getCategoryId(): ?int
    {
        return $this->category_id;
    }

    public function setCategoryId(?int $category_id): self
    {
        $this->category_id = $category_id;
        return $this;
    }

    public function getTypeId(): ?int
    {
        return $this->type_id;
    }

    public function setTypeId(int $type_id): self
    {
        $this->type_id = $type_id;
        return $this;
    }

    protected static function tableName(): string
    {
        return 'product';
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

        $table = self::tableName();

        $pdo = Database::getPDO();
        $sql = "
            SELECT *
            FROM $table
            WHERE status = 1
            LIMIT $limit
        ";
        $pdoStatement = $pdo->query($sql);
        $results = $pdoStatement->fetchAll(PDO::FETCH_CLASS, self::class);

        return $results;
    }
}
