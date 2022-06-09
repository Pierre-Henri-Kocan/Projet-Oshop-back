<?php

namespace App\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Type;

class ProductController extends CoreController
{
    /**
     * Méthode s'occupant de la page d'accueil
     *
     * @return void
     */
    public function list()
    {
        $this->show('product/list', [
            'products' => Product::findAll()
        ]);
    }

    public function form(?int $id = null)
    {
        // Dans le cas d'un add, id vaut null grace à la valeur apr défaut renseigné
        // dans la signature du parametre
        // Dans le cas d'un edit, AltoRouter à passer l'id de l'url en paramètre
        // donc $is vaut l'id de l'URL
        if ($id !== null) {
            $product = Product::find($id);

            if ($product === false) {
                $this->show404();
            }
        }

        $this->displayRecordForm(
            $id !== null ? $product : null
        );
    }

    public function record(?int $id = null)
    {
        global $router;

        /*
            $_POST["name"] ?? ''
                IDENTIQUE À
            isset($_POST["name"]) ? $_POST["name"] : ''
        */
        $name = trim(htmlspecialchars($_POST["name"] ?? ''));
        $description = trim(htmlspecialchars($_POST["description"] ?? ''));
        $picture = filter_input(INPUT_POST, 'picture', FILTER_VALIDATE_URL);
        $price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]]);
        $rate = filter_input(INPUT_POST, 'rate', FILTER_VALIDATE_INT);
        $status = filter_input(INPUT_POST, 'status', FILTER_VALIDATE_INT);
        $brand_id = filter_input(INPUT_POST, 'brand_id', FILTER_VALIDATE_INT);
        $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
        $type_id = filter_input(INPUT_POST, 'type_id', FILTER_VALIDATE_INT);

        $errors = self::checkInputs(
            $name,
            $description,
            $picture,
            $price,
            $rate,
            $status,
            $brand_id,
            $category_id,
            $type_id
        );

        // On va insérer notre Product en BDD
        $product = $id === null ? new Product() : Product::find($id);
        $product->setName($name);
        $product->setDescription($description);
        $product->setPicture($picture);
        $product->setPrice((float)$price);
        $product->setRate((int)$rate);
        $product->setStatus((int)$status);
        $product->setBrandId((int)$brand_id);
        $product->setCategoryId((int)$category_id);
        $product->setTypeId((int)$type_id);

        // Si j'ai aucune erreur
        if (empty($errors)) {
            // On enregistre en BDD
            if ($product->save()) {
                if ($id === null) {
                    // Si la sauvegarde a fonctionné, on redirige vers la liste des catégories.
                    header('Location: '. $router->generate('Product-list'));
                }
                else {
                    // Si la sauvegarde a fonctionné, on redirige vers le formulaire d'édition en mode GET
                    header('Location: '. $router->generate('Product-edit', ['id' => $product->getId()]));
                }
            }
            else {
                $errors[] = "La sauvegarde a échoué";
            }
        }

        // S'il y a au moins une erreur dans les données ou à l'enregistrement
        if (!empty($errors)) {
            // On réaffiche le formulaire, mais pré-rempli avec les (mauvaises)
            // données proposées par l'utilisateur.
            // On transmet aussi le tableau d'erreurs, pour avertir l'utilisateur.

            $this->displayRecordForm($product, $errors);
        }
    }

    public function delete(int $id)
    {
        global $router;

        // L'id présent dans l'url se retrouve en paramètre ici
        // On utilise $id pour retrouver la catégorie concernée en DB
        // On obtient un objet de la classe Product
        $product = Product::find($id);

        // On applique la méthode delete() pour demander la suppression en DB de cet objet
        $product->delete();

        header('Location: ' . $router->generate('Product-list'));
    }

    /**
     * @param mixed $name
     * @param mixed $description
     * @param mixed $picture
     * @param mixed $price
     * @param mixed $rate
     * @param mixed $status
     * @param mixed $brand_id
     * @param mixed $category_id
     * @param mixed $type_id
     *
     * @return string[]
     */
    private static function checkInputs(
        $name,
        $description,
        $picture,
        $price,
        $rate,
        $status,
        $brand_id,
        $category_id,
        $type_id
    ): array {
        $errors = [];

        if (empty($name)) {
            $errors[] = 'Le nom est vide';
        }

        if ($name === false) {
            $errors[] = 'Le nom est invalide';
        }

        // Pareil pour la "description".
        if (empty($description)) {
            $errors[] = 'La description est vide';
        }

        if ($description === false) {
            $errors[] = 'La description est invalide';
        }

        // Pour l'URL de l'image "picture", le filtre vérifie forcément sa présence aussi.
        if ($picture === false) {
            $errors[] = 'L\'URL d\'image est invalide';
        }

        // Etc.
        if ($price === false) {
            $errors[] = 'Le prix est invalide';
        }

        if ($rate === false) {
            $errors[] = 'La note est invalide';
        }

        if ($status === false) {
            $errors[] = 'Le statut est invalide';
        }

        if ($brand_id === false) {
            $errors[] = 'La marque est invalide';
        }

        if ($category_id === false) {
            $errors[] = 'La catégorie est invalide';
        }

        if ($type_id === false) {
            $errors[] = 'Le type est invalide';
        }
        // NOTE: clairement, ces validations ne sont pas suffisantes
        // (ex. relations par clé étrangère : comment vérifier que les autres ressources
        // existent vraiment ?)

        return $errors;
    }

    /**
     * @param Product|null $product Si c'est pas une objet Product, ça peut être null.
     *                              Par défaut, si on donne pas de valeur, ce sera null.
     * @param array         $errors
     */
    private function displayRecordForm(?Product $product = null, array $errors = [])
    {
        $this->show('product/record', [
            /*
                Avantage, pas besoin de vérifier partout dans le template que
                $product !== null

                $product ?? new Product()
                    IDENTIQUE À
                isset($product) ? $product : new Product()

                Rappel : isset vérifie si une variable existe, mais si la valeur
                est null, isset considère que ça n'existe pas
                Autres possibilités :
                    !empty($product) ? $product : new Product()
                        OU
                    $product !== null ? $product : new Product()
                        OU
                    $product instanceof Product ? $product : new Product()
                        ETC
            */
            'product' => $product ?? new Product(),
            'errors' => $errors,
            'categories' => Category::findAll(),
            'brands' => Brand::findAll(),
            'types' => Type::findAll(),
        ]);
    }
}
