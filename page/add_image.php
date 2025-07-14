<?php
require "../include/fonction.php";
$image = $_GET['id_objet'];

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Document</title>
</head>
<body>
<?php include "../include/header.php"; ?>

<div class="container mt-4">
    <h1>Ajouter une image</h1>
    <?php
    $images = get_images_objet($image);
    if ($images && is_array($images)) {
        foreach ($images as $img) {
            if (!empty($img['nom_image']) && !empty($img['id_image'])) {
                ?>
                <div style="display:inline-block; margin:5px;">
                    <img src="../assets/image/<?= $img['nom_image'] ?>" style="width:60px;">
                    <a href="delete_image.php?id_image=<?= $img['id_image'] ?>&id_objet=<?= $image ?>" onclick="return confirm('Supprimer cette image ?')">Supprimer</a>
                </div>
                <?php
            }
        }
    } else {
        echo "<p>Aucune image trouvée.</p>";
    }
    ?>
    <form action="traitement_photo.php" method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="fichier" class="form-label">Choisir une image</label>
            <input type="hidden" name="id_img" value="<?= $image ?>">
            <input type="file" class="form-control" id="fichier" name="fichier" accept=".jpg, .jpeg, .png, .gif" required>
        </div>
        <button type="submit" class="btn btn-primary">Ajouter l'image</button>
    </form>

</body>
</html>