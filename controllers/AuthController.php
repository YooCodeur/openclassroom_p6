<?php
// Utiliser le chemin racine défini dans index.php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/');
}

require_once ROOT_PATH . 'models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    // Afficher le formulaire d'inscription
    public function registerForm() {
        require_once ROOT_PATH . 'views/auth/register.php';
    }
    
    // Traiter l'inscription
    public function register() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et nettoyer les données
            $username = htmlspecialchars(trim($_POST['username'] ?? ''));
            $email = htmlspecialchars(trim($_POST['email'] ?? ''));
            $password = $_POST['password'] ?? '';
            $passwordConfirm = $_POST['password_confirm'] ?? '';
            $firstName = htmlspecialchars(trim($_POST['first_name'] ?? ''));
            $lastName = htmlspecialchars(trim($_POST['last_name'] ?? ''));
            
            $errors = [];
            
            // Validation
            if(empty($username)) {
                $errors[] = "Le nom d'utilisateur est requis";
            } elseif($this->userModel->usernameExists($username)) {
                $errors[] = "Ce nom d'utilisateur est déjà utilisé";
            }
            
            if(empty($email)) {
                $errors[] = "L'email est requis";
            } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format d'email invalide";
            } elseif($this->userModel->emailExists($email)) {
                $errors[] = "Cet email est déjà utilisé";
            }
            
            if(empty($password)) {
                $errors[] = "Le mot de passe est requis";
            } elseif(strlen($password) < 6) {
                $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
            }
            
            if($password !== $passwordConfirm) {
                $errors[] = "Les mots de passe ne correspondent pas";
            }
            
            // S'il n'y a pas d'erreurs, créer l'utilisateur
            if(empty($errors)) {
                $userId = $this->userModel->create($username, $email, $password, $firstName, $lastName);
                
                if($userId) {
                    // Démarrer la session et connecter l'utilisateur
                    session_start();
                    $_SESSION['user_id'] = $userId;
                    $_SESSION['username'] = $username;
                    
                    // Rediriger vers la page d'accueil
                    header('Location: index.php');
                    return;
                } else {
                    $errors[] = "Une erreur est survenue lors de l'inscription";
                }
            }
            
            // S'il y a des erreurs, afficher le formulaire avec les erreurs
            require_once ROOT_PATH . 'views/auth/register.php';
        } else {
            // Si ce n'est pas une requête POST, rediriger vers le formulaire
            $this->registerForm();
        }
    }
    
    // Afficher le formulaire de connexion
    public function loginForm() {
        require_once ROOT_PATH . 'views/auth/login.php';
    }
    
    // Traiter la connexion
    public function login() {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et nettoyer les données
            $email = htmlspecialchars(trim($_POST['email'] ?? ''));
            $password = $_POST['password'] ?? '';
            
            $errors = [];
            
            // Validation basique
            if(empty($email) || empty($password)) {
                $errors[] = "Email et mot de passe sont requis";
            }
            
            // Tentative de connexion
            if(empty($errors)) {
                $user = $this->userModel->login($email, $password);
                
                if($user) {
                    // Démarrer la session et stocker les informations de l'utilisateur
                    session_start();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    
                    // Rediriger vers la page d'accueil
                    header('Location: index.php');
                    return;
                } else {
                    $errors[] = "Email ou mot de passe incorrect";
                }
            }
            
            // S'il y a des erreurs, afficher le formulaire avec les erreurs
            require_once ROOT_PATH . 'views/auth/login.php';
        } else {
            // Si ce n'est pas une requête POST, rediriger vers le formulaire
            $this->loginForm();
        }
    }
    
    // Déconnexion
    public function logout() {
        session_start();
        
        // Détruire toutes les variables de session
        $_SESSION = array();
        
        // Détruire la session
        session_destroy();
        
        // Rediriger vers la page d'accueil
        header('Location: index.php');
        return;
    }
}
?> 