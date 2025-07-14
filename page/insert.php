<?php
require '../include/fonction.php';
$mail = $_POST['mail'];
$nom = $_POST['nom'];
$ville = $_POST['ville'];
$sexe = $_POST['sexe'];
$pass = $_POST['pass'];
$dtn = $_POST['dtn'];
if($mail==NULL || $pass==NULL || $nom==NULL || $dtn==NULL || $sexe==NULL || $ville==NULL){
   header("location:creer.php?error=1");
    echo $mail, $nom, $ville, $sexe, $pass, $dtn;
    echo "erreur";
}else{
    $requete = ajouter_membres($mail,$nom,$ville,$sexe,$pass,$dtn);
   header("location:index.php");
}
?>