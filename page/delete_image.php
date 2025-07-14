<?php
require '../include/fonction.php';
$id_image = $_GET['id_image'];
$id_objet = $_GET['id_objet'];
delete_image($id_image);
header('Location: add_image.php?id_objet=' . $id_objet);
exit;