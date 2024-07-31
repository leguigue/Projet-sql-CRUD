<?php
session_start();
require_once './_db/dbconnect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$stmt = $conn->prepare("SELECT * FROM user");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $id = $_POST['user_id'];
    $stmt = $conn->prepare("DELETE FROM user WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    header("Location: userhandling.php");
    exit();
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user'])) {
    $id = $_POST['user_id'];
    $nom = htmlspecialchars($_POST['nom'], ENT_QUOTES, 'UTF-8');
    $prenom = htmlspecialchars($_POST['prenom'], ENT_QUOTES, 'UTF-8');
    $mail = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $codepostal = filter_input(INPUT_POST, 'codepostal', FILTER_SANITIZE_NUMBER_INT);
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $error = "Adresse e-mail invalide";
    } elseif (!preg_match("/^[0-9]{5}$/", $codepostal)) {
        $error = "Code postal invalide";
    } else {
        $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
        $sql = "UPDATE user SET nom = :nom, prenom = :prenom, mail = :mail, codepostal = :codepostal";
        if ($password) {
            $sql .= ", password = :password";
        }
        $sql .= " WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenom', $prenom);
        $stmt->bindParam(':mail', $mail);
        $stmt->bindParam(':codepostal', $codepostal);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($password) {
            $stmt->bindParam(':password', $password);
        }
        $stmt->execute();
        header("Location: userhandling.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/uh.css">
    <link rel="icon" href="./assets/images/footfoot.png">
    <title>Gestion des Utilisateurs</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
    </style>
</head>
<body>
    <header>
        <h1>Gestion des Utilisateurs</h1>
        <?php include 'nav.php'; ?>
        <a href="./_db/logout.php" class="benjamin" id="logout">Logout</a>
    </header>
    <main>
        <h2>Liste des utilisateurs</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Email</th>
                <th>Code Postal</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($users as $user) : ?>
                <tr>
                    <form method="POST" autocomplete="off" action="">
                        <td><?php echo $user['id']; ?></td>
                        <td>
                            <?php if (isset($_GET['edit']) && $_GET['edit'] == $user['id']) : ?>
                                <input type="text" name="nom" value="<?php echo htmlspecialchars($user['nom'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php else : ?>
                                <?php echo htmlspecialchars($user['nom'], ENT_QUOTES, 'UTF-8'); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (isset($_GET['edit']) && $_GET['edit'] == $user['id']) : ?>
                                <input type="text" name="prenom" value="<?php echo htmlspecialchars($user['prenom'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php else : ?>
                                <?php echo htmlspecialchars($user['prenom'], ENT_QUOTES, 'UTF-8'); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (isset($_GET['edit']) && $_GET['edit'] == $user['id']) : ?>
                                <input type="email" name="email" value="<?php echo htmlspecialchars($user['mail'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php else : ?>
                                <?php echo htmlspecialchars($user['mail'], ENT_QUOTES, 'UTF-8'); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (isset($_GET['edit']) && $_GET['edit'] == $user['id']) : ?>
                                <input type="text" name="codepostal" value="<?php echo htmlspecialchars($user['codepostal'], ENT_QUOTES, 'UTF-8'); ?>">
                            <?php else : ?>
                                <?php echo htmlspecialchars($user['codepostal'], ENT_QUOTES, 'UTF-8'); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (isset($_GET['edit']) && $_GET['edit'] == $user['id']) : ?>
                                <input type="submit" class="benjamin" value="Enregistrer">
                                <input type="hidden" name="edit_user" value="1">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <a class="benjamin" href="userhandling.php">Annuler</a>
                            <?php else : ?>
                                <a class="benjamin" href="userhandling.php?edit=<?php echo $user['id']; ?>">Modifier</a>
                                <a href="#" class="benjamin" onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')){document.getElementById('delete_form_<?php echo $user['id']; ?>').submit();} return false;">Supprimer</a>
                                <form id="delete_form_<?php echo $user['id']; ?>" method="POST" action="" style="display:none;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                    <input type="hidden" name="delete_user" value="1">
                                </form>
                            <?php endif; ?>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </table>
    </main>
    <footer>
        <img src="./assets/images/footfoot.png" alt="iep">
    </footer>
    <img id="darkModeToggle" src="./assets/images/dark.svg" alt="Dark Mode Toggle">
    <script src="./assets/js/darkmode.js"></script>
</body>
</html>