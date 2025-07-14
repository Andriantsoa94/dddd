<?php
include "../include/fonction.php";
session_start();

if (!isset($_SESSION['IdUtilisateur'])) {
    header("location:index.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "ID membre manquant.";
    exit();
}

$id_membre = intval($_GET['id']);

$membre = get_membre_by_id($id_membre); 
if (!$membre) {
    echo "Membre introuvable.";
    exit();
}

$objets_par_categorie = get_objets_membre_par_categorie($id_membre); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <title>Fiche du membre</title>
</head>
<body>
    <?php include "../include/header.php"; ?>
    <div class="container mt-4">
        <h1>Fiche du membre</h1>
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($membre['nom']); ?></h5>
                <p class="card-text"><strong>Email :</strong> <?php echo htmlspecialchars($membre['email']); ?></p>
                <p class="card-text"><strong>Ville :</strong> <?php echo htmlspecialchars($membre['ville']); ?></p>
                <p class="card-text"><strong>Genre :</strong> <?php echo htmlspecialchars($membre['genre']); ?></p>
                <p class="card-text"><strong>Date de naissance :</strong> <?php echo htmlspecialchars($membre['date_naissance']); ?></p>
            </div>
        </div>
        <h3>Objets du membre par catégorie</h3>
        <?php if (!empty($objets_par_categorie)) {
            foreach ($objets_par_categorie as $categorie => $objets) { ?>
                <div class="mb-3">
                    <h5><?php echo htmlspecialchars($categorie); ?></h5>
                    <ul class="list-group">
                        <?php foreach ($objets as $objet) { ?>
                            <li class="list-group-item">
                                <a href="fiche_objet.php?id=<?php echo $objet['id_objet']; ?>">
                                    <?php echo htmlspecialchars($objet['nom_objet']); ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
        <?php   }
        } else { ?>
            <p>Aucun objet pour ce membre.</p>
        <?php } ?>
        <a href="accueil.php" class="btn btn-secondary mt-3">Retour à la liste</a>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
