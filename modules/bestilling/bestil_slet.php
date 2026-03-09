<?php
require_once __DIR__ . "/../../includes/config.php";

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Mangler ID.");
}

// Hent beholdID
$stmt = $pdo->prepare("SELECT beholdID FROM bestil WHERE id = :id");
$stmt->execute([':id' => $id]);
$row = $stmt->fetch();

if (!$row) {
    die("Bestilling findes ikke.");
}

$beholdID = $row['beholdID'];

// Slet bestilling
$stmt = $pdo->prepare("DELETE FROM bestil WHERE id = :id");
$stmt->execute([':id' => $id]);

// Hvis bestillingen er knyttet til lagerbeholdning → slet den også
if ($beholdID) {
    $stmt = $pdo->prepare("DELETE FROM lagerbeholdning WHERE id = :bid");
    $stmt->execute([':bid' => $beholdID]);
}

// Tilbage til bestillingslisten
header("Location: bestil_liste.php");
exit;
