<?php
// Utiliser le chemin racine défini dans index.php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__) . '/');
}

require_once ROOT_PATH . 'models/Book.php';

class BookController {
    private $bookModel;
    
    public function __construct() {
        $this->bookModel = new Book();
    }
    
    // Afficher la bibliothèque personnelle de l'utilisateur
    public function myBooks() {
        // Vérifier si l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=loginForm');
            return;
        }
        
        $userId = $_SESSION['user_id'];
        $books = $this->bookModel->getBooksByUserId($userId);
        
        require_once ROOT_PATH . 'views/books/my_books.php';
    }
    
    // Afficher tous les livres disponibles
    public function allBooks() {
        $books = $this->bookModel->getAvailableBooks();
        
        // Gestion de la recherche
        if(isset($_GET['search']) && !empty($_GET['search'])) {
            $search = htmlspecialchars($_GET['search']);
            $books = $this->bookModel->searchBooks($search);
        }
        
        require_once ROOT_PATH . 'views/books/all_books.php';
    }
    
    // Afficher les détails d'un livre
    public function viewBook() {
        if(!isset($_GET['id']) || empty($_GET['id'])) {
            header('Location: index.php?action=allBooks');
            return;
        }
        
        $bookId = $_GET['id'];
        $book = $this->bookModel->getBookById($bookId);
        
        if(!$book) {
            header('Location: index.php?action=allBooks');
            return;
        }
        
        require_once ROOT_PATH . 'views/books/view_book.php';
    }
    
    // Afficher le formulaire de création d'un livre
    public function createBookForm() {
        // Vérifier si l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=loginForm');
            return;
        }
        
        require_once ROOT_PATH . 'views/books/create_book.php';
    }
    
    // Traiter la création d'un livre
    public function createBook() {
        // Vérifier si l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=loginForm');
            return;
        }
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et nettoyer les données
            $title = htmlspecialchars(trim($_POST['title'] ?? ''));
            $author = htmlspecialchars(trim($_POST['author'] ?? ''));
            $description = htmlspecialchars(trim($_POST['description'] ?? ''));
            $status = $_POST['status'] ?? 'available';
            
            $errors = [];
            
            // Validation
            if(empty($title)) {
                $errors[] = "Le titre est requis";
            }
            
            if(empty($author)) {
                $errors[] = "L'auteur est requis";
            }
            
            // Traitement de l'image
            $image = "";
            if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['image']['name'];
                $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                // Vérifier si le format de l'image est autorisé
                if(in_array($filetype, $allowed)) {
                    $newFilename = uniqid() . '.' . $filetype;
                    $uploadDir = 'uploads/';
                    
                    // Créer le répertoire s'il n'existe pas
                    if(!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    // Déplacer le fichier téléchargé
                    if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newFilename)) {
                        $image = $uploadDir . $newFilename;
                    } else {
                        $errors[] = "Erreur lors du téléchargement de l'image";
                    }
                } else {
                    $errors[] = "Format d'image non autorisé";
                }
            }
            
            // S'il n'y a pas d'erreurs, créer le livre
            if(empty($errors)) {
                $userId = $_SESSION['user_id'];
                $bookId = $this->bookModel->create($userId, $title, $author, $description, $image, $status);
                
                if($bookId) {
                    header('Location: index.php?action=myBooks');
                    return;
                } else {
                    $errors[] = "Une erreur est survenue lors de la création du livre";
                }
            }
            
            // S'il y a des erreurs, afficher le formulaire avec les erreurs
            require_once ROOT_PATH . 'views/books/create_book.php';
        } else {
            // Si ce n'est pas une requête POST, rediriger vers le formulaire
            $this->createBookForm();
        }
    }
    
    // Afficher le formulaire de modification d'un livre
    public function editBookForm() {
        // Vérifier si l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=loginForm');
            return;
        }
        
        if(!isset($_GET['id']) || empty($_GET['id'])) {
            header('Location: index.php?action=myBooks');
            return;
        }
        
        $bookId = $_GET['id'];
        $book = $this->bookModel->getBookById($bookId);
        
        if(!$book || $book['user_id'] != $_SESSION['user_id']) {
            header('Location: index.php?action=myBooks');
            return;
        }
        
        require_once ROOT_PATH . 'views/books/edit_book.php';
    }
    
    // Traiter la modification d'un livre
    public function editBook() {
        // Vérifier si l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=loginForm');
            return;
        }
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupérer et nettoyer les données
            $bookId = $_POST['book_id'] ?? '';
            $title = htmlspecialchars(trim($_POST['title'] ?? ''));
            $author = htmlspecialchars(trim($_POST['author'] ?? ''));
            $description = htmlspecialchars(trim($_POST['description'] ?? ''));
            $status = $_POST['status'] ?? 'available';
            
            // Vérifier si le livre existe et appartient à l'utilisateur
            $book = $this->bookModel->getBookById($bookId);
            
            if(!$book || $book['user_id'] != $_SESSION['user_id']) {
                header('Location: index.php?action=myBooks');
                return;
            }
            
            $errors = [];
            
            // Validation
            if(empty($title)) {
                $errors[] = "Le titre est requis";
            }
            
            if(empty($author)) {
                $errors[] = "L'auteur est requis";
            }
            
            // Traitement de l'image
            $image = $book['image']; // Conserver l'image existante par défaut
            
            if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['image']['name'];
                $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if(in_array($filetype, $allowed)) {
                    $newFilename = uniqid() . '.' . $filetype;
                    $uploadDir = 'uploads/';
                    
                    if(!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    if(move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newFilename)) {
                        // Supprimer l'ancienne image si elle existe
                        if(!empty($book['image']) && file_exists($book['image'])) {
                            unlink($book['image']);
                        }
                        
                        $image = $uploadDir . $newFilename;
                    } else {
                        $errors[] = "Erreur lors du téléchargement de l'image";
                    }
                } else {
                    $errors[] = "Format d'image non autorisé";
                }
            }
            
            // S'il n'y a pas d'erreurs, mettre à jour le livre
            if(empty($errors)) {
                if($this->bookModel->update($bookId, $title, $author, $description, $image, $status)) {
                    header('Location: index.php?action=myBooks');
                    return;
                } else {
                    $errors[] = "Une erreur est survenue lors de la modification du livre";
                }
            }
            
            // S'il y a des erreurs, afficher le formulaire avec les erreurs
            $book = [
                'id' => $bookId,
                'title' => $title,
                'author' => $author,
                'description' => $description,
                'image' => $image,
                'status' => $status
            ];
            
            require_once ROOT_PATH . 'views/books/edit_book.php';
        } else {
            // Si ce n'est pas une requête POST, rediriger vers le formulaire
            $this->editBookForm();
        }
    }
    
    // Supprimer un livre
    public function deleteBook() {
        // Vérifier si l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=loginForm');
            return;
        }
        
        if(!isset($_GET['id']) || empty($_GET['id'])) {
            header('Location: index.php?action=myBooks');
            return;
        }
        
        $bookId = $_GET['id'];
        $book = $this->bookModel->getBookById($bookId);
        
        if(!$book || $book['user_id'] != $_SESSION['user_id']) {
            header('Location: index.php?action=myBooks');
            return;
        }
        
        // Supprimer l'image si elle existe
        if(!empty($book['image']) && file_exists($book['image'])) {
            unlink($book['image']);
        }
        
        $this->bookModel->delete($bookId);
        header('Location: index.php?action=myBooks');
        return;
    }
    
    // Changer le statut d'un livre
    public function toggleBookStatus() {
        // Vérifier si l'utilisateur est connecté
        if(!isset($_SESSION['user_id'])) {
            header('Location: index.php?action=loginForm');
            return;
        }
        
        if(!isset($_GET['id']) || empty($_GET['id'])) {
            header('Location: index.php?action=myBooks');
            return;
        }
        
        $bookId = $_GET['id'];
        $book = $this->bookModel->getBookById($bookId);
        
        if(!$book || $book['user_id'] != $_SESSION['user_id']) {
            header('Location: index.php?action=myBooks');
            return;
        }
        
        // Inverser le statut actuel
        $newStatus = ($book['status'] == 'available') ? 'unavailable' : 'available';
        
        $this->bookModel->updateStatus($bookId, $newStatus);
        header('Location: index.php?action=myBooks');
        return;
    }
}
?> 