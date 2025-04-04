<?php
/**
 * Classe ProfileController
 * 
 * Cette classe gère toutes les opérations liées aux profils utilisateurs:
 * - Affichage et modification du profil de l'utilisateur connecté
 * - Affichage des profils d'autres utilisateurs
 * - Téléchargement et gestion des avatars
 */

// Utiliser le chemin racine défini dans index.php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/');
}

// Inclusion du modèle User
require_once ROOT_PATH . 'models/User.php';

/**
 * Contrôleur pour la gestion des profils utilisateurs
 */
class ProfileController {
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
     * Affiche le profil de l'utilisateur connecté
     * Redirige vers la page de connexion si l'utilisateur n'est pas connecté
     */
    public function myProfile() {
        // Vérifier si l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            // Redirection vers le formulaire de connexion
            header('Location: index.php?action=loginForm');
            return;
        }
        
        // Récupération des données du profil utilisateur
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getUserById($userId);
        
        // Inclusion de la vue du profil
        require_once ROOT_PATH . 'views/profile/my_profile.php';
    }
    
    /**
     * Affiche le formulaire de modification du profil
     * Redirige vers la page de connexion si l'utilisateur n'est pas connecté
     */
    public function editProfileForm() {
        // Vérifier si l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            // Redirection vers le formulaire de connexion
            header('Location: index.php?action=loginForm');
            return;
        }
        
        // Récupération des données du profil utilisateur
        $userId = $_SESSION['user_id'];
        $user = $this->userModel->getUserById($userId);
        
        // Inclusion de la vue du formulaire d'édition
        require_once ROOT_PATH . 'views/profile/edit_profile.php';
    }
    
    /**
     * Traite la soumission du formulaire de modification du profil
     * Gère la validation des données, le téléchargement de l'avatar et la mise à jour du profil
     */
    public function editProfile() {
        // Vérifier si l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            // Redirection vers le formulaire de connexion
            header('Location: index.php?action=loginForm');
            return;
        }
        
        // Vérifier si la requête est de type POST
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et nettoyer les données du formulaire
            $userId = $_SESSION['user_id'];
            $username = htmlspecialchars(trim($_POST['username'] ?? ''));
            $email = htmlspecialchars(trim($_POST['email'] ?? ''));
            $firstName = htmlspecialchars(trim($_POST['first_name'] ?? ''));
            $lastName = htmlspecialchars(trim($_POST['last_name'] ?? ''));
            $bio = htmlspecialchars(trim($_POST['bio'] ?? ''));
            
            // Tableau pour stocker les erreurs de validation
            $errors = [];
            
            // Validation du nom d'utilisateur
            if(empty($username)) {
                $errors[] = "Le nom d'utilisateur est requis";
            } elseif($this->userModel->usernameExistsExcept($username, $userId)) {
                $errors[] = "Ce nom d'utilisateur est déjà utilisé";
            }
            
            // Validation de l'email
            if(empty($email)) {
                $errors[] = "L'email est requis";
            } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Format d'email invalide";
            } elseif($this->userModel->emailExistsExcept($email, $userId)) {
                $errors[] = "Cet email est déjà utilisé";
            }
            
            // Récupération de l'avatar existant
            $avatar = $this->userModel->getUserById($userId)['profile_image'] ?? ''; // Conserver l'avatar existant par défaut
            
            // Traitement de l'upload d'avatar si un fichier a été soumis
            if(isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                // Extensions de fichiers autorisées
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['avatar']['name'];
                $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                // Vérification de l'extension du fichier
                if(in_array($filetype, $allowed)) {
                    // Génération d'un nom de fichier unique
                    $newFilename = uniqid() . '.' . $filetype;
                    $uploadDir = 'uploads/avatars/';
                    
                    // Création du répertoire de destination si nécessaire
                    if(!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    // Déplacement du fichier uploadé vers le répertoire de destination
                    if(move_uploaded_file($_FILES['avatar']['tmp_name'], $uploadDir . $newFilename)) {
                        // Suppression de l'ancien avatar si existant
                        if(!empty($avatar) && file_exists($avatar)) {
                            unlink($avatar);
                        }
                        
                        // Mise à jour du chemin de l'avatar
                        $avatar = $uploadDir . $newFilename;
                    } else {
                        $errors[] = "Erreur lors du téléchargement de l'avatar";
                    }
                } else {
                    $errors[] = "Format d'image non autorisé";
                }
            }
            
            // Mise à jour du profil si aucune erreur
            if(empty($errors)) {
                if($this->userModel->updateProfile($userId, $username, $email, $firstName, $lastName, $bio, $avatar)) {
                    // Mise à jour du nom d'utilisateur dans la session
                    $_SESSION['username'] = $username;
                    
                    // Redirection vers la page de profil après succès
                    header('Location: index.php?action=myProfile');
                    return;
                } else {
                    $errors[] = "Une erreur est survenue lors de la modification du profil";
                }
            }
            
            // Préparation des données pour réafficher le formulaire en cas d'erreur
            $user = [
                'id' => $userId,
                'username' => $username,
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'bio' => $bio,
                'profile_image' => $avatar
            ];
            
            // Inclusion de la vue avec les erreurs
            require_once ROOT_PATH . 'views/profile/edit_profile.php';
        } else {
            // Si la requête n'est pas de type POST, rediriger vers le formulaire d'édition
            $this->editProfileForm();
        }
    }
    
    /**
     * Affiche le profil d'un autre utilisateur
     * Inclut ses livres disponibles à l'échange
     */
    public function viewProfile() {
        // Vérifier si l'ID de l'utilisateur est spécifié
        if(!isset($_GET['id']) || empty($_GET['id'])) {
            // Redirection vers la liste des livres si aucun ID n'est fourni
            header('Location: index.php?action=allBooks');
            return;
        }
        
        // Récupération de l'ID et des données de l'utilisateur
        $userId = $_GET['id'];
        $user = $this->userModel->getUserById($userId);
        
        // Vérifier si l'utilisateur existe
        if(!$user) {
            // Redirection si l'utilisateur n'existe pas
            header('Location: index.php?action=allBooks');
            return;
        }
        
        // Récupération des livres disponibles de cet utilisateur
        require_once ROOT_PATH . 'models/Book.php';
        $bookModel = new Book();
        $books = $bookModel->getAvailableBooksByUserId($userId);
        
        // Inclusion de la vue du profil
        require_once ROOT_PATH . 'views/profile/view_profile.php';
    }
}
?> 