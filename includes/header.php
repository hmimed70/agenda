<header class="main-header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="index.php">Agenda Partagé</a>
            </div>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <nav class="main-nav">
                    <ul>
                        <li><a href="index.php">Mes Agendas</a></li>
                    </ul>
                </nav>
                
                <div class="user-menu">
                    <div class="user-info">
                        <span>Bonjour, <?= htmlspecialchars($_SESSION['username']) ?></span>
                        <a href="logout.php" class="btn btn-sm">Déconnexion</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="auth-links">
                    <a href="login.php" class="btn btn-sm">Connexion</a>
                    <a href="register.php" class="btn btn-primary btn-sm">Inscription</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>
