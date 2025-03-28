<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $book['title']; ?> - TomTroc</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="book-details-page">
            <div class="book-header">
                <h1><?php echo $book['title']; ?></h1>
                <div class="actions">
                    <a href="index.php?action=allBooks" class="btn">Retour aux livres</a>
                    
                    <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $book['owner_id']): ?>
                        <a href="index.php?action=editBookForm&id=<?php echo $book['id']; ?>" class="btn btn-primary">Modifier</a>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="book-content">
                <div class="book-image-large">
                    <?php if(!empty($book['image']) && file_exists($book['image'])): ?>
                        <img src="<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>">
                    <?php else: ?>
                        <div class="no-image">Pas d'image disponible</div>
                    <?php endif; ?>
                </div>
                
                <div class="book-info">
                    <p class="info-label">Auteur:</p>
                    <p class="info-value"><?php echo $book['author']; ?></p>
                    
                    <p class="info-label">Proposé par:</p>
                    <p class="info-value">
                        <a href="index.php?action=viewProfile&id=<?php echo $book['owner_id']; ?>"><?php echo $book['owner_name']; ?></a>
                    </p>
                    
                    <p class="info-label">Statut:</p>
                    <p class="info-value">
                        <span class="status-badge <?php echo $book['status']; ?>">
                            <?php echo $book['status'] == 'available' ? 'Disponible' : 'Indisponible'; ?>
                        </span>
                    </p>
                    
                    <p class="info-label">Description:</p>
                    <div class="description">
                        <?php echo nl2br($book['description']); ?>
                    </div>
                    
                    <?php if($book['status'] == 'available' && isset($_SESSION['user_id']) && $_SESSION['user_id'] != $book['owner_id']): ?>
                        <div class="contact-owner">
                            <a href="index.php?action=sendMessageForm&receiver_id=<?php echo $book['owner_id']; ?>" class="btn btn-primary">Contacter le propriétaire</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 