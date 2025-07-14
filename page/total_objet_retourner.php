
<?php
include "../include/fonction.php";
session_start();

if (!isset($_SESSION['IdUtilisateur'])) {
    header("location:index.php");
    exit();
}


$tous_emprunts = get_tous_les_emprunts();


$statistiques = get_statistiques_emprunts();


$emprunts_en_cours = array_filter($tous_emprunts, function($emprunt) {
    return $emprunt['statut'] === 'En cours';
});

$emprunts_retournes = array_filter($tous_emprunts, function($emprunt) {
    return $emprunt['statut'] === 'Retourné';
});
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <title>Gestion des Emprunts</title>
</head>
<body>
    <?php include "../include/header.php"; ?>
    <div class="container mt-4">
        <h1>Gestion de tous les emprunts</h1>
        
        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-white bg-primary">
                    <div class="card-body text-center">
                        <h2><?php echo $statistiques['total']; ?></h2>
                        <p class="card-text">Total des emprunts</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning">
                    <div class="card-body text-center">
                        <h2><?php echo $statistiques['en_cours']; ?></h2>
                        <p class="card-text">Emprunts en cours</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body text-center">
                        <h2><?php echo $statistiques['retournes']; ?></h2>
                        <p class="card-text">Emprunts retournés</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglets pour séparer les emprunts -->
        <ul class="nav nav-tabs" id="empruntTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="en-cours-tab" data-bs-toggle="tab" data-bs-target="#en-cours" type="button" role="tab">
                    Emprunts en cours <span class="badge bg-warning"><?php echo count($emprunts_en_cours); ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="retournes-tab" data-bs-toggle="tab" data-bs-target="#retournes" type="button" role="tab">
                    Emprunts retournés <span class="badge bg-success"><?php echo count($emprunts_retournes); ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="tous-tab" data-bs-toggle="tab" data-bs-target="#tous" type="button" role="tab">
                    Tous les emprunts <span class="badge bg-primary"><?php echo count($tous_emprunts); ?></span>
                </button>
            </li>
        </ul>

        <div class="tab-content mt-3" id="empruntTabsContent">
            <!-- Emprunts en cours -->
            <div class="tab-pane fade show active" id="en-cours" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="text-warning mb-0">Emprunts en cours</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($emprunts_en_cours)) { ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Objet</th>
                                            <th>Catégorie</th>
                                            <th>Propriétaire</th>
                                            <th>Emprunteur</th>
                                            <th>Date emprunt</th>
                                            <th>Durée</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($emprunts_en_cours as $emprunt) { 
                                            $jours_emprunt = floor((time() - strtotime($emprunt['date_emprunt'])) / (60 * 60 * 24));
                                        ?>
                                            <tr>
                                                <td>
                                                    <a href="fiche_objet.php?id=<?php echo $emprunt['id_objet']; ?>">
                                                        <strong><?php echo htmlspecialchars($emprunt['nom_objet']); ?></strong>
                                                    </a>
                                                </td>
                                                <td><?php echo htmlspecialchars($emprunt['nom_categorie']); ?></td>
                                                <td><?php echo htmlspecialchars($emprunt['proprietaire_nom']); ?></td>
                                                <td>
                                                    <a href="fiche_membre.php?id=<?php echo $emprunt['emprunteur_id']; ?>">
                                                        <?php echo htmlspecialchars($emprunt['emprunteur_nom']); ?>
                                                    </a>
                                                </td>
                                                <td><?php echo date('d/m/Y', strtotime($emprunt['date_emprunt'])); ?></td>
                                                <td>
                                                    <span class="badge <?php echo $jours_emprunt > 7 ? 'bg-danger' : 'bg-info'; ?>">
                                                        <?php echo $jours_emprunt; ?> jour(s)
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="fiche_membre.php?id=<?php echo $emprunt['emprunteur_id']; ?>" class="btn btn-sm btn-primary">
                                                        Voir membre
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <p class="text-muted">Aucun emprunt en cours.</p>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Emprunts retournés -->
            <div class="tab-pane fade" id="retournes" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="text-success mb-0">Emprunts retournés</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($emprunts_retournes)) { ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Objet</th>
                                            <th>Catégorie</th>
                                            <th>Propriétaire</th>
                                            <th>Emprunteur</th>
                                            <th>Date emprunt</th>
                                            <th>Date retour</th>
                                            <th>Durée totale</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($emprunts_retournes as $emprunt) { 
                                            $duree_emprunt = floor((strtotime($emprunt['date_retour']) - strtotime($emprunt['date_emprunt'])) / (60 * 60 * 24));
                                        ?>
                                            <tr>
                                                <td>
                                                    <a href="fiche_objet.php?id=<?php echo $emprunt['id_objet']; ?>">
                                                        <?php echo htmlspecialchars($emprunt['nom_objet']); ?>
                                                    </a>
                                                </td>
                                                <td><?php echo htmlspecialchars($emprunt['nom_categorie']); ?></td>
                                                <td><?php echo htmlspecialchars($emprunt['proprietaire_nom']); ?></td>
                                                <td>
                                                    <a href="fiche_membre.php?id=<?php echo $emprunt['emprunteur_id']; ?>">
                                                        <?php echo htmlspecialchars($emprunt['emprunteur_nom']); ?>
                                                    </a>
                                                </td>
                                                <td><?php echo date('d/m/Y', strtotime($emprunt['date_emprunt'])); ?></td>
                                                <td><?php echo date('d/m/Y', strtotime($emprunt['date_retour'])); ?></td>
                                                <td>
                                                    <span class="badge bg-secondary"><?php echo $duree_emprunt; ?> jour(s)</span>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <p class="text-muted">Aucun emprunt retourné.</p>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <!-- Tous les emprunts -->
            <div class="tab-pane fade" id="tous" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Tous les emprunts</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($tous_emprunts)) { ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Objet</th>
                                            <th>Catégorie</th>
                                            <th>Propriétaire</th>
                                            <th>Emprunteur</th>
                                            <th>Date emprunt</th>
                                            <th>Date retour</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tous_emprunts as $emprunt) { ?>
                                            <tr>
                                                <td>
                                                    <a href="fiche_objet.php?id=<?php echo $emprunt['id_objet']; ?>">
                                                        <?php echo htmlspecialchars($emprunt['nom_objet']); ?>
                                                    </a>
                                                </td>
                                                <td><?php echo htmlspecialchars($emprunt['nom_categorie']); ?></td>
                                                <td><?php echo htmlspecialchars($emprunt['proprietaire_nom']); ?></td>
                                                <td>
                                                    <a href="fiche_membre.php?id=<?php echo $emprunt['emprunteur_id']; ?>">
                                                        <?php echo htmlspecialchars($emprunt['emprunteur_nom']); ?>
                                                    </a>
                                                </td>
                                                <td><?php echo date('d/m/Y', strtotime($emprunt['date_emprunt'])); ?></td>
                                                <td>
                                                    <?php if ($emprunt['date_retour']) { ?>
                                                        <?php echo date('d/m/Y', strtotime($emprunt['date_retour'])); ?>
                                                    <?php } else { ?>
                                                        <span class="text-muted">En cours</span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $emprunt['statut'] === 'En cours' ? 'bg-warning' : 'bg-success'; ?>">
                                                        <?php echo $emprunt['statut']; ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <p class="text-muted">Aucun emprunt enregistré.</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <a href="accueil.php" class="btn btn-secondary mt-3">Retour à l'accueil</a>
    </div>

    <script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>