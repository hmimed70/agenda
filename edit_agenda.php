<?php
session_start();
require_once 'db/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$agenda_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM agendas WHERE id = :agenda_id AND user_id = :user_id");
$stmt->execute([
    'agenda_id' => $agenda_id,
    'user_id' => $user_id
]);
$agenda = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$agenda) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $color = $_POST['color'] ?? '#3498db';
    
    if (empty($name)) {
        $error = 'Le nom de l\'agenda est obligatoire.';
    } else {
        $stmt = $pdo->prepare("
            UPDATE agendas SET
                name = :name,
                description = :description,
                color = :color,
                updated_at = NOW()
            WHERE id = :agenda_id
        ");
        $result = $stmt->execute([
            'name' => $name,
            'description' => $description,
            'color' => $color,
            'agenda_id' => $agenda_id
        ]);
        
        if ($result) {
            $success = 'L\'agenda a été mis à jour avec succès.';
            
            $agenda['name'] = $name;
            $agenda['description'] = $description;
            $agenda['color'] = $color;
        } else {
            $error = 'Une erreur est survenue lors de la mise à jour de l\'agenda.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier l'agenda - <?= htmlspecialchars($agenda['name']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">


</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="content-wrapper">
            <div class="page-header">
                <h1>Modifier l'agenda</h1>
                <div class="page-actions">
                    <a href="delete_agenda.php?id=<?= $agenda_id ?>" class="btn btn-danger" 
                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet agenda? Tous les événements associés seront également supprimés.')">
                        <i class="fas fa-trash"></i> Supprimer l'agenda
                    </a>
                    <a href="view_agenda.php?id=<?= $agenda_id ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour à l'agenda
                    </a>
                </div>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <div class="form-container">
                <form method="POST" action="edit_agenda.php?id=<?= $agenda_id ?>">
                    <div class="form-group">
                        <label for="name">Nom de l'agenda *</label>
                        <input type="text" id="name" name="name" required value="<?= htmlspecialchars($agenda['name']) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3"><?= htmlspecialchars($agenda['description']) ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="color">Couleur</label>
                        <div class="color-picker-container">
                            <input type="color" id="color" name="color" value="<?= htmlspecialchars($agenda['color']) ?>">
                            <span class="color-preview" style="background-color: <?= htmlspecialchars($agenda['color']) ?>"></span>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
    <script>
        const colorInput = document.getElementById('color');
        const colorPreview = document.querySelector('.color-preview');
        
        colorInput.addEventListener('input', function() {
            colorPreview.style.backgroundColor = this.value;
        });
    </script>
</body>
</html>
