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

// Maintenant, les routes concernant le CRUD
$crudControllers = [
    CategoryController::class => 'categories',
    ProductController::class => 'products',
    AppUserController::class => 'users',
];

foreach ($crudControllers as $controller => $baseUrl) {
    if (!class_exists($controller)) {
        break;
    }

    $controllerShortName = substr(strrchr($controller, '\\'), 1, -10);

    $addUrl  = "/$baseUrl/add";
    $editUrl = "/$baseUrl/[i:id]/edit";

    $formTarget   = ['controller' => $controller, 'method' => 'form'];
    $recordTarget = ['controller' => $controller, 'method' => 'record'];

    if (method_exists($controller, 'list')) {
        $router->map(
            'GET',
            "/$baseUrl",
            [
                'method' => 'list',
                'controller' => $controller
            ],
            "$controllerShortName-list"
        );
    }

    // Les routes d'affichage de formulaire d'ajout ou de modif
    if (method_exists($controller, 'form')) {
        $router->map('GET', $addUrl, $formTarget, "$controllerShortName-add");
        $router->map('GET', $editUrl, $formTarget, "$controllerShortName-edit");
    }

    // Les routes de traitement des formulaires soumis d'ajout ou de modif
    if (method_exists($controller, 'record')) {
        $router->map('POST', $addUrl, $recordTarget, "$controllerShortName-create");
        $router->map('POST', $editUrl, $recordTarget, "$controllerShortName-update");
    }

    if (method_exists($controller, 'delete')) {
        $router->map(
            'GET',
            "/$baseUrl/[i:id]/delete",
            [
                'method' => 'delete',
                'controller' => $controller
            ],
            "$controllerShortName-delete"
        );
    }
}

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
