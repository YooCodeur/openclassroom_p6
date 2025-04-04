<?php
// Titre de la page
$pageTitle = "Modifier mon profil";

// En-tête de la page
require_once ROOT_PATH . 'views/partials/header.php';
?>

<div class="container">
    <h1>Modifier mon profil</h1>
    
    <?php if(isset($errors) && !empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form action="index.php?action=editProfile" method="post" enctype="multipart/form-data" class="form">
        <div class="form-group">
            <label for="username">Nom d'utilisateur *</label>
            <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="first_name">Prénom</label>
            <input type="text" id="first_name" name="first_name" class="form-control" value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="last_name">Nom</label>
            <input type="text" id="last_name" name="last_name" class="form-control" value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>">
        </div>
        
        <div class="form-group">
            <label for="bio">À propos de moi</label>
            <textarea id="bio" name="bio" class="form-control" rows="5"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
            <small class="form-text text-muted">Partagez quelques informations sur vous et vos goûts littéraires.</small>
        </div>
        
        <div class="form-group">
            <label for="avatar">Avatar</label>
            <input type="file" id="avatar" name="avatar" class="form-control-file">
            <small class="form-text text-muted">Formats acceptés: JPG, JPEG, PNG, GIF. Taille maximale: 2 Mo.</small>
            
            <?php if(!empty($user['profile_image'])): ?>
                <div class="current-avatar">
                    <p>Avatar actuel:</p>
                    <img src="<?php echo htmlspecialchars($user['profile_image']); ?>" alt="Avatar actuel" style="max-width: 100px;">
                </div>
            <?php endif; ?>
        </div>
        
        <div class="form-buttons">
            <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
            <a href="index.php?action=myProfile" class="btn">Annuler</a>
        </div>
    </form>
</div>

<?php
// Pied de page
require_once ROOT_PATH . 'views/partials/footer.php';
?> 