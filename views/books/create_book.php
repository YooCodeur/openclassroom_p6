<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un livre - TomTroc</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Ajouter un livre</h1>
        
        <?php if(!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form action="index.php?action=createBook" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Titre du livre :</label>
                <input type="text" id="title" name="title" value="<?php echo $title ?? ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="author">Auteur :</label>
                <input type="text" id="author" name="author" value="<?php echo $author ?? ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description :</label>
                <textarea id="description" name="description" rows="5"><?php echo $description ?? ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Image de couverture :</label>
                <input type="file" id="image" name="image">
                <small>Formats acceptés: jpg, jpeg, png, gif</small>
            </div>
            
            <div class="form-group">
                <label for="status">Statut :</label>
                <select id="status" name="status">
                    <option value="available" <?php echo (isset($status) && $status == 'available') ? 'selected' : ''; ?>>Disponible</option>
                    <option value="unavailable" <?php echo (isset($status) && $status == 'unavailable') ? 'selected' : ''; ?>>Indisponible</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Ajouter le livre</button>
                <a href="index.php?action=myBooks" class="btn">Annuler</a>
            </div>
        </form>
    </div>
</body>
</html> 