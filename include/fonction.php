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
?>