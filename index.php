<?php
require_once __DIR__ . "/database/connection.php";

$sql = "SELECT * FROM students ORDER BY id DESC";
$stmt = $pdo->query($sql);
$apprenants = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Liste des apprenants</title>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Liste des apprenants</h1>
            <a href="create.php" class="btn btn-primary">Ajouter un apprenant</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Nom de famille</th>
                        <th>Prénom</th>
                        <th>Classe</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($apprenants) > 0): ?>
                        <?php foreach ($apprenants as $apprenant): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($apprenant['photo'])): ?>
                                        <img
                                            src="uploads/<?= rawurlencode($apprenant['photo']) ?>"
                                            alt="<?= htmlspecialchars($apprenant['firstName'], ENT_QUOTES, 'UTF-8') ?>"
                                            class="photo"
                                        >
                                    <?php else: ?>
                                        <span>Pas de photo</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($apprenant['lastName']) ?></td>
                                <td><?= htmlspecialchars($apprenant['firstName']) ?></td>
                                <td><?= htmlspecialchars($apprenant['classroom']) ?></td>
                                <td class="actions">
                                    <a href="edit.php?id=<?= $apprenant['id'] ?>" class="btn btn-edit">Modifier</a>
                                    <a href="delete.php?id=<?= $apprenant['id'] ?>" class="btn btn-delete" onclick="return confirm('Voulez-vous vraiment supprimer cet apprenant ?');">Supprimer</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty">Aucun apprenant enregistré.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>