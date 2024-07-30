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
        <form action="./_db/logout.php" autocomplete="off" method="post">
    <button id="logout" type="submit">Logout</button>
</form>
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
                <tr id="row-<?php echo $user['id']; ?>">
                    <form method="POST" autocomplete="off" action="">
                        <td><?php echo $user['id']; ?></td>
                        <td>
                            <span class="display"><?php echo htmlspecialchars($user['nom'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <input type="text" name="nom" class="edit" value="<?php echo htmlspecialchars($user['nom'], ENT_QUOTES, 'UTF-8'); ?>" style="display:none;">
                        </td>
                        <td>
                            <span class="display"><?php echo htmlspecialchars($user['prenom'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <input type="text" name="prenom" class="edit" value="<?php echo htmlspecialchars($user['prenom'], ENT_QUOTES, 'UTF-8'); ?>" style="display:none;">
                        </td>
                        <td>
                            <span class="display"><?php echo htmlspecialchars($user['mail'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <input type="email" name="email" class="edit" value="<?php echo htmlspecialchars($user['mail'], ENT_QUOTES, 'UTF-8'); ?>" style="display:none;">
                        </td>
                        <td>
                            <span class="display"><?php echo htmlspecialchars($user['codepostal'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <input type="text" name="codepostal" class="edit" value="<?php echo htmlspecialchars($user['codepostal'], ENT_QUOTES, 'UTF-8'); ?>" style="display:none;">
                        </td>
                        <td>
                            <button type="button" onclick="editUser(<?php echo $user['id']; ?>)">Modifier</button>
                            <button type="submit" name="edit_user" class="edit" style="display:none;">Enregistrer</button>
                            <button type="button" onclick="deleteUser(<?php echo $user['id']; ?>)">Supprimer</button>
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </table>
    </main>
    <footer>
        <img src="./assets/images/footfoot.png" alt="iep">
    </footer>
    <script>
        function editUser(id) {
            var row = document.getElementById('row-' + id);
            var displays = row.querySelectorAll('.display');
            var edits = row.querySelectorAll('.edit');
            displays.forEach(function(display) {
                display.style.display = 'none';
            });
            edits.forEach(function(edit) {
                edit.style.display = 'inline';
            });
        }
        function deleteUser(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                let form = document.createElement('form');
                form.method = 'POST';
                form.action = '';
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'user_id';
                input.value = id;
                form.appendChild(input);
                let button = document.createElement('input');
                button.type = 'hidden';
                button.name = 'delete_user';
                button.value = '1';
                form.appendChild(button);
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>