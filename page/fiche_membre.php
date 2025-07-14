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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['retourner_objet'])) {
    $id_emprunt = intval($_POST['id_emprunt']);
    $etat_objet = $_POST['etat_objet'];
    if (retourner_emprunt($id_emprunt)) {
        enregistrer_etat_retour($id_emprunt, $etat_objet);
    }
    header("location:fiche_membre.php?id=$id_membre");
    exit();
}

$membre = get_membre_by_id($id_membre); 
if (!$membre) {
    echo "Membre introuvable.";
    exit();
}

$objets_par_categorie = get_objets_membre_par_categorie($id_membre);
$emprunts = get_emprunts_membre($id_membre);
$emprunts_separes = separer_emprunts($emprunts);
$emprunts_actifs = $emprunts_separes['actifs'];
$emprunts_historique = $emprunts_separes['historique'];
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

        <h3>Objets empruntés</h3>
        <?php if (!empty($emprunts)) { ?>
            <div class="card mb-4">
                <div class="card-body">
                    <?php if (!empty($emprunts_actifs)) { ?>
                        <h5 class="text-warning">Emprunts en cours</h5>
                        <?php foreach ($emprunts_actifs as $emprunt) { ?>
                            <div class="border p-3 mb-3 bg-light">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <strong><?php echo htmlspecialchars($emprunt['nom_objet']); ?></strong><br>
                                        <small class="text-muted">Propriétaire: <?php echo htmlspecialchars($emprunt['proprietaire_nom']); ?></small>
                                    </div>
                                    <div class="col-md-3">
                                        <small>Emprunte le: <?php echo date('d/m/Y', strtotime($emprunt['date_emprunt'])); ?></small>
                                    </div>
                                    <div class="col-md-5">
                                        <form method="POST" class="d-flex align-items-center">
                                            <input type="hidden" name="id_emprunt" value="<?php echo $emprunt['id_emprunt']; ?>">
                                            <select name="etat_objet" class="form-select form-select-sm me-2" required>
                                                <option value="">Etat de l'objet</option>
                                                <option value="ok">OK</option>
                                                <option value="abime">Abime</option>
                                            </select>
                                            <button type="submit" name="retourner_objet" class="btn btn-success btn-sm">
                                                Retourner
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <p class="text-muted">Aucun emprunt en cours.</p>
                    <?php } ?>

                    <?php if (!empty($emprunts_historique)) { ?>
                        <h5 class="text-success mt-4">Historique des emprunts</h5>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Objet</th>
                                        <th>Propriétaire</th>
                                        <th>Date emprunt</th>
                                        <th>Date retour</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($emprunts_historique as $emprunt) { ?>
                                        <tr>
                                            <td>
                                                <a href="fiche_objet.php?id=<?php echo $emprunt['id_objet']; ?>">
                                                    <?php echo htmlspecialchars($emprunt['nom_objet']); ?>
                                                </a>
                                            </td>
                                            <td><?php echo htmlspecialchars($emprunt['proprietaire_nom']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($emprunt['date_emprunt'])); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($emprunt['date_retour'])); ?></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } else { ?>
            <p class="text-muted">Ce membre n'a emprunté aucun objet.</p>
        <?php } ?>

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
