<?php

namespace App\Controllers;

use AltoRouter;
use App\Models\Category;

class CategoryController extends CoreController
{
    /**
     * Méthode s'occupant de la page d'accueil
     *
     * @return void
     */
    public function list()
    {
        $categories = Category::findAll();

        $this->show('category/list', [
            'categories' => $categories
        ]);
    }

    public function add()
    {
        $this->displayAddForm();
    }

    public function create()
    {
        global $router;

        /*
            $_POST["name"] ?? ''
                IDENTIQUE À
            isset($_POST["name"]) ? $_POST["name"] : ''
        */
        $name = trim(htmlentities($_POST['name'] ?? ''));
        $subtitle = trim(htmlentities($_POST['subtitle'] ?? ''));
        $picture = filter_input(INPUT_POST, 'picture', FILTER_VALIDATE_URL);

        $errors = self::checkInputs($name, $subtitle, $picture);

        // On va insérer notre Category en BDD
        $category = new Category();
        $category->setName($name);
        $category->setSubtitle($subtitle);
        $category->setPicture($picture);

        // Si j'ai aucune erreur
        if (empty($errors)) {
            // On enregistre en BDD
            if ($category->insert()) {
                header('Location: ' . $router->generate('Category-list'));
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

            $this->displayAddForm($category, $errors);
        }
    }

    /**
     * @param mixed $name
     * @param mixed $subtitle
     * @param mixed $picture
     *
     * @return string[]
     */
    private static function checkInputs($name, $subtitle, $picture): array
    {
        // On va lister toutes les erreurs qu'on va pouvoir rencontrer
        $errors = [];

        if (empty($name)) {
            $errors[] = "Le nom est vide";
        }

        if (empty($subtitle)) {
            $errors[] = "Le sous-titre est vide";
        }

        if ($picture === false) {
            $errors[] = "L'URL d'image est invalide";
        }

        return $errors;
    }

    /**
     * @param Category|null $category Si c'est pas une objet Category, ça peut être null.
     *                                Par défaut, si on donne pas de valeur, ce sera null.
     * @param array         $errors
     */
    private function displayAddForm(?Category $category = null, array $errors = [])
    {
        $this->show('category/add', [
            /*
                Avantage, pas besoin de vérifier partout dans le template que
                $category !== null

                $category ?? new Category()
                    IDENTIQUE À
                isset($category) ? $category : new Category()

                Rappel : isset vérifie si une variable existe, mais si la valeur
                est null, isset considère que ça n'existe pas
                Autres possibilités :
                    !empty($category) ? $category : new Category()
                        OU
                    $category !== null ? $category : new Category()
                        OU
                    $category instanceof Category ? $category : new Category()
                        ETC
            */
            'category' => $category ?? new Category(),
            'errors' => $errors,
        ]);
    }
}
