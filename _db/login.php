<?php
session_start();
require_once 'dbconnect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['email']) && isset($_POST['password'])) {
        $mail = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $sql = "SELECT id, password FROM user WHERE mail = :mail";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':mail', $mail);
        try {
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: ../userhandling.php");
                exit();
            } else {
                echo "Adresse e-mail ou mot de passe incorrect. <a href='../index.php'>Retour à l'accueil</a>";
            }
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    } else {
        echo "Veuillez fournir une adresse e-mail et un mot de passe. <a href='../index.php'>Retour à l'accueil</a>";
    }
}
?>