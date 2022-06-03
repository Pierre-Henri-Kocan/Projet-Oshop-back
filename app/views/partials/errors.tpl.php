<?php

/** @var string[] $errors */

if (isset($errors) && is_array($errors)) {
    foreach ($errors as $error) {
        ?>
        <div class="alert alert-danger"><?= $error ?></div>
        <?php
    }
}

?>