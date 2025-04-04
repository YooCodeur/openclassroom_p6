<?php
/**
 * Point d'entrée principal de l'application TomTroc
 * 
 * Ce fichier est le point d'entrée unique de l'application et gère:
 * - L'initialisation de l'environnement (constantes, session)
 * - Le chargement des contrôleurs nécessaires
 * - Le routage des requêtes vers les bonnes méthodes de contrôleur
 * - L'affichage de la page d'accueil par défaut
 * 
 * Le routage est basé sur le paramètre GET 'action' qui détermine
 * quelle méthode de contrôleur doit être appelée.
 */

// Définition du chemin racine pour faciliter les inclusions de fichiers
// Ce chemin sera utilisé par tous les fichiers de l'application
define('ROOT_PATH', dirname(__DIR__) . '/');

// Démarrage de la session PHP pour gérer l'authentification
// Cette session sera disponible pour tous les fichiers
session_start();

// Inclusion des fichiers de contrôleurs
// Ces contrôleurs contiennent la logique métier de l'application
require_once ROOT_PATH . 'controllers/AuthController.php';     // Gestion de l'authentification
require_once ROOT_PATH . 'controllers/BookController.php';     // Gestion des livres
require_once ROOT_PATH . 'controllers/ProfileController.php';  // Gestion des profils utilisateurs

// Récupération du paramètre d'action depuis l'URL
// Si aucune action n'est spécifiée, 'home' est utilisée par défaut
$action = $_GET['action'] ?? 'home';

// Instanciation des contrôleurs nécessaires
// Ces instances géreront les différentes fonctionnalités de l'application
$authController = new AuthController();      // Authentification (inscription, connexion, déconnexion)
$bookController = new BookController();      // Gestion des livres (création, modification, suppression)
$profileController = new ProfileController(); // Gestion des profils (affichage, modification)

// Routage des requêtes en fonction de l'action demandée
// Le switch redirige vers la méthode appropriée du contrôleur
switch($action) {
    // ===== Routes d'authentification =====
    // Gestion de l'inscription, connexion et déconnexion
    case 'registerForm':
        // Affiche le formulaire d'inscription
        $authController->registerForm();
        break;
    case 'register':
        // Traite la soumission du formulaire d'inscription
        $authController->register();
        break;
    case 'loginForm':
        // Affiche le formulaire de connexion
        $authController->loginForm();
        break;
    case 'login':
        // Traite la soumission du formulaire de connexion
        $authController->login();
        break;
    case 'logout':
        // Déconnecte l'utilisateur
        $authController->logout();
        break;
    
    // ===== Routes de gestion des livres =====
    // Création, modification, suppression et consultation des livres
    case 'myBooks':
        // Affiche la bibliothèque personnelle de l'utilisateur
        $bookController->myBooks();
        break;
    case 'allBooks':
        // Affiche tous les livres disponibles à l'échange
        $bookController->allBooks();
        break;
    case 'viewBook':
        // Affiche les détails d'un livre spécifique
        $bookController->viewBook();
        break;
    case 'createBookForm':
        // Affiche le formulaire de création de livre
        $bookController->createBookForm();
        break;
    case 'createBook':
        // Traite la soumission du formulaire de création
        $bookController->createBook();
        break;
    case 'editBookForm':
        // Affiche le formulaire de modification d'un livre
        $bookController->editBookForm();
        break;
    case 'editBook':
        // Traite la soumission du formulaire de modification
        $bookController->editBook();
        break;
    case 'deleteBook':
        // Supprime un livre
        $bookController->deleteBook();
        break;
    case 'toggleBookStatus':
        // Change le statut de disponibilité d'un livre
        $bookController->toggleBookStatus();
        break;
    
    // ===== Routes de gestion des profils =====
    // Affichage et modification des profils utilisateurs
    case 'myProfile':
        // Affiche le profil de l'utilisateur connecté
        $profileController->myProfile();
        break;
    case 'editProfileForm':
        // Affiche le formulaire de modification du profil
        $profileController->editProfileForm();
        break;
    case 'editProfile':
        // Traite la soumission du formulaire de modification du profil
        $profileController->editProfile();
        break;
    case 'viewProfile':
        // Affiche le profil d'un autre utilisateur
        $profileController->viewProfile();
        break;
    
    // ===== Page d'accueil ou route par défaut =====
    // Affiche la page d'accueil ou redirige vers la connexion
    case 'home':
    default:
        // Vérifie si l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            // Si non connecté, redirection vers la page de connexion
            header('Location: index.php?action=loginForm');
            return;
        } else {
            // Si connecté, affichage de la page d'accueil avec les fonctionnalités principales
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
                            <a href="index.php?action=myProfile" class="btn">Mon profil</a>
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