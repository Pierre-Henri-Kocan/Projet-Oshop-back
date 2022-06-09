<?php

namespace App\Controllers;

use App\Models\AppUser;

abstract class CoreController
{
    protected const ROLE_ADMIN = "admin";
    protected const ROLE_CATALOG_MANAGER = "catalog-manager";

    public function __construct()
    {
        // La variable $match contient les infos sur la route courante
        global $match;

        if ($match === false) {
            // On sort de l'exécution du constructeur grace à return
            return false;
        }

        // On récupère le nom de la route courante
        // On va se servir du nom de la route demandée pour la faire coincider avec les ACL
        $routerName = $match['name'];

        // On définit la liste des permissions pour les routes nécessitant une connexion utilisateur
        $acl = [
            'Main-home'       => [self::ROLE_ADMIN, self::ROLE_CATALOG_MANAGER],
            'AppUser-list'    => [self::ROLE_ADMIN],
            'AppUser-add'     => [self::ROLE_ADMIN],
            'AppUser-create'  => [self::ROLE_ADMIN],
            'AppUser-edit'    => [self::ROLE_ADMIN],
            'AppUser-update'  => [self::ROLE_ADMIN],
            'Category-list'   => [self::ROLE_ADMIN, self::ROLE_CATALOG_MANAGER],
            'Category-add'    => [self::ROLE_ADMIN, self::ROLE_CATALOG_MANAGER],
            'Category-create' => [self::ROLE_ADMIN, self::ROLE_CATALOG_MANAGER],
            'Category-edit'   => [self::ROLE_ADMIN, self::ROLE_CATALOG_MANAGER],
            'Category-update' => [self::ROLE_ADMIN, self::ROLE_CATALOG_MANAGER],
            'Category-delete' => [self::ROLE_ADMIN, self::ROLE_CATALOG_MANAGER],
            'Product-list'    => [self::ROLE_ADMIN, self::ROLE_CATALOG_MANAGER],
            'Product-add'     => [self::ROLE_ADMIN, self::ROLE_CATALOG_MANAGER],
            'Product-create'  => [self::ROLE_ADMIN, self::ROLE_CATALOG_MANAGER],
            'Product-edit'    => [self::ROLE_ADMIN, self::ROLE_CATALOG_MANAGER],
            'Product-update'  => [self::ROLE_ADMIN, self::ROLE_CATALOG_MANAGER],
            'Product-delete'  => [self::ROLE_ADMIN, self::ROLE_CATALOG_MANAGER],
        ];

        // On vérifie si notre route courante est présente dans $acl.
        // Si c'est le cas, alors on vérifie la permission de l'utilisateur
        if (isset($acl[$routerName])) {
            $this->checkAuthorization($acl[$routerName]);
        }
        // if (array_key_exists($routerName, $acl)) {
        //     $this->checkAuthorization($acl[$routerName]);
        // }

        //-----
        // Token anti-CSRF
        //-----
        /*
            On liste les routes pour lesquelles on va devoir faire une vérification CSRF
            (toutes les routes qui traitent la soumission d'un formulaire et les routes de delete)
        */
        $routesNeedingCsrfCheck = [
            'Category-create',
            'Category-update',
            'Category-delete',
            'Product-create',
            'Product-update',
            'Product-delete',
            'AppUser-create',
            'AppUser-update',
            'AppUser-delete',
        ];

        /*
            Si on n'est pas sur une route traitant la soumission d'un formulaire,
                on crée le token CSRF qu'on met en session.
            Sinon on va vérifier que le token soumis correspond au token attendu
                (celui en session).
        */
        if (!in_array($routerName, $routesNeedingCsrfCheck)) {
            // Désormais, pour se prémunir d'une potentielle attaque de type CSRF
            // Il faut générer un token, et pour ça on choisit la logique qu'on veut
            $_SESSION['token'] = bin2hex(random_bytes(32));
        }
        else {
            // On récupère le token en POST ou en GET
            // Si $_POST['token'] existe, alors on le récupère; sinon si $_GET['token']
            // existe, alors on le récupère, sinon on met une chaine vide
            $token = $_POST['token'] ?? $_GET['token'] ?? '';
            // $token = $_POST['token'] ?? '';
            // if ($token === '') {
            //     $token = $_GET['token'] ?? '';
            // }

            // On récupère le token en SESSION
            $sessionToken = $_SESSION['token'] ?? '';

            // Si les deux tokens sont différents ou que le token du formulaire est vide
            if (empty($token) || $token !== $sessionToken) {
                // Alors on affiche une 403
                $this->show403();
            }
            else {
                // Si tout va bien
                // On remplace le token en session
                // Ainsi, on ne pourra pas réutiliser ce token
                $_SESSION['token'] = bin2hex(random_bytes(32));
            }
        }
    }

    /**
     * Méthode permettant d'afficher du code HTML en se basant sur les views
     *
     * @param string $viewName Nom du fichier de vue
     * @param array $viewData Tableau des données à transmettre aux vues
     * @return void
     */
    protected function show(string $viewName, $viewData = [])
    {
        // On globalise $router car on ne sait pas faire mieux pour l'instant
        global $router;

        // Comme $viewData est déclarée comme paramètre de la méthode show()
        // les vues y ont accès
        // ici une valeur dont on a besoin sur TOUTES les vues
        // donc on la définit dans show()
        $viewData['currentPage'] = $viewName;

        // définir l'url absolue pour nos assets
        $viewData['assetsBaseUri'] = $_SERVER['BASE_URI'] . 'assets/';
        // définir l'url absolue pour la racine du site
        // /!\ != racine projet, ici on parle du répertoire public/
        $viewData['baseUri'] = $_SERVER['BASE_URI'];

        // On veut désormais accéder aux données de $viewData, mais sans accéder au tableau
        // La fonction extract permet de créer une variable pour chaque élément du tableau passé en argument
        extract($viewData);
        // => la variable $currentPage existe désormais, et sa valeur est $viewName
        // => la variable $assetsBaseUri existe désormais, et sa valeur est $_SERVER['BASE_URI'] . '/assets/'
        // => la variable $baseUri existe désormais, et sa valeur est $_SERVER['BASE_URI']
        // => il en va de même pour chaque élément du tableau

        // $viewData est disponible dans chaque fichier de vue
        require_once __DIR__ . '/../views/layout/header.tpl.php';
        require_once __DIR__ . '/../views/' . $viewName . '.tpl.php';
        require_once __DIR__ . '/../views/layout/footer.tpl.php';
    }

    /**
     * Lève une erreur 404 en affichant la vue correspondante et stop le code PHP
     */
    protected function show404()
    {
        header('HTTP/1.0 404 Not Found');
        $this->show('error/err404');
        exit;
    }

    /**
     * Lève une erreur 403 en affichant la vue correspondante et stop le code PHP
     */
    protected function show403()
    {
        // On envoie le header "403 Forbidden"
        http_response_code(403);

        // Puis on affiche la page d'erreur 403
        $this->show('error/err403');
        exit;
    }

    /**
     * Vérifie que l'utilisateur connecté à le role necéssaire pour accèder à la
     * page courante.
     * Affiche une erreur 403 en cas de non autorisation.
     *
     * @param string[] $roles Liste des rôles ayant l'autorisation nécessaire
     */
    protected function checkAuthorization(array $roles)
    {
        // Si le user est connecté
        if (!empty($_SESSION['connectedUser']) &&
            $_SESSION['connectedUser'] instanceof AppUser
        ) {
            // Récupérer l'utilisateur connecté (en session)
            /** @var AppUser $connectedUser */
            $connectedUser = $_SESSION['connectedUser'];

            // Puis on récupère son rôle
            $userRole = $connectedUser->getRole();

            // Est-ce que son rôle est dans la liste des rôles autorisé (ceux en paramètre)
            if (in_array($userRole, $roles, true)) {
                // Si oui, alors on retourne true
                return true;
            }
            else {
                // Sinon on va mettre une erreur "403 Forbidden"
                $this->show403();
            }
        }
        else {
            // Sinon, on redirige l'utilisateur vers la page de connexion
            global $router;
            header('Location: ' . $router->generate('AppUser-login'));
            exit;
        }
    }
}
