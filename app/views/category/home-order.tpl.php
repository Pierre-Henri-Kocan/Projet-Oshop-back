<?php
/** @var \App\Models\Category[] $categories */
/** @var string[] $errors */
?>

<div class="row">
    <div class="col-12 my-4">
        <a href="<?= $router->generate('Category-list') ?>" class="btn btn-success float-end">Retour</a>
        <h2>Gestion de la page d'accueil</h2>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <form id="home-order-form" action="" method="POST" class="mt-5">
            <?php
            // Pour afficher les messages d'erreurs éventuels.
            include __DIR__ . '/../partials/errors.tpl.php';
            ?>

            <div class="row">

                <?php
                for ($index = 1; $index <= 5; $index++) {
                    ?>
                    <div class="col">
                        <div class="form-group">
                            <label for="emplacement<?= $index ?>">Emplacement #<?= $index ?></label>
                            <select class="form-control" id="emplacement<?= $index ?>" name="emplacement[]">
                                <option value="">Choisissez :</option>
                                <?php
                                foreach ($categories as $category) {
                                    ?>
                                    <option value="<?= $category->getId() ?>"
                                            <?= $category->getHomeOrder() === $index ? 'selected' : '' ?>>
                                        <?= $category->getName() ?>
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <?php
                }
                ?>

            </div>

            <button type="submit" class="btn btn-primary btn-block mt-5">Valider</button>

            <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
        </form>
    </div>
</div>

<script src="/assets/js/home-order.js"></script>