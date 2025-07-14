<?php
session_start();
require('../include/fonction.php');

$idimg = $_POST['id_img'];
if (!$idimg) {
    die('ID image invalide.');
}

$uploadDir = dirname(__DIR__) . '/assets/image/';
$maxSize = 2 * 1024 * 1024;
$allowedMimeTypes = ['image/jpeg', 'image/png', 'application/pdf'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['fichier'])) {
    $file = $_FILES['fichier'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        die('Erreur lors de l’upload : ' . $file['error']);
    }

    if ($file['size'] > $maxSize) {
        die('Le fichier est trop volumineux.');
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, $allowedMimeTypes)) {
        die('Type de fichier non autorisé : ' . htmlspecialchars($mime));
    }

    $originalName = pathinfo($file['name'], PATHINFO_FILENAME);
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newName = $originalName . '_' . uniqid() . '.' . $extension;

    if (move_uploaded_file($file['tmp_name'], $uploadDir . $newName)) {
        if (upload_img($idimg, $newName)) {
            echo "Fichier uploadé avec succès : " . $newName;
           header('Location: accueil.php');
            exit;
        } else {
            die('Échec lors de la mise à jour de l’image dans la base de données.');
        }
    } else {
        die('Échec du déplacement du fichier.');
    }
} else {
    die('Aucun fichier reçu ou paramètres invalides.');
}