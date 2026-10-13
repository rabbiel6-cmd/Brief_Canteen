<?php
require_once 'database/connection.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $classe = trim($_POST['classe'] ?? '');
    $age = intval($_POST['age'] ?? 0);

    $photo = null;

    if (!empty($nom) && !empty($prenom) && !empty($classe) && !empty($age)) {
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
                $message = 'Le téléversement de la photo a échoué.';
            } else {
                $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                $extensionsAutorisees = ['jpg', 'jpeg', 'png', 'web'];

                if (!in_array($extension, $extensionsAutorisees, true)) {
                    $message = 'Le format de la photo doit être JPG, PNG ou Web.';
                } else {
                    $nomPhoto = uniqid('apprenant_', true) . '.' . $extension;
                    $destination = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $nomPhoto;

                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
                        $photo = $nomPhoto;
                    } else {
                        $message = 'Impossible d’enregistrer la photo. Vérifiez le dossier uploads.';
                    }
                }
            }
        }

        if ($message === '') {
            $req = $pdo->prepare('
                INSERT INTO students
                    (photo, lastname, firstname, classroom, age)
                VALUES
                    (:photo, :lastname, :firstname, :classroom, :age)
            ');

            $req->execute([
                'photo' => $photo,
                'lastname' => $nom,
                'firstname' => $prenom,
                'classroom' => $classe,
                'age' => $age
            ]);

            header('Location: index.php');
            exit;
        }
    } else {
        $message = 'Veuillez remplir correctement tous les champs';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un apprenant</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="form-container">
        <h1>Ajouter un apprenant</h1>

        <?php if (!empty($message)): ?>
            <p style="color: red; margin: 15px 0;">
                <?= htmlspecialchars($message) ?>
            </p>
        <?php endif; ?>

        <form
            action=""
            method="POST"
            enctype="multipart/form-data"
        >
            <div class="form-group">
                <label for="photo">Photo</label>

                <input
                    type="file"
                    id="photo"
                    name="photo"
                    accept="image/jpeg, image/png, image/web"
                >
            </div>

            <div class="form-group">
                <label for="nom">Nom de famille</label>

                <input
                    type="text"
                    id="nom"
                    name="nom"
                    required
                >
            </div>

            <div class="form-group">
                <label for="prenom">Prénom</label>

                <input
                    type="text"
                    id="prenom"
                    name="prenom"
                    required
                >
            </div>

            <div class="form-group">
                <label for="classe">Classe</label>

                <input
                    type="text"
                    id="classe"
                    name="classe"
                    placeholder="Ex : Term A"
                    required
                >
            </div>

            <div class="form-group">
                <label for="age">Age</label>

                <input
                    type="number"
                    id="age"
                    name="age"
                    min="1"
                    max="100"
                    required
                >
            </div>

            <div class="form-actions">
                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Enregistrer
                </button>

                <a
                    href="index.php"
                    class="btn btn-secondary"
                >
                    Annuler
                </a>
            </div>
        </form>
    </div>
</body>
</html>