<?php
session_start();
require_once('db/database.php');

$error = '';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs';
    } else {
        $sql = 'SELECT * FROM users WHERE email = :email';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(mode: PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: index.php');
            exit();
        } else {
            $error = 'Email ou mot de passe incorrect';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion d'Agendas</title>
       <link rel="stylesheet" href="assets/css/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body>
    <div class="auth-container">
        <div class="auth-form-container"></div>
        <h1>Connexion</h1>
        <?php if ($error): ?>
            <p style="color: red;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form action="login.php" method="post">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-block">Se connecter</button>
            </div>
        </form>
        <div class="auth-links">
            <p>Pas encore de compte? <a href="register.php">S'inscrire</a></p>
        </div>
    </div>
    </div>
</body>

</html>