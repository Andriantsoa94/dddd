<?php
include "../include/fonction.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <title>Login</title>
</head>
<body class="bg-light">
<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="card shadow p-4" style="max-width: 400px; width: 100%;">
        <form action="traitelog.php" method="post">
            <h3 class="mb-4 text-center">Sign in to your account</h3>
            <div class="mb-3">
                <input type="email" name="mail" class="form-control" placeholder="Email" required>
            </div>
            <div class="mb-3">
                <input type="password" name="pass" class="form-control" placeholder="Password" required>
            </div>
            <?php if (isset($_GET['error']) && $_GET['error'] == 1) { ?>
                <div class="alert alert-danger py-2">Please try again</div>
            <?php } ?>
            <button type="submit" class="btn btn-primary w-100">Connect</button>
            <p class="mt-3 text-center">Don't have an account? <a href="creer.php">Sign up</a></p>
        </form>
    </div>
</div>
<script src="../assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>