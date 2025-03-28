<?php
// Utiliser le chemin racine défini dans index.php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/');
}

require_once ROOT_PATH . 'config/database.php';

class Book {
    private $conn;
    
    public function __construct() {
        $database = Database::getInstance();
        $this->conn = $database->getConnection();
    }
    
    // Récupérer tous les livres
    public function getAllBooks() {
        $query = "SELECT b.*, u.username as owner_name 
                  FROM books b 
                  JOIN users u ON b.user_id = u.id
                  ORDER BY b.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer les livres disponibles
    public function getAvailableBooks() {
        $query = "SELECT b.*, u.username as owner_name 
                  FROM books b 
                  JOIN users u ON b.user_id = u.id
                  WHERE b.status = 'available'
                  ORDER BY b.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer les livres d'un utilisateur
    public function getBooksByUserId($userId) {
        $query = "SELECT * FROM books WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Récupérer un livre par son ID
    public function getBookById($bookId) {
        $query = "SELECT b.*, u.username as owner_name, u.id as owner_id 
                  FROM books b 
                  JOIN users u ON b.user_id = u.id 
                  WHERE b.id = :book_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":book_id", $bookId);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Créer un nouveau livre
    public function create($userId, $title, $author, $description, $image = "", $status = "available") {
        $query = "INSERT INTO books (user_id, title, author, description, image, status) 
                  VALUES (:user_id, :title, :author, :description, :image, :status)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":author", $author);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":image", $image);
        $stmt->bindParam(":status", $status);
        
        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    // Mettre à jour un livre
    public function update($bookId, $title, $author, $description, $image, $status) {
        $query = "UPDATE books 
                  SET title = :title, author = :author, description = :description, 
                  image = :image, status = :status 
                  WHERE id = :book_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":author", $author);
        $stmt->bindParam(":description", $description);
        $stmt->bindParam(":image", $image);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":book_id", $bookId);
        
        return $stmt->execute();
    }
    
    // Changer le statut d'un livre
    public function updateStatus($bookId, $status) {
        $query = "UPDATE books SET status = :status WHERE id = :book_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":book_id", $bookId);
        
        return $stmt->execute();
    }
    
    // Supprimer un livre
    public function delete($bookId) {
        $query = "DELETE FROM books WHERE id = :book_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":book_id", $bookId);
        
        return $stmt->execute();
    }
    
    // Rechercher des livres par titre
    public function searchBooks($search) {
        $search = '%' . $search . '%';
        
        $query = "SELECT b.*, u.username as owner_name 
                  FROM books b 
                  JOIN users u ON b.user_id = u.id
                  WHERE b.title LIKE :search AND b.status = 'available'
                  ORDER BY b.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":search", $search);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?> 