<?php
// Titre de la page
$pageTitle = "Profil de " . htmlspecialchars($user['username'] ?? 'Utilisateur');

// En-tête de la page
require_once ROOT_PATH . 'views/partials/header.php';
?>

<div class="container">
    <h1>Profil de <?php echo htmlspecialchars($user['username'] ?? 'Utilisateur'); ?></h1>
    
    <?php if(isset($user) && $user): ?>
        <div class="profile-container">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php if(!empty($user['profile_image'])): ?>
                        <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Avatar">
                    <?php else: ?>
                        <div class="no-avatar">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="profile-info">
                    <h2><?php echo htmlspecialchars($user['username']); ?></h2>
                    <p>
                        <?php if(!empty($user['first_name']) || !empty($user['last_name'])): ?>
                            <span class="profile-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span>
                        <?php endif; ?>
                    </p>
                    <p>
                        <a href="index.php?action=sendMessage&to=<?php echo $user['id']; ?>" class="btn btn-primary">Contacter</a>
                    </p>
                </div>
            </div>
            
            <div class="profile-details">
                <div class="profile-section">
                    <h3>À propos</h3>
                    <p>
                        <?php if(!empty($user['bio'])): ?>
                            <?php echo nl2br(htmlspecialchars($user['bio'])); ?>
                        <?php else: ?>
                            <em>Aucune information</em>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Afficher les livres disponibles de cet utilisateur -->
        <div class="user-books">
            <h2>Livres disponibles à l'échange</h2>
            
            <?php if(isset($books) && !empty($books)): ?>
                <div class="books-grid">
                    <?php foreach($books as $book): ?>
                        <div class="book-card">
                            <div class="book-cover">
                                <?php if(!empty($book['image'])): ?>
                                    <img src="<?php echo htmlspecialchars($book['image']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                                <?php else: ?>
                                    <div class="no-cover">
                                        <i class="fas fa-book"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="book-info">
                                <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                                <p>Par <?php echo htmlspecialchars($book['author']); ?></p>
                                <div class="book-actions">
                                    <a href="index.php?action=viewBook&id=<?php echo $book['id']; ?>" class="btn btn-sm">Voir détails</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Cet utilisateur n'a pas de livres disponibles à l'échange pour le moment.</p>
            <?php endif; ?>
        </div>
        
        <div class="profile-actions">
            <a href="javascript:history.back()" class="btn">Retour</a>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            Profil introuvable. <a href="index.php">Retour à l'accueil</a>
        </div>
    <?php endif; ?>
</div>

<?php
// Pied de page
require_once ROOT_PATH . 'views/partials/footer.php';
?> 