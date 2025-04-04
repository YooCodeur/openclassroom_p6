<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>TomTroc</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Font Awesome pour les icônes -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="index.php">
                        <h1>TomTroc</h1>
                    </a>
                </div>
                <nav class="main-nav">
                    <ul>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li><a href="index.php?action=allBooks">Tous les livres</a></li>
                            <li><a href="index.php?action=myBooks">Ma bibliothèque</a></li>
                            <li><a href="index.php?action=myProfile">Mon profil</a></li>
                            <li><a href="index.php?action=logout">Déconnexion</a></li>
                        <?php else: ?>
                            <li><a href="index.php?action=allBooks">Tous les livres</a></li>
                            <li><a href="index.php?action=loginForm">Connexion</a></li>
                            <li><a href="index.php?action=registerForm">Inscription</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>
    <main>
        <!-- Le contenu spécifique de chaque page commencera ici --> 