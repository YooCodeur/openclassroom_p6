<?php
/**
 * Classe Book
 * 
 * Cette classe gère toutes les opérations liées aux livres:
 * - Récupération, création, mise à jour et suppression de livres
 * - Recherche et filtrage des livres
 * - Gestion des statuts des livres
 */

// Utiliser le chemin racine défini dans index.php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/');
}

// Inclusion de la configuration de la base de données
require_once ROOT_PATH . 'config/database.php';

/**
 * Classe Book pour la gestion des livres
 */
class Book {
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
     * Récupère tous les livres de la base de données avec les informations du propriétaire
     * 
     * @return array Tableau de tous les livres avec les informations associées
     */
    public function getAllBooks() {
        // Préparation de la requête avec jointure pour obtenir le nom du propriétaire
        $query = "SELECT b.*, u.username as owner_name 
                  FROM books b 
                  JOIN users u ON b.user_id = u.id
                  ORDER BY b.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        // Retourne tous les livres trouvés
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère uniquement les livres disponibles
     * 
     * @return array Tableau des livres disponibles avec les informations du propriétaire
     */
    public function getAvailableBooks() {
        // Préparation de la requête avec filtrage sur le statut 'available'
        $query = "SELECT b.*, u.username as owner_name 
                  FROM books b 
                  JOIN users u ON b.user_id = u.id
                  WHERE b.status = 'available'
                  ORDER BY b.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        // Retourne tous les livres disponibles
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les livres appartenant à un utilisateur spécifique
     * 
     * @param int $userId ID de l'utilisateur propriétaire des livres
     * @return array Tableau des livres de l'utilisateur spécifié
     */
    public function getBooksByUserId($userId) {
        // Préparation de la requête filtrant par ID d'utilisateur
        $query = "SELECT * FROM books WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        // Liaison du paramètre user_id
        $stmt->bindParam(":user_id", $userId);
        $stmt->execute();
        
        // Retourne tous les livres de l'utilisateur
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les détails d'un livre spécifique par son ID
     * 
     * @param int $bookId ID du livre à récupérer
     * @return array|false Détails du livre ou false si non trouvé
     */
    public function getBookById($bookId) {
        // Préparation de la requête avec jointure pour obtenir les informations du propriétaire
        $query = "SELECT b.*, u.username as owner_name, u.id as owner_id 
                  FROM books b 
                  JOIN users u ON b.user_id = u.id 
                  WHERE b.id = :book_id";
        $stmt = $this->conn->prepare($query);
        // Liaison du paramètre book_id
        $stmt->bindParam(":book_id", $bookId);
        $stmt->execute();
        
        // Retourne les détails du livre
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Crée un nouveau livre dans la base de données
     * 
     * @param int $userId ID de l'utilisateur propriétaire du livre
     * @param string $title Titre du livre
     * @param string $author Auteur du livre
     * @param string $description Description du livre
     * @param string $image Chemin vers l'image de couverture (optionnel)
     * @param string $status Statut du livre (par défaut: 'available')
     * @return int|false ID du livre créé ou false en cas d'échec
     */
    public function create($userId, $title, $author, $description, $image = "", $status = "available") {
        // Préparation de la requête d'insertion
        $query = "INSERT INTO books (user_id, title, author, description, image, status) 
                  VALUES (:user_id, :title, :author, :description, :image, :status)";
        
        // Préparation et liaison des paramètres
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":author", $author);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":image", $image);
        $stmt->bindParam(":status", $status);
        
        // Exécution de la requête et retour de l'ID généré ou false
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Met à jour les informations d'un livre existant
     * 
     * @param int $bookId ID du livre à mettre à jour
     * @param string $title Nouveau titre
     * @param string $author Nouvel auteur
     * @param string $description Nouvelle description
     * @param string $image Nouvelle image de couverture
     * @param string $status Nouveau statut
     * @return bool Succès ou échec de la mise à jour
     */
    public function update($bookId, $title, $author, $description, $image, $status) {
        // Préparation de la requête de mise à jour
        $query = "UPDATE books 
                  SET title = :title, author = :author, description = :description, 
                  image = :image, status = :status 
                  WHERE id = :book_id";
        
        // Préparation et liaison des paramètres
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":author", $author);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":image", $image);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":book_id", $bookId);
        
        // Exécution de la requête et retour du résultat
        return $stmt->execute();
    }
    
    /**
     * Met à jour uniquement le statut d'un livre
     * 
     * @param int $bookId ID du livre à mettre à jour
     * @param string $status Nouveau statut ('available', 'borrowed', etc.)
     * @return bool Succès ou échec de la mise à jour
     */
    public function updateStatus($bookId, $status) {
        // Préparation de la requête de mise à jour du statut uniquement
        $query = "UPDATE books SET status = :status WHERE id = :book_id";
        $stmt = $this->conn->prepare($query);
        // Liaison des paramètres
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":book_id", $bookId);
        
        // Exécution de la requête et retour du résultat
        return $stmt->execute();
    }
    
    /**
     * Supprime un livre de la base de données
     * 
     * @param int $bookId ID du livre à supprimer
     * @return bool Succès ou échec de la suppression
     */
    public function delete($bookId) {
        // Préparation de la requête de suppression
        $query = "DELETE FROM books WHERE id = :book_id";
        $stmt = $this->conn->prepare($query);
        // Liaison du paramètre book_id
        $stmt->bindParam(":book_id", $bookId);
        
        // Exécution de la requête et retour du résultat
        return $stmt->execute();
    }
    
    /**
     * Recherche des livres par titre
     * 
     * @param string $search Terme de recherche
     * @return array Tableau des livres correspondants à la recherche
     */
    public function searchBooks($search) {
        // Ajout des caractères wildcard pour la recherche partielle
        $search = '%' . $search . '%';
        
        // Préparation de la requête avec filtrage LIKE et jointure
        $query = "SELECT b.*, u.username as owner_name 
                  FROM books b 
                  JOIN users u ON b.user_id = u.id
                  WHERE b.title LIKE :search AND b.status = 'available'
                  ORDER BY b.created_at DESC";
        
        // Préparation et liaison des paramètres
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":search", $search);
        $stmt->execute();
        
        // Retourne les résultats de recherche
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Récupère les livres disponibles d'un utilisateur spécifique
     * 
     * @param int $userId ID de l'utilisateur propriétaire des livres
     * @return array Tableau des livres disponibles de l'utilisateur
     */
    public function getAvailableBooksByUserId($userId) {
        // Préparation de la requête avec double filtre (utilisateur et statut)
        $query = "SELECT * FROM books 
                  WHERE user_id = :user_id AND status = 'available' 
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        // Liaison du paramètre user_id
        $stmt->bindParam(":user_id", $userId);
        $stmt->execute();
        
        // Retourne les livres disponibles de l'utilisateur
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?> 