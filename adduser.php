<?php
session_start();
require_once './_db/dbconnect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $nom = htmlspecialchars($_POST['nom'], ENT_QUOTES, 'UTF-8');
    $prenom = htmlspecialchars($_POST['prenom'], ENT_QUOTES, 'UTF-8');
    $mail = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $codepostal = filter_input(INPUT_POST, 'codepostal', FILTER_SANITIZE_NUMBER_INT);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse e-mail invalide";
    } elseif (!preg_match("/^[0-9]{5}$/", $codepostal)) {
        $error = "Code postal invalide";
    } else {
        $sql = "INSERT INTO user (nom, prenom, mail, codepostal, password) VALUES (:nom, :prenom, :mail, :codepostal, :password)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':mail', $mail);
        $stmt->bindParam(':codepostal', $codepostal);
        $stmt->bindParam(':password', $password);
        if ($stmt->execute()) {
            header("Location: userhandling.php");
            exit();
        } else {
            $error = "Erreur lors de l'ajout de l'utilisateur";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/adduser.css">
    <title>Ajouter un Utilisateur</title>
</head>
<body>
    <header> <h1>Ajouter un Utilisateur</h1>
    <?php include 'nav.php'; ?></header>
    <main>
    <form method="POST" autocomplete="off" action="">
        <label for="nom">Nom</label>
        <input type="text" name="nom" required>
        <label for="prenom">Prénom</label>
        <input type="text" name="prenom" required>
        <label for="email">Email</label>
        <input type="email" name="email" required>
        <label for="codepostal">Code Postal</label>
        <input type="text" name="codepostal" required>
        <label for="password">Mot de passe</label>
        <input type="password" name="password" required>
        <button type="submit" name="add_user">Ajouter</button>
    </form>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    </main>
   <footer>
    <img src="./assets/images/footfoot.png" alt="iep" id="iep">
   </footer>
   <img id="darkModeToggle" src="./assets/images/dark.svg" alt="Dark Mode Toggle">
   <script src="./assets/js/darkmode.js"></script>
</body>
</html>