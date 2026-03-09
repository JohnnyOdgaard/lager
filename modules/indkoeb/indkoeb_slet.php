<?php
require_once __DIR__ . "/../../includes/config.php";

$id = $_GET['id'] ?? 0;

$stmt = $pdo->prepare("DELETE FROM lagerbeholdning WHERE id = :id");
$stmt->execute([':id' => $id]);

header("Location: ../soeg/vareliste.php");
exit;
