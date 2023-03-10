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

    public function form(?int $id = null)
    {
        if ($id !== null) {
            $category = Category::find($id);

            if ($category === false) {
                $this->show404();
            }
        }

        $this->displayRecordForm(
            $id !== null ? $category : null
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
        $name = trim(htmlentities($_POST['name'] ?? ''));
        $subtitle = trim(htmlentities($_POST['subtitle'] ?? ''));
        $picture = filter_input(INPUT_POST, 'picture', FILTER_VALIDATE_URL);

        $errors = self::checkInputs($name, $subtitle, $picture);

        // Il nous faut unr instance de Category
        $category = $id === null ? new Category() : Category::find($id);
        $category->setName($name);
        $category->setSubtitle($subtitle);
        $category->setPicture($picture);

        // Si j'ai aucune erreur
        if (empty($errors)) {
            // On enregistre en BDD
            if ($category->save()) {
                if ($id === null) {
                    // Si la sauvegarde a fonctionné, on redirige vers la liste des catégories.
                    header('Location: '. $GLOBALS["router"]->generate('Category-list'));
                }
                else {
                    // Si la sauvegarde a fonctionné, on redirige vers le formulaire d'édition en mode GET
                    header('Location: '. $GLOBALS["router"]->generate('Category-edit', ['id' => $category->getId()]));
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

            $this->displayRecordForm($category, $errors);
        }
    }

    public function delete(int $id)
    {
        global $router;

        // L'id présent dans l'url se retrouve en paramètre ici
        // On utilise $id pour retrouver la catégorie concernée en DB
        // On obtient un objet de la classe Category
        $category = Category::find($id);

        // On applique la méthode delete() pour demander la suppression en DB de cet objet
        $category->delete();

        // header("Refresh:0");
        header('Location: ' . $router->generate('Category-list'));
    }

    public function homeOrder()
    {
        $this->displayHomeOrderForm();
    }

    public function homeOrderPost()
    {
        global $router;

        // Dans les <select> de la page précédente, tous les attributs name valent
        // «emplacement[]». Ça va donner une indication à PHP de créer une clé
        // `emplacement` dans $_POST. La valeur de $_POST['emplacement'] est un
        // tableau indexé dont chacune des valeurs est l'id de chacune des
        // catégories sélectionnées dans le formulaire
        $categoriesIdInOrder = filter_input(INPUT_POST, 'emplacement', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

        $errors = $this->checkEmplacements($categoriesIdInOrder);

        if (empty($errors)) {
            // On va faire notre enregistrement en DB
            // On a créé une méthode statique dans Category qui définit les home_order en DB
            // On lui envoie la liste des id sélectionnés depuis le formulaire
            // Cette liste est un simple tableau du genre : [5, 7, 12, 3, 19]
            // contenant les id des catégories
            if (Category::defineHomepage($categoriesIdInOrder)) {
                // Rediriger vers le formulaire en GET
                header('Location: ' . $router->generate('Category-homeOrder'));
            }
            else {
                $errors[] = "Échec lors de l'enregistrement";
            }
        }

        // S'il y a au moins une erreur dans les données ou à l'enregistrement
        if (!empty($errors)) {
            // On réaffiche le formulaire, mais pré-rempli avec les (mauvaises)
            // données proposées par l'utilisateur.
            // On transmet aussi le tableau d'erreurs, pour avertir l'utilisateur.

            $this->displayHomeOrderForm($errors);
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
    private function displayRecordForm(?Category $category = null, array $errors = [])
    {
        $this->show('category/record', [
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

    /**
     * Vérifie la liste des emplacement passé en post
     *
     * @param array $emplacements
     *
     * @return array
     */
    private function checkEmplacements(array $emplacements): array
    {
        $errors = [];
        if (!is_array($emplacements)) {
            $errors[] = "Liste des emplacements invalide";
        }
        else {
            foreach ($emplacements as $key => $value) {
                if (!ctype_digit($value)) {
                    $emplacementPosition = $key + 1;

                    $errors[] = "L'emplacement $emplacementPosition a un id invalide";
                }
            }
        }

        // On devrait aussi vérifier qu'on n'a pas la même catégorie à 2 emplacements
        // différents et que les id existent bien en DB et qu'on a bien 5 emplacements
        // remplis, etc

        return $errors;
    }

    private function displayHomeOrderForm(array $errors = [])
    {
        // On récupère bien toutes les catégories car on va en avoir besoin pour
        // remplir nos listes déroulantes
        $categories = Category::findAll();

        $this->show('category/home-order', [
            'categories' => $categories,
            'errors' => $errors,
        ]);
    }
}
