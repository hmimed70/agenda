<?php
session_start();
require_once('db/database.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $user_id = $_SESSION['user_id'];
    $color = $_POST['color'] ?? '#3498db';
    if (empty($name) ) {
        $error = 'Le nom de l\'agenda est obligatoire';
    } else {
        $sql = 'INSERT INTO agendas (name, description, color, user_id, created_at) VALUES (:name, :description, :color, :user_id, NOW())';
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute([
            'name' => $name,
             'description' => $description,
              'color' => $color,
              'user_id' => $user_id
            ]);
        if($result){
            $agenda_id = $pdo->lastInsertId();
            header('Location: view_agenda.php?id=' . $agenda_id);
        exit();
        }
        else {
            $error = 'Une erreur est survenue lors de la création de l\'agenda';
        }
        //header('Location: index.php');
        //exit();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Agenda - Gestion d'Agendas</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>
    <?php include('includes/header.php'); ?>
    <div class="container">
        <div class="content-wrapper">
            <div class="page-header">
                <h1>Créer un nouvel agenda</h1>
                <a href="index.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <div class="form-container">
                <form action="create_agenda.php" method="post">
                    <div class="form-group">
                        <label for="name">Nom de l'agenda *</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="color">Couleur</label>
                        <div class="color-picker-container">
                            <input type="color" id="color" name="color" value="#3498db">
                            <span class="color-preview"></span>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary btn-block">Créer</button>
                    </div>
                </form>
            </div>
        </div>
        <?php include 'includes/footer.php'; ?>
        <script src="assets/js/main.js"></script>
        <script>
            const colorInput = document.getElementById('color');
            const colorPreview = document.querySelector('.color-preview');

            colorInput.addEventListener('input', function () {
                colorPreview.style.backgroundColor = this.value;
            });

            colorPreview.style.backgroundColor = colorInput.value;
        </script>
</body>