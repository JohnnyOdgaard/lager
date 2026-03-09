<?php
require_once __DIR__ . "/../../includes/config.php";

$id = $_POST['id'] ?? null;

if (!$id) {
    die("Mangler ID.");
}

$stmt = $pdo->prepare("DELETE FROM lagerbeholdning WHERE id = :id");
$stmt->execute([':id' => $id]);

header("Location: ../hent/hent.php");
exit;
