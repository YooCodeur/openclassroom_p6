<?php
/**
 * Classe BookController
 * 
 * Cette classe gère toutes les opérations liées aux livres:
 * - Affichage des livres (personnels, disponibles, détails)
 * - Création, modification et suppression de livres
 * - Gestion des statuts de disponibilité
 * - Téléchargement et gestion des images de couverture
 */

// Utiliser le chemin racine défini dans index.php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/');
}

// Inclusion du modèle Book
require_once ROOT_PATH . 'models/Book.php';

/**
 * Contrôleur pour la gestion des livres
 */
class BookController {
    /**
     * Instance du modèle Book
     * @var Book
     */
    private $bookModel;
    
    /**
     * Constructeur de la classe
     * Initialise le modèle Book
     */
    public function __construct() {
        // Création d'une instance du modèle Book
        $this->bookModel = new Book();
    }
    
    /**
     * Affiche la bibliothèque personnelle de l'utilisateur connecté
     * Redirige vers la page de connexion si l'utilisateur n'est pas connecté
     */
    public function myBooks() {
        // Vérifier si l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            // Redirection vers le formulaire de connexion
            header('Location: index.php?action=loginForm');
            return;
        }
        
        // Récupération des livres de l'utilisateur
        $userId = $_SESSION['user_id'];
        $books = $this->bookModel->getBooksByUserId($userId);
        
        // Inclusion de la vue de la bibliothèque personnelle
        require_once ROOT_PATH . 'views/books/my_books.php';
    }
    
    /**
     * Affiche tous les livres disponibles à l'échange
     * Gère également la fonctionnalité de recherche
     */
    public function allBooks() {
        // Récupération de tous les livres disponibles par défaut
        $books = $this->bookModel->getAvailableBooks();
        
        // Gestion de la recherche par titre
        if(isset($_GET['search']) && !empty($_GET['search'])) {
            // Nettoyage du terme de recherche
            $search = htmlspecialchars($_GET['search']);
            // Récupération des résultats de recherche
            $books = $this->bookModel->searchBooks($search);
        }
        
        // Inclusion de la vue de tous les livres
        require_once ROOT_PATH . 'views/books/all_books.php';
    }
    
    /**
     * Affiche les détails d'un livre spécifique
     * Redirige vers la liste de tous les livres si l'ID est invalide
     */
    public function viewBook() {
        // Vérifier si l'ID du livre est spécifié
        if(!isset($_GET['id']) || empty($_GET['id'])) {
            // Redirection si aucun ID n'est fourni
            header('Location: index.php?action=allBooks');
            return;
        }
        
        // Récupération du livre par son ID
        $bookId = $_GET['id'];
        $book = $this->bookModel->getBookById($bookId);
        
        // Vérifier si le livre existe
        if(!$book) {
            // Redirection si le livre n'existe pas
            header('Location: index.php?action=allBooks');
            return;
        }
        
        // Inclusion de la vue des détails du livre
        require_once ROOT_PATH . 'views/books/view_book.php';
    }
    
    /**
     * Affiche le formulaire de création d'un livre
     * Redirige vers la page de connexion si l'utilisateur n'est pas connecté
     */
    public function createBookForm() {
        // Vérifier si l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            // Redirection vers le formulaire de connexion
            header('Location: index.php?action=loginForm');
            return;
        }
        
        // Inclusion de la vue du formulaire de création
        require_once ROOT_PATH . 'views/books/create_book.php';
    }
    
    /**
     * Traite la soumission du formulaire de création d'un livre
     * Gère la validation des données, le téléchargement de l'image et la création du livre
     */
    public function createBook() {
        // Vérifier si l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            // Redirection vers le formulaire de connexion
            header('Location: index.php?action=loginForm');
            return;
        }
        
        // Vérifier si la requête est de type POST
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et nettoyer les données du formulaire
            $title = htmlspecialchars(trim($_POST['title'] ?? ''));
            $author = htmlspecialchars(trim($_POST['author'] ?? ''));
            $description = htmlspecialchars(trim($_POST['description'] ?? ''));
            $status = $_POST['status'] ?? 'available';
            
            // Tableau pour stocker les erreurs de validation
            $errors = [];
            
            // Validation des champs obligatoires
            if(empty($title)) {
                $errors[] = "Le titre est requis";
            }
            
            if(empty($author)) {
                $errors[] = "L'auteur est requis";
            }
            
            // Traitement de l'image de couverture
            $image = "";
            if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                // Extensions de fichiers autorisées
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['image']['name'];
                $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                // Vérifier si le format de l'image est autorisé
                if(in_array($filetype, $allowed)) {
                    // Génération d'un nom de fichier unique
                    $newFilename = uniqid() . '.' . $filetype;
                    $uploadDir = 'uploads/';
                    
                    // Création du répertoire de destination si nécessaire
                    if(!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    // Déplacement du fichier téléchargé vers le répertoire de destination
                    if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newFilename)) {
                        // Mise à jour du chemin de l'image
                        $image = $uploadDir . $newFilename;
                    } else {
                        $errors[] = "Erreur lors du téléchargement de l'image";
                    }
                } else {
                    $errors[] = "Format d'image non autorisé";
                }
            }
            
            // Création du livre si aucune erreur
            if(empty($errors)) {
                $userId = $_SESSION['user_id'];
                $bookId = $this->bookModel->create($userId, $title, $author, $description, $image, $status);
                
                if($bookId) {
                    // Redirection vers la bibliothèque personnelle après succès
                    header('Location: index.php?action=myBooks');
                    return;
                } else {
                    $errors[] = "Une erreur est survenue lors de la création du livre";
                }
            }
            
            // Inclusion de la vue avec les erreurs si présentes
            require_once ROOT_PATH . 'views/books/create_book.php';
        } else {
            // Si la requête n'est pas de type POST, rediriger vers le formulaire de création
            $this->createBookForm();
        }
    }
    
    /**
     * Affiche le formulaire de modification d'un livre
     * Vérifie que l'utilisateur est connecté et qu'il est le propriétaire du livre
     */
    public function editBookForm() {
        // Vérifier si l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            // Redirection vers le formulaire de connexion
            header('Location: index.php?action=loginForm');
            return;
        }
        
        // Vérifier si l'ID du livre est spécifié
        if(!isset($_GET['id']) || empty($_GET['id'])) {
            // Redirection si aucun ID n'est fourni
            header('Location: index.php?action=myBooks');
            return;
        }
        
        // Récupération du livre par son ID
        $bookId = $_GET['id'];
        $book = $this->bookModel->getBookById($bookId);
        
        // Vérifier si le livre existe et appartient à l'utilisateur connecté
        if(!$book || $book['user_id'] != $_SESSION['user_id']) {
            // Redirection si le livre n'existe pas ou n'appartient pas à l'utilisateur
            header('Location: index.php?action=myBooks');
            return;
        }
        
        // Inclusion de la vue du formulaire de modification
        require_once ROOT_PATH . 'views/books/edit_book.php';
    }
    
    /**
     * Traite la soumission du formulaire de modification d'un livre
     * Gère la validation des données, le téléchargement de l'image et la mise à jour du livre
     */
    public function editBook() {
        // Vérifier si l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            // Redirection vers le formulaire de connexion
            header('Location: index.php?action=loginForm');
            return;
        }
        
        // Vérifier si la requête est de type POST
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et nettoyer les données du formulaire
            $bookId = $_POST['book_id'] ?? '';
            $title = htmlspecialchars(trim($_POST['title'] ?? ''));
            $author = htmlspecialchars(trim($_POST['author'] ?? ''));
            $description = htmlspecialchars(trim($_POST['description'] ?? ''));
            $status = $_POST['status'] ?? 'available';
            
            // Vérifier si le livre existe et appartient à l'utilisateur
            $book = $this->bookModel->getBookById($bookId);
            
            if(!$book || $book['user_id'] != $_SESSION['user_id']) {
                // Redirection si le livre n'existe pas ou n'appartient pas à l'utilisateur
                header('Location: index.php?action=myBooks');
                return;
            }
            
            // Tableau pour stocker les erreurs de validation
            $errors = [];
            
            // Validation des champs obligatoires
            if(empty($title)) {
                $errors[] = "Le titre est requis";
            }
            
            if(empty($author)) {
                $errors[] = "L'auteur est requis";
            }
            
            // Traitement de l'image de couverture
            $image = $book['image']; // Conserver l'image existante par défaut
            
            if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                // Extensions de fichiers autorisées
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['image']['name'];
                $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                // Vérifier si le format de l'image est autorisé
                if(in_array($filetype, $allowed)) {
                    // Génération d'un nom de fichier unique
                    $newFilename = uniqid() . '.' . $filetype;
                    $uploadDir = 'uploads/';
                    
                    // Création du répertoire de destination si nécessaire
                    if(!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    // Déplacement du fichier téléchargé vers le répertoire de destination
                    if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newFilename)) {
                        // Suppression de l'ancienne image si elle existe
                        if(!empty($book['image']) && file_exists($book['image'])) {
                            unlink($book['image']);
                        }
                        
                        // Mise à jour du chemin de l'image
                        $image = $uploadDir . $newFilename;
                    } else {
                        $errors[] = "Erreur lors du téléchargement de l'image";
                    }
                } else {
                    $errors[] = "Format d'image non autorisé";
                }
            }
            
            // Mise à jour du livre si aucune erreur
            if(empty($errors)) {
                if($this->bookModel->update($bookId, $title, $author, $description, $image, $status)) {
                    // Redirection vers la bibliothèque personnelle après succès
                    header('Location: index.php?action=myBooks');
                    return;
                } else {
                    $errors[] = "Une erreur est survenue lors de la modification du livre";
                }
            }
            
            // Préparation des données pour réafficher le formulaire en cas d'erreur
            $book = [
                'id' => $bookId,
                'title' => $title,
                'author' => $author,
                'description' => $description,
                'image' => $image,
                'status' => $status
            ];
            
            // Inclusion de la vue avec les erreurs
            require_once ROOT_PATH . 'views/books/edit_book.php';
        } else {
            // Si la requête n'est pas de type POST, rediriger vers le formulaire de modification
            $this->editBookForm();
        }
    }
    
    /**
     * Supprime un livre après vérification du propriétaire
     * Supprime également l'image associée du serveur
     */
    public function deleteBook() {
        // Vérifier si l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            // Redirection vers le formulaire de connexion
            header('Location: index.php?action=loginForm');
            return;
        }
        
        // Vérifier si l'ID du livre est spécifié
        if(!isset($_GET['id']) || empty($_GET['id'])) {
            // Redirection si aucun ID n'est fourni
            header('Location: index.php?action=myBooks');
            return;
        }
        
        // Récupération du livre par son ID
        $bookId = $_GET['id'];
        $book = $this->bookModel->getBookById($bookId);
        
        // Vérifier si le livre existe et appartient à l'utilisateur connecté
        if(!$book || $book['user_id'] != $_SESSION['user_id']) {
            // Redirection si le livre n'existe pas ou n'appartient pas à l'utilisateur
            header('Location: index.php?action=myBooks');
            return;
        }
        
        // Suppression de l'image associée du serveur si elle existe
        if(!empty($book['image']) && file_exists($book['image'])) {
            unlink($book['image']);
        }
        
        // Suppression du livre de la base de données
        $this->bookModel->delete($bookId);
        
        // Redirection vers la bibliothèque personnelle
        header('Location: index.php?action=myBooks');
        return;
    }
    
    /**
     * Change le statut d'un livre entre disponible et indisponible
     * Vérifie que l'utilisateur est le propriétaire du livre
     */
    public function toggleBookStatus() {
        // Vérifier si l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            // Redirection vers le formulaire de connexion
            header('Location: index.php?action=loginForm');
            return;
        }
        
        // Vérifier si l'ID du livre est spécifié
        if(!isset($_GET['id']) || empty($_GET['id'])) {
            // Redirection si aucun ID n'est fourni
            header('Location: index.php?action=myBooks');
            return;
        }
        
        // Récupération du livre par son ID
        $bookId = $_GET['id'];
        $book = $this->bookModel->getBookById($bookId);
        
        // Vérifier si le livre existe et appartient à l'utilisateur connecté
        if(!$book || $book['user_id'] != $_SESSION['user_id']) {
            // Redirection si le livre n'existe pas ou n'appartient pas à l'utilisateur
            header('Location: index.php?action=myBooks');
            return;
        }
        
        // Inversion du statut actuel du livre
        $newStatus = ($book['status'] == 'available') ? 'unavailable' : 'available';
        
        // Mise à jour du statut dans la base de données
        $this->bookModel->updateStatus($bookId, $newStatus);
        
        // Redirection vers la bibliothèque personnelle
        header('Location: index.php?action=myBooks');
        return;
    }
}
?> 