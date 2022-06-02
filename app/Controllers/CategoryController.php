<?php

namespace App\Controllers;

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
        $this->show('category/add');
    }
}
