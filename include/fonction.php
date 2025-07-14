<?php
require "connection.php";
function ajouter_membres($mail, $nom, $ville = null, $sexe = null, $pass, $dtn)
{
    $sql = "INSERT INTO membre (email,nom,ville, genre, mdp, date_naissance) VALUES ('$mail', '$nom', '$ville', '$sexe', '$pass', '$dtn')";
    $requete = mysqli_query(dbconnect(), $sql);
    return $requete;
}

function get_categories() {
    $sql = "SELECT id_categorie as id, nom_categorie as nom FROM categorie_objet ORDER BY nom_categorie";
    $result = mysqli_query(dbconnect(), $sql);
    return $result;
}

function get_objets_vue($categorie_id = null, $nom_objet = '', $disponible = false) {
    $where = [];
    if ($categorie_id) {
        $where[] = "id_categorie = '" . mysqli_real_escape_string(dbconnect(), $categorie_id) . "'";
    }
    if ($nom_objet) {
        $where[] = "nom_objet LIKE '%" . mysqli_real_escape_string(dbconnect(), $nom_objet) . "%'";
    }
    if ($disponible) {
        $where[] = "statut_emprunt = 'Disponible'";
    }
    $sql = "SELECT * FROM vue_objets_emprunts";
    if (count($where) > 0) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " ORDER BY nom_objet";
    $result = mysqli_query(dbconnect(), $sql);
    return $result;
}
function get_objet_by_id($id_objet)
{
    $id_objet = intval($id_objet);
    $sql = "SELECT id_objet, nom_objet, nom_categorie, proprietaire_nom, statut_emprunt FROM vue_objets_emprunts WHERE id_objet = '$id_objet'";
    $result = mysqli_query(dbconnect(), $sql);
    return mysqli_fetch_assoc($result);
}
function get_images_objet($id_objet) {
    $id_objet = intval($id_objet);
    $sql = "SELECT nom_image as url FROM image_objet WHERE id_objet = '$id_objet' ORDER BY id_image";
    $result = mysqli_query(dbconnect(), $sql);
    $images = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $images[] = $row;
    }
    return $images;
}

function get_historique_emprunts($id_objet)
{
    $id_objet = intval($id_objet);
    $sql = "SELECT emprunteur_nom, date_emprunt, date_retour FROM vue_objets_emprunts WHERE id_objet = '$id_objet' ORDER BY date_emprunt DESC";
    $result = mysqli_query(dbconnect(), $sql);
    $historique = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $historique[] = $row;
    }
    return $historique;
}

function upload_img($id, $name) {
    $sql = "INSERT INTO image_objet (id_objet, nom_image) VALUES ('$id', '$name')";
    $requete = mysqli_query(dbconnect(), $sql);
    return $requete;

}

function get_membre_by_id($id_membre) {
    $id_membre = intval($id_membre);
    $sql = "SELECT * FROM membre WHERE id_membre = '$id_membre'";
    $result = mysqli_query(dbconnect(), $sql);
    return mysqli_fetch_assoc($result);
}

function get_objets_membre_par_categorie($id_membre) {
    $id_membre = intval($id_membre);
    $sql = "SELECT id_objet, nom_objet, nom_categorie FROM vue_objets_emprunts WHERE proprietaire_id = '$id_membre' ORDER BY nom_categorie, nom_objet";
    $result = mysqli_query(dbconnect(), $sql);
    $objets = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $cat = $row['nom_categorie'];
        if (!isset($objets[$cat])) $objets[$cat] = [];
        $objets[$cat][] = $row;
    }
    return $objets;
}

function get_main_image($id_objet) {
    $id_objet = intval($id_objet);
    $sql = "SELECT nom_image FROM image_objet WHERE id_objet = '$id_objet' ORDER BY id_image ASC LIMIT 1";
    $result = mysqli_query(dbconnect(), $sql);
    $row = mysqli_fetch_assoc($result);
    return $row ? $row['nom_image'] : 'default.png';
}


function delete_image($id_image) {
    $sql = "SELECT nom_image FROM image_objet WHERE id_image = '$id_image'";
    $result = mysqli_query(dbconnect(), $sql);
    $row = mysqli_fetch_assoc($result);
    if ($row) {
        $file = dirname(__DIR__) . '/assets/image/' . $row['nom_image'];
        if (file_exists($file)) unlink($file);
        mysqli_query(dbconnect(), "DELETE FROM image_objet WHERE id_image = '$id_image'");
        return true;
    }
    return false;
}

function get_connection() {
    $serveur = "localhost";
    $username = "root";
    $password = "";
    $database = "gestion_objets";
    
    $conn = mysqli_connect($serveur, $username, $password, $database);
    
    if (!$conn) {
        die("Connexion échouée: " . mysqli_connect_error());
    }
    
    return $conn;
}
function retourner_emprunt($id_emprunt) {
    $conn = dbconnect();
    $query = "UPDATE emprunt SET date_retour = NOW() WHERE id_emprunt = ?";
    $stmt = mysqli_prepare($conn, $query);
    $result = $stmt;
    mysqli_close($conn);
    return $result;
}

function get_emprunts_membre($id_membre) {
    $conn = dbconnect();
    $query = "SELECT e.id_emprunt, e.date_emprunt, e.date_retour, o.nom_objet, o.id_objet,
                     m.nom as proprietaire_nom
              FROM emprunt e 
              JOIN objet o ON e.id_objet = o.id_objet 
              JOIN membre m ON o.id_membre = m.id_membre
              WHERE e.id_membre = ? 
              ORDER BY e.date_emprunt DESC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id_membre);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $emprunts = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($conn);
    return $emprunts;
}

function separer_emprunts($emprunts) {
    $emprunts_actifs = array_filter($emprunts, function($emprunt) {
        return $emprunt['date_retour'] === null;
    });
    $emprunts_historique = array_filter($emprunts, function($emprunt) {
        return $emprunt['date_retour'] !== null;
    });
    
    return [
        'actifs' => $emprunts_actifs,
        'historique' => $emprunts_historique
    ];
}

function enregistrer_etat_retour($id_emprunt, $etat_objet) {
    error_log("Objet retourne - Emprunt ID: $id_emprunt, Etat: $etat_objet");
}

function get_tous_les_emprunts() {
    $conn = dbconnect();
    $query = "SELECT e.id_emprunt, e.date_emprunt, e.date_retour, 
                     o.nom_objet, o.id_objet,
                     proprietaire.nom as proprietaire_nom,
                     emprunteur.nom as emprunteur_nom,
                     emprunteur.id_membre as emprunteur_id,
                     c.nom_categorie,
                     CASE 
                         WHEN e.date_retour IS NULL THEN 'En cours'
                         ELSE 'Retourné'
                     END as statut
              FROM emprunt e 
              JOIN objet o ON e.id_objet = o.id_objet 
              JOIN membre proprietaire ON o.id_membre = proprietaire.id_membre
              JOIN membre emprunteur ON e.id_membre = emprunteur.id_membre
              JOIN categorie_objet c ON o.id_categorie = c.id_categorie
              ORDER BY e.date_emprunt DESC";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $emprunts = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_close($conn);
    return $emprunts;
}


function get_statistiques_emprunts() {
    $conn = dbconnect();
    
    
    $query_total = "SELECT COUNT(*) as total FROM emprunt";
    $result_total = mysqli_query($conn, $query_total);
    $total = mysqli_fetch_assoc($result_total)['total'];
    
    
    $query_en_cours = "SELECT COUNT(*) as en_cours FROM emprunt WHERE date_retour IS NULL";
    $result_en_cours = mysqli_query($conn, $query_en_cours);
    $en_cours = mysqli_fetch_assoc($result_en_cours)['en_cours'];
    

    $query_retournes = "SELECT COUNT(*) as retournes FROM emprunt WHERE date_retour IS NOT NULL";
    $result_retournes = mysqli_query($conn, $query_retournes);
    $retournes = mysqli_fetch_assoc($result_retournes)['retournes'];
    
    mysqli_close($conn);
    
    return [
        'total' => $total,
        'en_cours' => $en_cours,
        'retournes' => $retournes
    ];
}


?>