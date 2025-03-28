<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ma Bibliothèque - TomTroc</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Ma Bibliothèque</h1>
        
        <div class="actions">
            <a href="index.php?action=createBookForm" class="btn btn-primary">Ajouter un livre</a>
            <a href="index.php" class="btn">Retour à l'accueil</a>
        </div>
        
        <?php if(empty($books)): ?>
            <p class="info">Vous n'avez pas encore ajouté de livres à votre bibliothèque.</p>
        <?php else: ?>
            <div class="books-grid">
                <?php foreach($books as $book): ?>
                    <div class="book-card <?php echo $book['status'] == 'unavailable' ? 'unavailable' : ''; ?>">
                        <div class="book-image">
                            <?php if(!empty($book['image']) && file_exists($book['image'])): ?>
                                <img src="<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>">
                            <?php else: ?>
                                <div class="no-image">Pas d'image</div>
                            <?php endif; ?>
                        </div>
                        <div class="book-details">
                            <h3><?php echo $book['title']; ?></h3>
                            <p class="author">par <?php echo $book['author']; ?></p>
                            <p class="status">Statut: 
                                <span class="status-badge <?php echo $book['status']; ?>">
                                    <?php echo $book['status'] == 'available' ? 'Disponible' : 'Indisponible'; ?>
                                </span>
                            </p>
                            <div class="book-actions">
                                <a href="index.php?action=viewBook&id=<?php echo $book['id']; ?>" class="btn btn-small">Détails</a>
                                <a href="index.php?action=editBookForm&id=<?php echo $book['id']; ?>" class="btn btn-small">Modifier</a>
                                <a href="index.php?action=toggleBookStatus&id=<?php echo $book['id']; ?>" class="btn btn-small">
                                    <?php echo $book['status'] == 'available' ? 'Rendre indisponible' : 'Rendre disponible'; ?>
                                </a>
                                <a href="index.php?action=deleteBook&id=<?php echo $book['id']; ?>" class="btn btn-small btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ?')">Supprimer</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 