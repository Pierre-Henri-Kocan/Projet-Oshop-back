<?php
/** @var \App\Models\AppUser $user */
/** @var string[] $errors */
?>

<a href="<?= $router->generate('AppUser-list') ?>" class="btn btn-success float-end">Retour</a>

<?php
if ($user->getId() > 0) {
    ?>
    <h2>Modifier l'utilisateur #<?= $user->getId() ?></h2>
    <?php
}
else {
    ?>
    <h2>Ajouter un utilisateur</h2>
    <?php
}
?>

<form action="" method="POST" class="mt-5">
    <?php
    // Pour afficher les messages d'erreurs éventuels.
    include __DIR__ . '/../partials/errors.tpl.php';
    ?>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="text"
               class="form-control"
               id="email"
               name="email"
               value="<?= $user->getEmail() ?>"
               placeholder="Email">
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Mot de passe</label>
        <input type="text"
               class="form-control"
               id="password"
               name="password"
               placeholder="Mot de passe">
    </div>

    <div class="mb-3">
        <label for="lastname" class="form-label">Nom de famille</label>
        <input type="text"
               class="form-control"
               id="lastname"
               name="lastname"
               value="<?= $user->getLastname() ?>"
               placeholder="Nom de famille">
    </div>

    <div class="mb-3">
        <label for="firstname" class="form-label">Prénom</label>
        <input type="text"
               class="form-control"
               id="firstname"
               name="firstname"
               value="<?= $user->getFirstname() ?>"
               placeholder="Prénom">
    </div>

    <div class="mb-3">
        <label for="role" class="form-label">Rôle</label>
        <select id="role" name="role" class="form-control">
            <option value="catalog-namager" <?= $user->getRole() === 'catalog-namager' ? "selected" : "" ?>>
                Catalog Manager
            </option>
            <option value="admin" <?= $user->getRole() === 'admin' ? "selected" : "" ?>>
                Admin
            </option>
        </select>
    </div>

    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select id="status" name="status" class="form-control">
            <option value="1" <?= 1 == $user->getStatus() ? "selected" : "" ?>>Actif</option>
            <option value="2" <?= 2 == $user->getStatus() ? "selected" : "" ?>>Inactif</option>
        </select>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary mt-5">Valider</button>
    </div>

    <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
</form>