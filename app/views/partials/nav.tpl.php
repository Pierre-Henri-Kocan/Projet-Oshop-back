<?php
use App\Models\AppUser;
?>

<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= $router->generate('Main-home') ?>">oShop</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="<?= $router->generate('Main-home') ?>">Accueil <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $router->generate('Category-list') ?>">Catégories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $router->generate('Product-list') ?>">Produits</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Types</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Marques</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Tags</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= $router->generate('Category-homeOrder') ?>">Sélection Accueil</a>
                </li>
                <li class="nav-item">
                    <!-- Si les informations de connexion sont dans la session,
                        je sais que l'utilisateur est connecte et on met un lien
                        de déconnexion -->
                    <?php
                    if (!empty($_SESSION['connectedUser']) &&
                        $_SESSION['connectedUser'] instanceof AppUser
                    ) {
                        ?>
                        <a class="nav-link" href="<?= $router->generate('AppUser-logout') ?>">Déconnexion</a>
                        <?php
                    }
                    else {
                        ?>
                        <a class="nav-link" href="<?= $router->generate('AppUser-login') ?>">Connexion</a>
                        <?php
                    }
                    ?>
                </li>
            </ul>
        </div>
    </div>
</nav>