<?php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Sletning kræver en POST-forespørgsel.');
}

require_once __DIR__ . "/../../includes/config.php";
require_once __DIR__ . "/../../includes/transaction.php";

$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($id === false || $id === null) {
    http_response_code(400);
    exit("Ugyldigt ID.");
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
