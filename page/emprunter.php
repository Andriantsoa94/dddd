<?php
include "../include/fonction.php";
session_start();

if (!isset($_SESSION['IdUtilisateur'])) {
    header("location:index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $objet_id = intval($_POST['objet_id']);
    $duree = intval($_POST['duree']);
    $emprunteur_id = $_SESSION['IdUtilisateur'];

    if ($duree < 1 || $duree > 30) {
        header("location:accueil.php");
        exit();
    }

    $conn = dbconnect();

    $date_retour = date('Y-m-d', strtotime("+$duree days"));

    $query = "INSERT INTO emprunt (id_objet, id_membre, date_emprunt, date_retour) 
          VALUES (?, ?, NOW(), ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "iis", $objet_id, $emprunteur_id, $date_retour);
    mysqli_stmt_execute($stmt);


    mysqli_close($conn);
}

header("location:accueil.php");
exit();
?>