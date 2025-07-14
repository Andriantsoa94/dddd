<?php
include "../include/fonction.php";
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <title>Register</title>
</head>
<body class="bg-light">
<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="card shadow p-4" style="max-width: 450px; width: 100%;">
        <form action="insert.php" method="post">
            <h3 class="mb-4 text-center">Create an Account</h3>
            <div class="mb-3">
                <label for="mail" class="form-label">Mail</label>
                <input type="email" name="mail" class="form-control" id="mail" required>
            </div>
            <div class="mb-3">
                <label for="nom" class="form-label">Name</label>
                <input type="text" name="nom" class="form-control" id="nom" required>
            </div>
            <div class="mb-3">
                <label for="ville" class="form-label">Ville</label>
                <input type="text" name="ville" class="form-control" id="ville" required>
            </div>
            <div class="mb-3">
                <label for="sexe" class="form-label">Sexe</label>
                <select name="sexe" class="form-select" id="sexe" required>
                    <option value="Homme">Homme</option>
                    <option value="Femme">Femme</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="pass" class="form-label">Password</label>
                <input type="password" name="pass" class="form-control" id="pass" required>
            </div>
            <div class="mb-3">
                <label for="dtn" class="form-label">Date de naissance</label>
                <input type="date" name="dtn" class="form-control" id="dtn" required>
            </div>
            <?php if (isset($_GET['error'])) { ?>
                <div class="alert alert-danger py-2">ERROR</div>
            <?php } ?>
            <button type="submit" class="btn btn-primary w-100">Register</button>
            <p class="mt-3 text-center">
                <a href="index.php">I have an account</a>
            </p>
        </form>
    </div>
</div>
<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>