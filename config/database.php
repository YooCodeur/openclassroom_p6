<?php
/**
 * Configuration et classe de connexion à la base de données
 * 
 * Ce fichier définit les constantes de connexion à la base de données
 * et implémente une classe singleton pour gérer les connexions.
 * 
 * Le pattern Singleton utilisé permet de n'avoir qu'une seule instance
 * de connexion à la base de données pour toute l'application.
 */

// Constantes de configuration de la base de données
// Ces valeurs devraient idéalement être stockées dans un fichier .env pour la production
define('DB_HOST', 'localhost');  // Hôte de la base de données
define('DB_NAME', 'tomtroc');    // Nom de la base de données
define('DB_USER', 'root');       // Nom d'utilisateur
define('DB_PASS', 'root');       // Mot de passe

/**
 * Classe Database - Implémentation du pattern Singleton pour la connexion à la base de données
 * 
 * Cette classe assure qu'une seule connexion à la base de données est établie
 * et partagée à travers toute l'application, optimisant ainsi les ressources.
 */
class Database {
    /**
     * Instance unique de la classe Database (pattern Singleton)
     * @var Database|null
     */
    private static $instance = null;
    
    /**
     * Objet de connexion PDO
     * @var PDO
     */
    private $conn;
    
    /**
     * Constructeur privé - empêche l'instanciation directe
     * Établit la connexion à la base de données via PDO
     * 
     * @throws PDOException En cas d'échec de connexion
     */
    private function __construct() {
        try {
            // Création d'une nouvelle connexion PDO avec les paramètres définis
            $this->conn = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
                DB_USER,
                DB_PASS,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION] // Configuration pour lancer des exceptions en cas d'erreur
            );
        } catch(PDOException $e) {
            // En cas d'échec, arrête l'exécution et affiche l'erreur
            // Note: En production, il serait préférable de logger l'erreur plutôt que de l'afficher
            die("Erreur de connexion : " . $e->getMessage());
        }
    }
    
    /**
     * Méthode statique pour obtenir l'instance unique de Database
     * Implémentation du pattern Singleton
     * 
     * @return Database Instance unique de la classe Database
     */
    public static function getInstance() {
        // Crée une nouvelle instance uniquement si elle n'existe pas déjà
        if(self::$instance === null) {
            self::$instance = new self();
        }
        // Retourne l'instance unique
        return self::$instance;
    }
    
    /**
     * Obtient l'objet de connexion PDO
     * 
     * @return PDO Objet de connexion à la base de données
     */
    public function getConnection() {
        return $this->conn;
    }
}
?> 