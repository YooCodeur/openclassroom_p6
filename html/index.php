<?php
// Point d'entrée de l'application

// Définir le chemin racine du projet
define('ROOT_PATH', dirname(__DIR__) . '/');

// Démarrer la session
session_start();

// Inclure les contrôleurs
require_once ROOT_PATH . 'controllers/AuthController.php';
require_once ROOT_PATH . 'controllers/BookController.php';

// Routing simple
$action = $_GET['action'] ?? 'home';

// Instancier les contrôleurs
$authController = new AuthController();
$bookController = new BookController();

// Router les requêtes
switch($action) {
    // Routes d'authentification
    case 'registerForm':
        $authController->registerForm();
        break;
    case 'register':
        $authController->register();
        break;
    case 'loginForm':
        $authController->loginForm();
        break;
    case 'login':
        $authController->login();
        break;
    case 'logout':
        $authController->logout();
        break;
    
    // Routes des livres
    case 'myBooks':
        $bookController->myBooks();
        break;
    case 'allBooks':
        $bookController->allBooks();
        break;
    case 'viewBook':
        $bookController->viewBook();
        break;
    case 'createBookForm':
        $bookController->createBookForm();
        break;
    case 'createBook':
        $bookController->createBook();
        break;
    case 'editBookForm':
        $bookController->editBookForm();
        break;
    case 'editBook':
        $bookController->editBook();
        break;
    case 'deleteBook':
        $bookController->deleteBook();
        break;
    case 'toggleBookStatus':
        $bookController->toggleBookStatus();
        break;
    
    // Page d'accueil (à développer)
    case 'home':
    default:
        // Pour l'instant, redirection vers la page de connexion si non connecté
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=loginForm');
            return;
        } else {
            // Afficher une page d'accueil simple avec les liens vers les fonctionnalités
            ?>
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Accueil - TomTroc</title>
                <link rel="stylesheet" href="assets/css/style.css">
            </head>
            <body>
                <div class="container">
                    <h1>Bienvenue sur TomTroc, <?php echo htmlspecialchars($_SESSION['username']); ?> !</h1>
                    
                    <div class="home-menu">
                        <div class="menu-section">
                            <h2>Gestion des livres</h2>
                            <a href="index.php?action=myBooks" class="btn btn-primary">Ma bibliothèque</a>
                            <a href="index.php?action=allBooks" class="btn">Tous les livres disponibles</a>
                            <a href="index.php?action=createBookForm" class="btn">Ajouter un livre</a>
                        </div>
                        
                        <div class="menu-section">
                            <h2>Mon compte</h2>
                            <a href="index.php?action=logout" class="btn">Déconnexion</a>
                        </div>
                    </div>
                </div>
            </body>
            </html>
            <?php
        }
        break;
}
?>
