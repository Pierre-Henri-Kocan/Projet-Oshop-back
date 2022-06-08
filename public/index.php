<?php

// POINT D'ENTRÉE UNIQUE :
// FrontController

// inclusion des dépendances via Composer
// autoload.php permet de charger d'un coup toutes les dépendances installées avec composer
// mais aussi d'activer le chargement automatique des classes (convention PSR-4)

require_once '../vendor/autoload.php';

session_start();

use App\Controllers\AppUserController;
use App\Controllers\CategoryController;
use App\Controllers\ErrorController;
use App\Controllers\MainController;
use App\Controllers\ProductController;

/* ------------
--- ROUTAGE ---
-------------*/


// création de l'objet router
// Cet objet va gérer les routes pour nous, et surtout il va
$router = new AltoRouter();

// le répertoire (après le nom de domaine) dans lequel on travaille est celui-ci
// Mais on pourrait travailler sans sous-répertoire
// Si il y a un sous-répertoire
if (array_key_exists('BASE_URI', $_SERVER)) {
    // Alors on définit le basePath d'AltoRouter
    $router->setBasePath($_SERVER['BASE_URI']);
    // ainsi, nos routes correspondront à l'URL, après la suite de sous-répertoire
} else { // sinon
    // On donne une valeur par défaut à $_SERVER['BASE_URI'] car c'est utilisé dans le CoreController
    $_SERVER['BASE_URI'] = '/';
}

// On doit déclarer toutes les "routes" à AltoRouter,
// afin qu'il puisse nous donner LA "route" correspondante à l'URL courante
// On appelle cela "mapper" les routes
// 1. méthode HTTP : GET ou POST (pour résumer)
// 2. La route : la portion d'URL après le basePath
// 3. Target/Cible : informations contenant
//      - le nom de la méthode à utiliser pour répondre à cette route
//      - le nom du controller contenant la méthode
// 4. Le nom de la route : pour identifier la route, on va suivre une convention
//      - "NomDuController-NomDeLaMéthode"
//      - ainsi pour la route /, méthode "home" du MainController => "Main-home"

$router->map(
    'GET',
    '/',
    [
        'method' => 'home',
        'controller' => MainController::class
    ],
    'Main-home'
);

$router->map(
    'GET',
    '/categories',
    [
        'method' => 'list',
        'controller' => CategoryController::class
    ],
    'Category-list'
);

$router->map(
    'GET',
    '/categories/add',
    [
        'method' => 'form',
        'controller' => CategoryController::class
    ],
    'Category-add'
);

$router->map(
    'GET',
    '/categories/[i:id]/edit',
    [
        'method' => 'form',
        'controller' => CategoryController::class
    ],
    'Category-edit'
);

$router->map(
    'POST',
    '/categories/add',
    [
        'method' => 'record',
        'controller' => CategoryController::class
    ],
    'Category-create'
);

$router->map(
    'POST',
    '/categories/[i:id]/edit',
    [
        'method' => 'record',
        'controller' => CategoryController::class
    ],
    'Category-update'
);

$router->map(
    'GET',
    '/products',
    [
        'method' => 'list',
        'controller' => ProductController::class
    ],
    'Product-list'
);

$router->map(
    'GET',
    '/products/add',
    [
        'method' => 'form',
        'controller' => ProductController::class
    ],
    'Product-add'
);

$router->map(
    'GET',
    '/products/[i:id]/edit',
    [
        'method' => 'form',
        'controller' => ProductController::class
    ],
    'Product-edit'
);

$router->map(
    'POST',
    '/products/add',
    [
        'method' => 'record',
        'controller' => ProductController::class
    ],
    'Product-create'
);

$router->map(
    'POST',
    '/products/[i:id]/edit',
    [
        'method' => 'record',
        'controller' => ProductController::class
    ],
    'Product-update'
);

$router->map(
    'GET',
    '/login',
    [
        'method' => 'login',
        'controller' => AppUserController::class
    ],
    'AppUser-login'
);

$router->map(
    'POST',
    '/login',
    [
        'method' => 'loginPost',
        'controller' => AppUserController::class
    ],
    'AppUser-loginPost'
);

$router->map(
    'GET',
    '/logout',
    [
        'method' => 'logout',
        'controller' => AppUserController::class
    ],
    'AppUser-logout'
);

/* -------------
--- DISPATCH ---
--------------*/

// On demande à AltoRouter de trouver une route qui correspond à l'URL courante
$match = $router->match();

// Ensuite, pour dispatcher le code dans la bonne méthode, du bon Controller
// On délègue à une librairie externe : https://packagist.org/packages/benoclock/alto-dispatcher
// 1er argument : la variable $match retournée par AltoRouter
// 2e argument : le "target" (controller & méthode) pour afficher la page 404
$dispatcher = new Dispatcher($match, ErrorController::class . '::err404');
// Une fois le "dispatcher" configuré, on lance le dispatch qui va exécuter la méthode du controller
$dispatcher->dispatch();
