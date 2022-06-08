<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Product;

class MainController extends CoreController
{
    /**
     * Méthode s'occupant de la page d'accueil
     *
     * @return void
     */
    public function home()
    {
        // Est-ce que t'as le droit d'être là ?
        $this->checkAuthorization([self::ROLE_ADMIN, self::ROLE_CATALOG_MANAGER]);

        $categories = Category::findWithLimit(5);
        $products = Product::findWithLimit(5);

        // On appelle la méthode show() de l'objet courant
        // En argument, on fournit le fichier de Vue
        // Par convention, chaque fichier de vue sera dans un sous-dossier du nom du Controller
        $this->show('main/home', [
            'categories' => $categories,
            'products' => $products,
        ]);
    }
}
