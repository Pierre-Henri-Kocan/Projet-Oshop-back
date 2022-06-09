<?php
/** @var \App\Models\Product $product */
/** @var string[] $errors */
?>

<a href="<?= $router->generate('Product-list') ?>" class="btn btn-success float-end">Retour</a>

<?php
if ($product->getId() > 0) {
    ?>
    <h2>Modifier le produit #<?= $product->getId() ?></h2>
    <?php
}
else {
    ?>
    <h2>Ajouter un produit</h2>
    <?php
}
?>

<form action="" method="POST" class="mt-5">
    <?php
    // Pour afficher les messages d'erreurs éventuels.
    include __DIR__ . '/../partials/errors.tpl.php';
    ?>

    <div class="mb-3">
        <label for="name" class="form-label">Nom</label>
        <input type="text"
               class="form-control"
               id="name"
               name="name"
               value="<?= $product->getName() ?>"
               placeholder="Nom du produit">
    </div>

    <div class="mb-3">
        <label for="subtitle" class="form-label">Description</label>
        <textarea id="description"
                  name="description"
                  class="form-control"
                  placeholder="Description"
                  rows="5"><?= $product->getDescription() ?></textarea>

        <small id="subtitleHelpBlock" class="form-text text-muted">
            Sera affiché sur la page d'accueil comme bouton devant l'image
        </small>
    </div>

    <div class="mb-3">
        <label for="picture" class="form-label">Image</label>
        <input type="text"
               class="form-control"
               id="picture"
               name="picture"
               value="<?= $product->getPicture() ?>"
               placeholder="image jpg, gif, svg, png"
               aria-describedby="pictureHelpBlock">

        <small id="pictureHelpBlock" class="form-text text-muted">
            URL relative d'une image (jpg, gif, svg ou png) fournie sur
            <a href="https://benoclock.github.io/S06-images/" target="_blank">cette page</a>
        </small>
    </div>

    <div class="mb-3">
        <label for="price" class="form-label">Prix <small>(en €)</small></label>
        <input type="text"
               class="form-control"
               id="price"
               name="price"
               value="<?= $product->getPrice() ?>"
               placeholder="Prix">
    </div>

    <div class="mb-3">
        <label for="rate" class="form-label">Note</label>
        <select id="rate" name="rate" class="form-control">
            <option value="">Choisir une note</option>
            <?php
            for ($index=1; $index <= 5; $index++) {
                ?>
                <option value="<?= $index ?>" <?= $index == $product->getRate() ? "selected" : "" ?>>
                    <?= $index ?>
                </option>
                <?php
            }
            ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select id="status" name="status" class="form-control">
            <option value="1" <?= 1 == $product->getStatus() ? "selected" : "" ?>>Disponible</option>
            <option value="2" <?= 2 == $product->getStatus() ? "selected" : "" ?>>Indisponible</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="category_id" class="form-label">Catégorie</label>
        <select id="category_id" name="category_id" class="form-control">
            <option value="">Choisir une catégorie</option>
            <?php
            foreach ($categories as $category) {
                ?>
                <option value="<?= $category->getId() ?>"
                        <?= $category->getId() == $product->getCategoryId() ? "selected" : "" ?>>
                    <?= $category->getName() ?>
                </option>
                <?php
            }
            ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="brand_id" class="form-label">Marque</label>
        <select id="brand_id" name="brand_id" class="form-control">
            <option value="">Choisir une marque</option>
            <?php
            foreach ($brands as $brand) {
                ?>
                <option value="<?= $brand->getId() ?>"
                        <?= $brand->getId() == $product->getBrandId() ? "selected" : "" ?>>
                    <?= $brand->getName() ?>
                </option>
                <?php
            }
            ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="type_id" class="form-label">Type</label>
        <select id="type_id" name="type_id" class="form-control">
            <option value="">Choisir un type</option>
            <?php
            foreach ($types as $type) {
                ?>
                <option value="<?= $type->getId() ?>"
                        <?= $type->getId() == $product->getTypeId() ? "selected" : "" ?>>
                    <?= $type->getName() ?>
                </option>
                <?php
            }
            ?>
        </select>
    </div>

    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary mt-5">Valider</button>
    </div>

    <input type="hidden" name="token" value="<?= $_SESSION['token'] ?>">
</form>