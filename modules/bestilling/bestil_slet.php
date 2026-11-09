<?php
require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/transaction.php";

$id = $_GET['id'] ?? null;

if (!$id) {
    die("Mangler ID.");
}

$resultat = medTransaktion($pdo, function () use ($pdo, $id) {
// Hent beholdID
$stmt = $pdo->prepare("SELECT beholdID FROM bestil WHERE id = :id FOR UPDATE");
$stmt->execute([':id' => $id]);
$row = $stmt->fetch();

if (!$row) {
    return false;
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
return true;
});

if (!$resultat) {
    die("Bestilling findes ikke.");
}

// Tilbage til bestillingslisten
header("Location: bestil_liste.php");
exit;
