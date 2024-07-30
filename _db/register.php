<?php
session_start();
require_once 'dbconnect.php';
if (!isset($conn) || !($conn instanceof PDO)) {
    die("Erreur : La connexion à la base de données n'est pas établie correctement.");
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = isset($_POST['nom']) ? htmlspecialchars($_POST['nom'], ENT_QUOTES, 'UTF-8') : '';
    $prenom = isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom'], ENT_QUOTES, 'UTF-8') : '';
    $mail = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $codepostal = filter_input(INPUT_POST, 'codepostal', FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        echo "Adresse e-mail invalide";
        exit;
    }
    if ($codepostal === null || !preg_match("/^[0-9]{5}$/", $codepostal)) {
        echo "Code postal invalide";
        exit;
    }
    $password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO user (nom, prenom, mail, password, codepostal) VALUES (:nom, :prenom, :mail, :password, :codepostal)"; 
    try {
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Erreur lors de la préparation de la requête : " . $conn->errorInfo()[2]);
        }
        $stmt->bindValue(':nom', $nom);
        $stmt->bindValue(':prenom', $prenom);
        $stmt->bindValue(':mail', $mail);
        $stmt->bindValue(':password', $password);
        $stmt->bindValue(':codepostal', $codepostal);
        if ($stmt->execute()) {
            echo "Inscription réussie. <a href='../index.php'>Retour à l'accueil</a>";
        } else {
            throw new Exception("Erreur lors de l'exécution de la requête : " . $stmt->errorInfo()[2]);
        }
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>