<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livres disponibles - TomTroc</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Livres disponibles à l'échange</h1>
        
        <div class="actions">
            <a href="index.php" class="btn">Retour à l'accueil</a>
        </div>
        
        <div class="search-form">
            <form action="index.php" method="GET">
                <input type="hidden" name="action" value="allBooks">
                <input type="text" name="search" placeholder="Rechercher un livre par titre" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </form>
        </div>
        
        <?php if(empty($books)): ?>
            <p class="info">Aucun livre disponible pour le moment.</p>
        <?php else: ?>
            <div class="books-grid">
                <?php foreach($books as $book): ?>
                    <div class="book-card">
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
                            <p class="owner">Proposé par: <?php echo $book['owner_name']; ?></p>
                            <div class="book-actions">
                                <a href="index.php?action=viewBook&id=<?php echo $book['id']; ?>" class="btn btn-small">Voir détails</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 