<?php
include "../include/fonction.php";
session_start();

if (!isset($_SESSION['IdUtilisateur'])) {
    header("location:index.php");
    exit();
}

if (!isset($_GET['id'])) {
    echo "ID objet manquant.";
    exit();
}

$id_objet = $_GET['id'];

$objet = get_objet_by_id($id_objet); 
if (!$objet) {
    echo "Objet introuvable.";
    exit();
}
$images = get_images_objet($id_objet); 

$historique = get_historique_emprunts($id_objet); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <title>Fiche de l'objet</title>
</head>
<body>
    <?php include "../include/header.php"; ?>
    <div class="container mt-4">
        <h1><?php echo $objet['nom_objet']; ?></h1>
        <div class="row">
            <div class="col-md-4">
                <h5>Image principale</h5>
                <?php if (!empty($images)) { ?>
                    <img src="../assets/image/<?php echo $images[0]['url']; ?>" class="img-fluid mb-2" alt="Image principale">
                <?php } else { ?>
                    <p>Aucune image disponible.</p>
                <?php } ?>
                <?php if (count($images) > 1) { ?>
                    <h6>Autres images</h6>
                    <div class="d-flex flex-wrap">
                        <?php for ($i = 1; $i < count($images); $i++) { ?>
                            <img src="../assets/image/<?php echo $images[$i]['url']; ?>" class="img-thumbnail me-2 mb-2" style="max-width: 100px;" alt="Autre image">
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
            <div class="col-md-8">
                <h5>Details</h5>
                <ul class="list-group mb-3">
                    <li class="list-group-item"><strong>Categorie :</strong> <?php echo $objet['nom_categorie']; ?></li>
                    <li class="list-group-item"><strong>Proprietaire :</strong> <?php echo $objet['proprietaire_nom']; ?></li>
                    <li class="list-group-item"><strong>Statut :</strong> <?php echo $objet['statut_emprunt']; ?></li>
                </ul>
                <h5>Historique des emprunts</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Emprunteur</th>
                                <th>Date d'emprunt</th>
                                <th>Date de retour</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($historique)) {
                                foreach ($historique as $emprunt) { ?>
                                    <tr>
                                        <td><?php echo $emprunt['emprunteur_nom']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($emprunt['date_emprunt'])); ?></td>
                                        <td><?php echo $emprunt['date_retour'] ? date('d/m/Y', strtotime($emprunt['date_retour'])) : '-'; ?></td>
                                    </tr>
                            <?php   }
                            } else { ?>
                                <tr><td colspan="3">Aucun emprunt enregistre.</td></tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <a href="accueil.php" class="btn btn-secondary mt-3">Retour a la liste</a>
    </div>
    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>