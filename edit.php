<?php
require_once __DIR__ . '/database/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id']);

$stmt = $pdo->prepare('SELECT * FROM students WHERE id = :id');
$stmt->execute([':id' => $id]);
$apprenant = $stmt->fetch();

if (!$apprenant) {
    header('Location: index.php');
    exit;
}

$message = '';
$photo = $apprenant['photo'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lastName = trim($_POST['lastName'] ?? '');
    $firstName = trim($_POST['firstName'] ?? '');
    $classroom = trim($_POST['classroom'] ?? '');
    $age = trim($_POST['age'] ?? '');

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions, true)) {
            $message = 'Le format de la photo est invalide.';
        } else {
            $newPhoto = uniqid('photo_', true) . '.' . $extension;
            $destination = __DIR__ . '/uploads/' . $newPhoto;

            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
                $message = 'Impossible de téléverser la photo.';
            } else {
                $photo = $newPhoto;

                if (!empty($apprenant['photo'])) {
                    $oldFile = __DIR__ . '/uploads/' . $apprenant['photo'];
                    if (file_exists($oldFile) && $apprenant['photo'] !== $newPhoto) {
                        unlink($oldFile);
                    }
                }
            }
        }
    }

    if ($message === '' && $lastName !== '' && $firstName !== '' && $classroom !== '' && $age !== '') {
        $sql = 'UPDATE students SET photo = :photo, lastName = :lastName, firstName = :firstName, classroom = :classroom, age = :age WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':photo' => $photo,
            ':lastName' => $lastName,
            ':firstName' => $firstName,
            ':classroom' => $classroom,
            ':age' => $age,
            ':id' => $id,
        ]);

        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Modifier un apprenant</title>
</head>
<body>
    <div class="form-container">
            <h1>Modifier un apprenant</h1>

            <?php if ($message !== ''): ?>
                <p class="message"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Photo actuelle</label>
                        <?php if (!empty($apprenant['photo'])): ?>
                            <img src="uploads/<?= htmlspecialchars($apprenant['photo']) ?>" alt="Photo actuelle" class="current-photo">
                        <?php else: ?>
                            <span>Pas de photo</span>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label for="photo">Nouvelle photo</label>
                        <input type="file" id="photo" name="photo" accept="image/*">
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="lastName">Nom</label>
                        <input type="text" id="lastName" name="lastName" placeholder="Nom" value="<?= htmlspecialchars($apprenant['lastName']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="firstName">Prénom</label>
                        <input type="text" id="firstName" name="firstName" placeholder="Prénom" value="<?= htmlspecialchars($apprenant['firstName']) ?>" required>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="classroom">Classe</label>
                        <input type="text" id="classroom" name="classroom" placeholder="Classe" value="<?= htmlspecialchars($apprenant['classroom']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="age">Age</label>
                        <input type="number" id="age" name="age" min="0" max="100" placeholder="Âge" value="<?= htmlspecialchars((string) $apprenant['age']) ?>" required>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                    <a href="index.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
    </div>
</body>
</html>
