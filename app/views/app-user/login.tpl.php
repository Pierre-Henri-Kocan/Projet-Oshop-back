<?php
/** @var \App\Models\AppUser $user */
/** @var string[] $errors */
?>

<div class="row">
    <div class="col-12">
        <h1>Connexion</h1>
    </div>
</div>

<div class="row">
    <form class="col-12" method="post">
        <?php
        // Pour afficher les messages d'erreurs éventuels.
        include __DIR__ . '/../partials/errors.tpl.php';
        ?>

        <!-- my-4 : margin sur l'axe y (vertical) numéro 4 -->
        <input type="email"
               class="form-control
               my-4"
               name="email"
               value="<?= $user->getEmail() ?>"
               placeholder="E-mail">

        <input type="password"
               class="form-control
               my-4"
               name="password"
               value="<?= $user->getPassword() ?>"
               placeholder="Mot de passe">

        <button class="btn btn-success">Se connecter</button>
    </form>
</div>