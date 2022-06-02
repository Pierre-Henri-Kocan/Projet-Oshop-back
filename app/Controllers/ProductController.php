<?php

namespace App\Controllers;

use App\Models\Product;

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

    public function add()
    {
        $this->show('product/add');
    }
}
