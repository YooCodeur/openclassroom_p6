<?php
// Titre de la page
$pageTitle = "Mon Profil";

// En-tête de la page
require_once ROOT_PATH . 'views/partials/header.php';
?>

<div class="container">
    <h1>Mon Profil</h1>
    
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
                        <a href="index.php?action=editProfileForm" class="btn btn-primary">Modifier mon profil</a>
                    </p>
                </div>
            </div>
            
            <div class="profile-details">
                <div class="profile-section">
                    <h3>À propos de moi</h3>
                    <p>
                        <?php if(!empty($user['bio'])): ?>
                            <?php echo nl2br(htmlspecialchars($user['bio'])); ?>
                        <?php else: ?>
                            <em>Aucune information</em>
                        <?php endif; ?>
                    </p>
                </div>
                
                <div class="profile-section">
                    <h3>Contact</h3>
                    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                </div>
            </div>
        </div>
        
        <div class="profile-actions">
            <a href="index.php?action=myBooks" class="btn">Ma bibliothèque</a>
            <a href="index.php" class="btn">Retour à l'accueil</a>
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