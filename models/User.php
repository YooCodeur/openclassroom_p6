<?php
/**
 * Classe User
 * 
 * Cette classe gère toutes les opérations liées aux utilisateurs:
 * - Création et authentification des utilisateurs
 * - Vérification de l'existence d'emails et noms d'utilisateur
 * - Récupération et mise à jour des profils
 */

// Utiliser le chemin racine défini dans index.php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/');
}

// Inclusion de la configuration de la base de données
require_once ROOT_PATH . 'config/database.php';

/**
 * Classe User pour la gestion des utilisateurs
 */
class User {
    /**
     * Instance de connexion à la base de données
     * @var PDO
     */
    private $conn;
    
    /**
     * Constructeur de la classe
     * Initialise la connexion à la base de données
     */
    public function __construct() {
        // Récupération de l'instance singleton de la base de données
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    /**
     * Vérifie si un email existe déjà dans la base de données
     * 
     * @param string $email Email à vérifier
     * @return bool Retourne true si l'email existe, false sinon
     */
    public function emailExists($email) {
        // Préparation de la requête SQL
        $query = "SELECT id FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        // Liaison du paramètre email
        $stmt->bindParam(":email", $email);
        // Exécution de la requête
        $stmt->execute();
        
        // Retourne true si au moins une ligne est trouvée
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Vérifie si un email existe déjà (sauf pour un utilisateur spécifique)
     * Utile lors de la mise à jour d'un profil
     * 
     * @param string $email Email à vérifier
     * @param int $userId ID de l'utilisateur à exclure de la vérification
     * @return bool Retourne true si l'email existe pour un autre utilisateur, false sinon
     */
    public function emailExistsExcept($email, $userId) {
        // Préparation de la requête SQL avec exclusion d'un ID
        $query = "SELECT id FROM users WHERE email = :email AND id != :userId";
        $stmt = $this->conn->prepare($query);
        // Liaison des paramètres
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":userId", $userId);
        // Exécution de la requête
        $stmt->execute();
        
        // Retourne true si au moins une ligne est trouvée
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Vérifie si un nom d'utilisateur existe déjà
     * 
     * @param string $username Nom d'utilisateur à vérifier
     * @return bool Retourne true si le nom d'utilisateur existe, false sinon
     */
    public function usernameExists($username) {
        // Préparation de la requête SQL
        $query = "SELECT id FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        // Liaison du paramètre username
        $stmt->bindParam(":username", $username);
        // Exécution de la requête
        $stmt->execute();
        
        // Retourne true si au moins une ligne est trouvée
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Vérifie si un nom d'utilisateur existe déjà (sauf pour un utilisateur spécifique)
     * Utile lors de la mise à jour d'un profil
     * 
     * @param string $username Nom d'utilisateur à vérifier
     * @param int $userId ID de l'utilisateur à exclure de la vérification
     * @return bool Retourne true si le nom d'utilisateur existe pour un autre utilisateur, false sinon
     */
    public function usernameExistsExcept($username, $userId) {
        // Préparation de la requête SQL avec exclusion d'un ID
        $query = "SELECT id FROM users WHERE username = :username AND id != :userId";
        $stmt = $this->conn->prepare($query);
        // Liaison des paramètres
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":userId", $userId);
        // Exécution de la requête
        $stmt->execute();
        
        // Retourne true si au moins une ligne est trouvée
        return $stmt->rowCount() > 0;
    }
    
    /**
     * Crée un nouvel utilisateur dans la base de données
     * 
     * @param string $username Nom d'utilisateur
     * @param string $email Adresse email
     * @param string $password Mot de passe (en clair, sera haché)
     * @param string $firstName Prénom
     * @param string $lastName Nom de famille
     * @return int|bool ID de l'utilisateur créé ou false en cas d'échec
     */
    public function create($username, $email, $password, $firstName, $lastName) {
        // Hachage du mot de passe pour sécuriser le stockage
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Préparation de la requête d'insertion
        $query = "INSERT INTO users (username, email, password, first_name, last_name) 
                  VALUES (:username, :email, :password, :first_name, :last_name)";
        
        // Préparation et liaison des paramètres
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashedPassword);
        $stmt->bindParam(":first_name", $firstName);
        $stmt->bindParam(":last_name", $lastName);
        
        // Exécution de la requête et retour de l'ID généré ou false
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Authentifie un utilisateur avec son email et mot de passe
     * 
     * @param string $email Email de l'utilisateur
     * @param string $password Mot de passe en clair
     * @return array|bool Données de l'utilisateur ou false si authentification échouée
     */
    public function login($email, $password) {
        // Préparation de la requête pour récupérer l'utilisateur par email
        $query = "SELECT id, username, email, password FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        // Vérification si l'utilisateur existe
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Vérification du mot de passe avec la fonction sécurisée password_verify
            if(password_verify($password, $row['password'])) {
                // Retourne les données de l'utilisateur sans le mot de passe
                return [
                    'id' => $row['id'],
                    'username' => $row['username'],
                    'email' => $row['email']
                ];
            }
        }
        
        // Authentification échouée
        return false;
    }
    
    /**
     * Récupère les informations d'un utilisateur par son ID
     * 
     * @param int $id ID de l'utilisateur
     * @return array|bool Données de l'utilisateur ou false si non trouvé
     */
    public function getUserById($id) {
        // Préparation de la requête pour récupérer les informations de l'utilisateur
        $query = "SELECT id, username, email, first_name, last_name, bio, profile_image 
                  FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        
        // Retourne les données de l'utilisateur
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Met à jour le profil d'un utilisateur
     * 
     * @param int $userId ID de l'utilisateur à mettre à jour
     * @param string $username Nouveau nom d'utilisateur
     * @param string $email Nouvel email
     * @param string $firstName Nouveau prénom
     * @param string $lastName Nouveau nom de famille
     * @param string $bio Nouvelle biographie
     * @param string $avatar Chemin de la nouvelle image de profil
     * @return bool Succès ou échec de la mise à jour
     */
    public function updateProfile($userId, $username, $email, $firstName, $lastName, $bio, $avatar) {
        // Préparation de la requête de mise à jour
        $query = "UPDATE users 
                  SET username = :username, 
                      email = :email, 
                      first_name = :firstName, 
                      last_name = :lastName, 
                      bio = :bio, 
                      profile_image = :avatar 
                  WHERE id = :userId";
        
        // Préparation et liaison des paramètres
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":firstName", $firstName);
        $stmt->bindParam(":lastName", $lastName);
        $stmt->bindParam(":bio", $bio);
        $stmt->bindParam(":avatar", $avatar);
        $stmt->bindParam(":userId", $userId);
        
        // Exécution de la requête et retour du résultat
        return $stmt->execute();
    }
}
?> 