<div class="admin">
    <!-- --Affichage des erreurs et succès -->
    <?php

    if (! empty($error)) { ?>
        <div class="alert alert-danger">
            <p><?= $error ?></p>
        </div>
    <?php } ?>

    <form class="form" id="form" method="post" enctype="multipart/form-data" action="admin-add-article.php">
        <div class="form-control">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" value="<?= $_POST['title'] ?? '' ?>">
        </div>
        <input type="hidden" name="slug" id="slug" value="<?= $_POST['slug'] ?? ''  ?>">

        <div class="form-control">
            <label for="introduction">Introduction:</label>
            <textarea name="introduction" id="introduction">
                <?= $_POST['introduction'] ?? '' ?>
            </textarea>
        </div>

        <div class="form-control">
            <label for="content">Content:</label>
            <textarea name="content" id="content" class="ckeditor">
                 <?= $_POST['content'] ?? ''  ?>
            </textarea>
        </div>

        <div class="form-control">
            <label for="image">Image de l'article:</label>
            <input type="file" name="image" id="image" accept="image/*">
        </div>
        <div class="form-control">
            <button type="submit" name="add-article" value="add-article">Ajouter</button>
        </div>
    </form>

</div>