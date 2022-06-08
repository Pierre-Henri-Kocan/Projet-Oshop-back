<?php

namespace App\Controllers;

use App\Models\AppUser;

abstract class CoreController
{
    protected const ROLE_ADMIN = "admin";
    protected const ROLE_CATALOG_MANAGER = "catalog-manager";

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
        }
    }
}
