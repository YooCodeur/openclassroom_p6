<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un livre - TomTroc</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Modifier un livre</h1>
        
        <?php if(!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form action="index.php?action=editBook" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="book_id" value="<?php echo $book['id']; ?>">
            
            <div class="form-group">
                <label for="title">Titre du livre :</label>
                <input type="text" id="title" name="title" value="<?php echo $book['title']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="author">Auteur :</label>
                <input type="text" id="author" name="author" value="<?php echo $book['author']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description :</label>
                <textarea id="description" name="description" rows="5"><?php echo $book['description']; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Image de couverture :</label>
                <?php if(!empty($book['image']) && file_exists($book['image'])): ?>
                    <div class="current-image">
                        <img src="<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>" style="max-width: 200px;">
                        <p>Image actuelle</p>
                    </div>
                <?php endif; ?>
                <input type="file" id="image" name="image">
                <small>Formats acceptés: jpg, jpeg, png, gif. Laissez vide pour conserver l'image actuelle.</small>
            </div>
            
            <div class="form-group">
                <label for="status">Statut :</label>
                <select id="status" name="status">
                    <option value="available" <?php echo $book['status'] == 'available' ? 'selected' : ''; ?>>Disponible</option>
                    <option value="unavailable" <?php echo $book['status'] == 'unavailable' ? 'selected' : ''; ?>>Indisponible</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="index.php?action=myBooks" class="btn">Annuler</a>
            </div>
        </form>
    </div>
</body>
</html> 