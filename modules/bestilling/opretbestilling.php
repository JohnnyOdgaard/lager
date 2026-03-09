<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../../includes/config.php";

$beholdID = $_POST['beholdID'] ?? null;
$vare     = $_POST['vare'] ?? '';

if ($vare === '') {
    die("Varenavn mangler.");
}

$stmt = $pdo->prepare("
    INSERT INTO bestil (vare, bestilt, beholdID)
    VALUES (:vare, 'nej', :beholdID)
");

$stmt->execute([
    ':vare'     => $vare,
    ':beholdID' => $beholdID
]);

include __DIR__ . "/bestil_update.php";
exit;
