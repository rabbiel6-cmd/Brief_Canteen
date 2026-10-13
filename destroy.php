<?php
require_once __DIR__ . '/database/connection.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id = intval($_GET['id']);

$stmt = $pdo->prepare('SELECT photo FROM students WHERE id = :id');
$stmt->execute([':id' => $id]);
$apprenant = $stmt->fetch();

if ($apprenant) {
    if (!empty($apprenant['photo'])) {
        $fichier = __DIR__ . '/uploads/' . $apprenant['photo'];

        if (file_exists($fichier)) {
            unlink($fichier);
        }
    }

    $stmt = $pdo->prepare('DELETE FROM students WHERE id = :id');
    $stmt->execute([':id' => $id]);
}

header('Location: index.php');
exit;
