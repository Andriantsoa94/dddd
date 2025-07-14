<?php
include "../include/connection.php";
session_start();

$pass = $_POST['pass'];
$mail = $_POST['mail'];

$sql = "SELECT id_membre,email, mdp FROM membre WHERE email = '$mail' AND mdp = '$pass';";
echo $sql;
$result = mysqli_query(dbconnect(), $sql);

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $_SESSION['IdUtilisateur'] = $user['id_membre'];

    echo $_SESSION['IdUtilisateur'];
    header("location:accueil.php");
} else {
    header("location:index.php?error=1");
}
?>