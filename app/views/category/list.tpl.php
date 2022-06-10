<?php
/** @var \App\Models\Category[] $categories */
?>

<a href="<?= $router->generate('Category-add') ?>" class="btn btn-success float-end">Ajouter</a>
<a href="<?= $router->generate('Category-homeOrder') ?>" class="btn btn-success float-end me-2">Catégories en page d'accueil</a>

<h2>Liste des catégories</h2>

<table class="table table-hover mt-4">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Nom</th>
            <th scope="col">Sous-titre</th>
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
                <td><?= $category->getSubtitle() ?></td>
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