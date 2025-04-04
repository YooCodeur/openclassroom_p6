<?php
/**
 * Classe AuthController
 * 
 * Cette classe gère toutes les opérations liées à l'authentification:
 * - Inscription des nouveaux utilisateurs
 * - Connexion des utilisateurs existants
 * - Déconnexion des utilisateurs
 * - Validation des données d'authentification
 */

// Utiliser le chemin racine défini dans index.php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/');
}

// Inclusion du modèle User
require_once ROOT_PATH . 'models/User.php';

/**
 * Contrôleur pour la gestion de l'authentification
 */
class AuthController {
    /**
     * Instance du modèle User
     * @var User
     */
    private $userModel;
    
    /**
     * Constructeur de la classe
     * Initialise le modèle User
     */
    public function __construct() {
        // Création d'une instance du modèle User
        $this->userModel = new User();
    }
    
    /**
     * Affiche le formulaire d'inscription
     * Charge la vue correspondante
     */
    public function registerForm() {
        // Inclusion de la vue du formulaire d'inscription
        require_once ROOT_PATH . 'views/auth/register.php';
    }
    
    /**
     * Traite la soumission du formulaire d'inscription
     * Gère la validation des données et la création du compte utilisateur
     */
    public function register() {
        // Vérifier si la requête est de type POST
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et nettoyer les données du formulaire
            $username = htmlspecialchars(trim($_POST['username'] ?? ''));
            $email = htmlspecialchars(trim($_POST['email'] ?? ''));
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';
            $firstName = htmlspecialchars(trim($_POST['first_name'] ?? ''));
            $lastName = htmlspecialchars(trim($_POST['last_name'] ?? ''));
            
            // Tableau pour stocker les erreurs de validation
            $errors = [];
            
            // Validation du nom d'utilisateur
            if(empty($username)) {
                $errors[] = "Le nom d'utilisateur est requis";
            } elseif($this->userModel->usernameExists($username)) {
                $errors[] = "Ce nom d'utilisateur est déjà utilisé";
            }
            
            // Validation de l'email
            if(empty($email)) {
                $errors[] = "L'email est requis";
            } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format d'email invalide";
            } elseif($this->userModel->emailExists($email)) {
                $errors[] = "Cet email est déjà utilisé";
            }
            
            // Validation du mot de passe
            if(empty($password)) {
                $errors[] = "Le mot de passe est requis";
            } elseif(strlen($password) < 6) {
                $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
            }
            
            // Vérification de la correspondance des mots de passe
            if($password !== $passwordConfirm) {
                $errors[] = "Les mots de passe ne correspondent pas";
            }
            
            // Création de l'utilisateur si aucune erreur
            if(empty($errors)) {
                $userId = $this->userModel->create($username, $email, $password, $firstName, $lastName);
                
                if($userId) {
                    // Démarrer la session et enregistrer les informations de l'utilisateur
                    session_start();
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['username'] = $username;
                    
                    // Redirection vers la page d'accueil après inscription réussie
                    header('Location: index.php');
                    return;
                } else {
                    $errors[] = "Une erreur est survenue lors de l'inscription";
                }
            }
            
            // Inclusion de la vue avec les erreurs si présentes
            require_once ROOT_PATH . 'views/auth/register.php';
        } else {
            // Si la requête n'est pas de type POST, rediriger vers le formulaire d'inscription
            $this->registerForm();
        }
    }
    
    /**
     * Affiche le formulaire de connexion
     * Charge la vue correspondante
     */
    public function loginForm() {
        // Inclusion de la vue du formulaire de connexion
        require_once ROOT_PATH . 'views/auth/login.php';
    }
    
    /**
     * Traite la soumission du formulaire de connexion
     * Vérifie les identifiants et authentifie l'utilisateur
     */
    public function login() {
        // Vérifier si la requête est de type POST
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et nettoyer les données du formulaire
            $email = htmlspecialchars(trim($_POST['email'] ?? ''));
            $password = $_POST['password'] ?? '';
            
            // Tableau pour stocker les erreurs de validation
            $errors = [];
            
            // Validation basique des champs obligatoires
            if(empty($email) || empty($password)) {
                $errors[] = "Email et mot de passe sont requis";
            }
            
            // Tentative d'authentification
            if(empty($errors)) {
                $user = $this->userModel->login($email, $password);
                
                if($user) {
                    // Démarrer la session et enregistrer les informations de l'utilisateur
                    session_start();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    
                    // Redirection vers la page d'accueil après connexion réussie
                    header('Location: index.php');
                    return;
                } else {
                    $errors[] = "Email ou mot de passe incorrect";
                }
            }
            
            // Inclusion de la vue avec les erreurs si présentes
            require_once ROOT_PATH . 'views/auth/login.php';
        } else {
            // Si la requête n'est pas de type POST, rediriger vers le formulaire de connexion
            $this->loginForm();
        }
    }
    
    /**
     * Déconnecte l'utilisateur en détruisant la session
     * Redirige vers la page d'accueil
     */
    public function logout() {
        // Démarrer la session si ce n'est pas déjà fait
        session_start();
        
        // Détruire toutes les variables de session
        $_SESSION = array();
        
        // Détruire la session
        session_destroy();
        
        // Redirection vers la page d'accueil après déconnexion
        header('Location: index.php');
        return;
    }
}
?> 