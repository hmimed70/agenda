

<?php
session_start();
require_once 'db/database.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

error_reporting(E_ALL);
if (!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) != 'login.php' && basename($_SERVER['PHP_SELF']) != 'register.php') {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$agendas = [];

if ($user_id) {
   echo $user_id;
    $stmt = $pdo->prepare("
     SELECT a.*, u.username as owner_name, 
CASE WHEN a.user_id = :user_id_case THEN 1 ELSE 0 END as is_owner
FROM agendas a
LEFT JOIN users u ON a.user_id = u.id
WHERE a.user_id = :user_id_where 
OR a.id IN (SELECT agenda_id FROM agenda_shares WHERE user_id = :user_id_subquery)
ORDER BY a.name ASC

    ");

    $stmt->execute([
        'user_id_case' => $user_id,
        'user_id_where' => $user_id,
        'user_id_subquery' => $user_id
    ]);

    $agendas = $stmt->fetchAll(PDO::FETCH_ASSOC);

}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion d'Agendas Partagés</title>
    <link rel="stylesheet" href="assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="container">
        <div class="dashboard">
            <h1>Mes Agendas</h1>
            
            <div class="action-bar">
                <a href="create_agenda.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Créer un nouvel agenda
                </a>
            </div>
            
            <?php if (empty($agendas)): ?>
                <div class="empty-state">
                    <p>Vous n'avez pas encore d'agenda. Créez-en un pour commencer!</p>
                </div>
            <?php else: ?>
                <div class="agenda-grid">
                    <?php foreach ($agendas as $agenda): ?>
                        <div class="agenda-card">
                            <div class="agenda-header" style="background-color: <?= htmlspecialchars($agenda['color']) ?>">
                                <h3><?= htmlspecialchars($agenda['name']) ?></h3>
                                <?php if ($agenda['is_owner']): ?>
                                    <span class="owner-badge">Propriétaire</span>
                                <?php else: ?>
                                    <span class="shared-badge">Partagé par <?= htmlspecialchars($agenda['owner_name']) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="agenda-body">
                                <p><?= htmlspecialchars($agenda['description']) ?></p>
                                <div class="agenda-actions">
                                    <a href="view_agenda.php?id=<?= $agenda['id'] ?>" class="btn btn-sm">
                                        <i class="fas fa-calendar"></i> Voir
                                    </a>
                                    <?php if ($agenda['is_owner']): ?>
                                        <a href="edit_agenda.php?id=<?= $agenda['id'] ?>" class="btn btn-sm">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                        <a href="share_agenda.php?id=<?= $agenda['id'] ?>" class="btn btn-sm">
                                            <i class="fas fa-share"></i> Partager
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/main.js"></script>
</body>
</html>
