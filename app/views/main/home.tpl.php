<?php

/** @var \App\Models\Category[] $categories */
/** @var \App\Models\Product[] $products */
?>

<p class="display-4">
    Bienvenue dans le backOffice <strong>Dans les shoe</strong>...
</p>

<div class="row mt-5">
    <div class="col-12 col-md-6">
        <div class="card text-white mb-3">
            <div class="card-header bg-primary">Liste des catégories</div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nom</th>
                            <th scope="col">Order</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($categories as $category) {
                        ?>
                            <tr>
                                <th scope="row"><?= $category->getId() ?></th>
                                <td><?= $category->getName() ?></td>
                                <td><?= $category->getHomeOrder() ?></td>
                                <td class="text-end">
                                    <a href="<?= $router->generate('Category-edit', ['id' => $category->getId()]) ?>" class="btn btn-sm btn-warning">
                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                    </a>
                                    <!-- Example single danger button -->
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-trash-o" aria-hidden="true"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item"
                                               href="<?= $router->generate('Category-delete', ['id' => $category->getId()]) ?>?token=<?= $_SESSION['token'] ?>">
                                               Oui, je veux supprimer
                                            </a>
                                            <a class="dropdown-item" href="#" data-toggle="dropdown">Oups !</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <div class="d-grid gap-2">
                    <a href="<?= $router->generate('Category-list') ?>" class="btn btn-success">Voir plus</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-6">
        <div class="card text-white mb-3">
            <div class="card-header bg-primary">Liste des produits</div>
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nom</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        foreach ($products as $product) {
                        ?>
                        <tr>
                            <th scope="row"><?= $product->getId() ?></th>
                            <td><?= $product->getName() ?></td>
                            <td class="text-end">
                                <a href="<?= $router->generate('Product-edit', ['id' => $product->getId()]) ?>" class="btn btn-sm btn-warning">
                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                </a>
                                <!-- Example single danger button -->
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-danger dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-trash-o" aria-hidden="true"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item"
                                           href="<?= $router->generate('Product-delete', ['id' => $product->getId()]) ?>?token=<?= $_SESSION['token'] ?>">
                                           Oui, je veux supprimer
                                        </a>
                                        <a class="dropdown-item" href="#" data-toggle="dropdown">Oups !</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <div class="d-grid gap-2">
                    <a href="<?= $router->generate('Product-list') ?>" class="btn btn-success">Voir plus</a>
                </div>
            </div>
        </div>
    </div>
</div>